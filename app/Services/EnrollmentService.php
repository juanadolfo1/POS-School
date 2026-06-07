<?php

namespace App\Services;

use App\Models\EnrollmentProcess;
use App\Models\GradePromotion;
use App\Models\Group;
use App\Models\StudentGroup;
use App\Models\CustomException;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    private WithdrawalService $withdrawalService;

    public function __construct(WithdrawalService $withdrawalService)
    {
        $this->withdrawalService = $withdrawalService;
    }

    /**
     * Ejecuta la reinscripción masiva de un nivel académico.
     *
     * Flujo:
     * 1. Obtiene todos los alumnos activos del ciclo anterior en ese nivel
     * 2. Excluye los que tienen baja definitiva
     * 3. Para cada alumno, busca su promoción de grado (4A→5A)
     * 4. Si es grado final (6° primaria, 3° secundaria) → lo marca como egresado
     * 5. Si no, crea el nuevo student_group en el ciclo destino
     *
     * @return array Resumen del proceso
     */
    public function promoteBatch(int $fromScholarYearId, int $toScholarYearId, int $academicLevelId): array
    {
        if ($fromScholarYearId === $toScholarYearId) {
            throw new CustomException('El ciclo origen y destino no pueden ser el mismo', 422);
        }

        DB::beginTransaction();
        try {
            $result = $this->executePromoteBatch($fromScholarYearId, $toScholarYearId, $academicLevelId);
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function executePromoteBatch(int $fromScholarYearId, int $toScholarYearId, int $academicLevelId): array
    {

        // Validar que no se haya ejecutado ya este proceso
        $existingProcess = EnrollmentProcess::where([
            ['from_scholar_year_id', '=', $fromScholarYearId],
            ['to_scholar_year_id', '=', $toScholarYearId],
            ['academic_level_id', '=', $academicLevelId],
        ])->first();

        if ($existingProcess) {
            throw new CustomException('Este proceso de reinscripción ya fue ejecutado', 409);
        }

        // Obtener alumnos activos del ciclo anterior en ese nivel
        $activeStudents = StudentGroup::join('groups', 'groups.id', '=', 'student_groups.group_id')
            ->where([
                ['groups.scholar_year_id', '=', $fromScholarYearId],
                ['groups.academic_level_id', '=', $academicLevelId],
                ['student_groups.status', '=', 1],
            ])
            ->select(
                'student_groups.student_id',
                'groups.label as current_grade',
                'groups.academic_level_id'
            )
            ->get();

        // Obtener configuración de promoción de grados para este nivel
        $promotions = GradePromotion::where('academic_level_id', $academicLevelId)->get();

        $promoted = 0;
        $graduated = 0;
        $excluded = 0;

        foreach ($activeStudents as $studentData) {
            // Excluir bajas definitivas
            if ($this->withdrawalService->hasDefinitiveWithdrawal($studentData->student_id)) {
                $excluded++;
                continue;
            }

            // Buscar la regla de promoción para su grado actual
            $promotion = $promotions->first(function ($p) use ($studentData) {
                return strtoupper($p->from_grade) === strtoupper($studentData->current_grade);
            });

            if (!$promotion) {
                // Sin regla de promoción configurada, no se puede procesar
                $excluded++;
                continue;
            }

            if ($promotion->is_final_grade) {
                // Alumno egresa del nivel
                $graduated++;
                // Desactivar su student_group actual
                StudentGroup::join('groups', 'groups.id', '=', 'student_groups.group_id')
                    ->where([
                        ['student_groups.student_id', '=', $studentData->student_id],
                        ['groups.scholar_year_id', '=', $fromScholarYearId],
                    ])
                    ->update(['student_groups.status' => 0]);
                continue;
            }

            // Buscar o crear el grupo destino en el nuevo ciclo
            $targetGroup = Group::firstOrCreate(
                [
                    'label' => $promotion->to_grade,
                    'scholar_year_id' => $toScholarYearId,
                    'academic_level_id' => $academicLevelId,
                ],
                ['status' => 1]
            );

            // Verificar que no esté ya inscrito en el nuevo ciclo
            $alreadyEnrolled = StudentGroup::where([
                ['student_id', '=', $studentData->student_id],
                ['group_id', '=', $targetGroup->id],
            ])->exists();

            if (!$alreadyEnrolled) {
                StudentGroup::create([
                    'student_id' => $studentData->student_id,
                    'group_id' => $targetGroup->id,
                    'status' => 1,
                ]);
                $promoted++;
            }
        }

        $process = EnrollmentProcess::create([
            'from_scholar_year_id' => $fromScholarYearId,
            'to_scholar_year_id' => $toScholarYearId,
            'academic_level_id' => $academicLevelId,
            'students_promoted' => $promoted,
            'students_graduated' => $graduated,
            'students_excluded' => $excluded,
        ]);

        return [
            'process_id' => $process->id,
            'promoted' => $promoted,
            'graduated' => $graduated,
            'excluded' => $excluded,
            'total_processed' => $activeStudents->count(),
        ];
    }

    /**
     * Reinscripción individual: promueve a un solo alumno al nuevo ciclo.
     */
    public function promoteStudent(int $studentId, int $fromScholarYearId, int $toScholarYearId, int $academicLevelId): array
    {
        if ($this->withdrawalService->hasDefinitiveWithdrawal($studentId)) {
            throw new CustomException('El alumno tiene baja definitiva y no puede ser reinscrito', 422);
        }

        // Obtener grupo actual del alumno
        $currentGroup = StudentGroup::join('groups', 'groups.id', '=', 'student_groups.group_id')
            ->where([
                ['student_groups.student_id', '=', $studentId],
                ['groups.scholar_year_id', '=', $fromScholarYearId],
                ['groups.academic_level_id', '=', $academicLevelId],
                ['student_groups.status', '=', 1],
            ])
            ->select('groups.label as current_grade')
            ->first();

        if (!$currentGroup) {
            throw new CustomException('El alumno no tiene grupo activo en el ciclo origen', 404);
        }

        // Buscar regla de promoción
        $promotion = GradePromotion::where([
            ['academic_level_id', '=', $academicLevelId],
        ])->whereRaw('UPPER(from_grade) = ?', [strtoupper($currentGroup->current_grade)])
          ->first();

        if (!$promotion) {
            throw new CustomException('No hay regla de promoción configurada para el grado ' . $currentGroup->current_grade, 422);
        }

        if ($promotion->is_final_grade) {
            return [
                'status' => 'graduated',
                'message' => 'El alumno egresa del nivel académico',
            ];
        }

        // Buscar o crear grupo destino
        $targetGroup = Group::firstOrCreate(
            [
                'label' => $promotion->to_grade,
                'scholar_year_id' => $toScholarYearId,
                'academic_level_id' => $academicLevelId,
            ],
            ['status' => 1]
        );

        // Verificar que no esté ya inscrito
        $alreadyEnrolled = StudentGroup::where([
            ['student_id', '=', $studentId],
            ['group_id', '=', $targetGroup->id],
        ])->exists();

        if ($alreadyEnrolled) {
            throw new CustomException('El alumno ya está inscrito en el ciclo destino', 409);
        }

        StudentGroup::create([
            'student_id' => $studentId,
            'group_id' => $targetGroup->id,
            'status' => 1,
        ]);

        return [
            'status' => 'promoted',
            'from_grade' => $currentGroup->current_grade,
            'to_grade' => $promotion->to_grade,
            'new_group_id' => $targetGroup->id,
        ];
    }

    /**
     * Vista previa: muestra qué pasaría si se ejecuta la reinscripción masiva.
     * No modifica nada en la BD.
     */
    public function previewBatch(int $fromScholarYearId, int $toScholarYearId, int $academicLevelId): array
    {
        $activeStudents = StudentGroup::join('groups', 'groups.id', '=', 'student_groups.group_id')
            ->join('students', 'students.id', '=', 'student_groups.student_id')
            ->join('people', 'people.id', '=', 'students.person_id')
            ->where([
                ['groups.scholar_year_id', '=', $fromScholarYearId],
                ['groups.academic_level_id', '=', $academicLevelId],
                ['student_groups.status', '=', 1],
            ])
            ->select(
                'student_groups.student_id',
                'groups.label as current_grade',
                DB::raw("CONCAT_WS(' ', people.name, people.first_lastname, people.second_lastname) as student_name"),
                'students.curp'
            )
            ->get();

        $promotions = GradePromotion::where('academic_level_id', $academicLevelId)->get();

        $preview = [
            'to_promote' => [],
            'to_graduate' => [],
            'excluded' => [],
            'no_rule' => [],
        ];

        foreach ($activeStudents as $student) {
            if ($this->withdrawalService->hasDefinitiveWithdrawal($student->student_id)) {
                $preview['excluded'][] = [
                    'student_id' => $student->student_id,
                    'name' => $student->student_name,
                    'curp' => $student->curp,
                    'reason' => 'Baja definitiva',
                ];
                continue;
            }

            $promotion = $promotions->first(function ($p) use ($student) {
                return strtoupper($p->from_grade) === strtoupper($student->current_grade);
            });

            if (!$promotion) {
                $preview['no_rule'][] = [
                    'student_id' => $student->student_id,
                    'name' => $student->student_name,
                    'current_grade' => $student->current_grade,
                ];
                continue;
            }

            if ($promotion->is_final_grade) {
                $preview['to_graduate'][] = [
                    'student_id' => $student->student_id,
                    'name' => $student->student_name,
                    'current_grade' => $student->current_grade,
                ];
            } else {
                $preview['to_promote'][] = [
                    'student_id' => $student->student_id,
                    'name' => $student->student_name,
                    'from_grade' => $student->current_grade,
                    'to_grade' => $promotion->to_grade,
                ];
            }
        }

        $preview['summary'] = [
            'total' => $activeStudents->count(),
            'to_promote' => count($preview['to_promote']),
            'to_graduate' => count($preview['to_graduate']),
            'excluded' => count($preview['excluded']),
            'no_rule' => count($preview['no_rule']),
        ];

        return $preview;
    }

    /**
     * Obtiene el historial de procesos de reinscripción.
     */
    public function getProcessHistory()
    {
        return EnrollmentProcess::with(['fromScholarYear', 'toScholarYear', 'academicLevel'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
