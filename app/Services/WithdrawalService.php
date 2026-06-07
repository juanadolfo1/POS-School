<?php

namespace App\Services;

use App\Models\StudentWithdrawal;
use App\Models\StudentGroup;
use App\Models\CustomException;
use Illuminate\Support\Facades\DB;

class WithdrawalService
{
    /**
     * Registra la baja de un alumno.
     * - Desactiva su student_group del ciclo activo
     * - Registra el motivo y tipo de baja
     */
    public function withdraw(int $studentId, int $scholarYearId, string $type, string $reason, string $effectiveDate): StudentWithdrawal
    {
        // Verificar que no tenga ya una baja activa en este ciclo
        $existingWithdrawal = StudentWithdrawal::where([
            ['student_id', '=', $studentId],
            ['scholar_year_id', '=', $scholarYearId],
            ['status', '=', 1],
        ])->first();

        if ($existingWithdrawal) {
            throw new CustomException('El alumno ya tiene una baja activa en este ciclo escolar', 409);
        }

        DB::beginTransaction();
        try {
            // Desactivar student_groups del ciclo actual
            $affectedGroups = StudentGroup::join('groups', 'groups.id', '=', 'student_groups.group_id')
                ->where([
                    ['student_groups.student_id', '=', $studentId],
                    ['groups.scholar_year_id', '=', $scholarYearId],
                    ['student_groups.status', '=', 1],
                ])
                ->select('student_groups.id')
                ->get();

            StudentGroup::whereIn('id', $affectedGroups->pluck('id'))->update(['status' => 0]);

            $withdrawal = StudentWithdrawal::create([
                'student_id' => $studentId,
                'scholar_year_id' => $scholarYearId,
                'type' => $type,
                'reason' => $reason,
                'effective_date' => $effectiveDate,
                'status' => 1,
            ]);

            DB::commit();
            return $withdrawal;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reactiva un alumno con baja temporal.
     * - Reactiva su student_group
     * - Marca la baja como reactivada
     */
    public function reactivate(int $withdrawalId): StudentWithdrawal
    {
        $withdrawal = StudentWithdrawal::findOrFail($withdrawalId);

        if ($withdrawal->type === 'definitiva') {
            throw new CustomException('No se puede reactivar una baja definitiva', 422);
        }

        if ($withdrawal->status === 0) {
            throw new CustomException('Esta baja ya fue reactivada', 422);
        }

        // Reactivar student_groups del ciclo
        $groupIds = StudentGroup::join('groups', 'groups.id', '=', 'student_groups.group_id')
            ->where([
                ['student_groups.student_id', '=', $withdrawal->student_id],
                ['groups.scholar_year_id', '=', $withdrawal->scholar_year_id],
                ['student_groups.status', '=', 0],
            ])
            ->select('student_groups.id')
            ->get();

        StudentGroup::whereIn('id', $groupIds->pluck('id'))->update(['status' => 1]);

        // Marcar baja como reactivada
        $withdrawal->update([
            'status' => 0,
            'reactivated_at' => now()->toDateString(),
        ]);

        return $withdrawal->fresh();
    }

    /**
     * Obtiene las bajas de un ciclo escolar (para el reporte).
     */
    public function getWithdrawals(?int $scholarYearId = null)
    {
        $query = StudentWithdrawal::join('students', 'students.id', '=', 'student_withdrawals.student_id')
            ->join('people', 'people.id', '=', 'students.person_id')
            ->join('scholar_years', 'scholar_years.id', '=', 'student_withdrawals.scholar_year_id')
            ->select(
                'student_withdrawals.id',
                'student_withdrawals.type',
                'student_withdrawals.reason',
                'student_withdrawals.effective_date',
                'student_withdrawals.reactivated_at',
                'student_withdrawals.status',
                'student_withdrawals.student_id',
                'scholar_years.year as scholar_year',
                DB::raw("CONCAT_WS(' ', people.name, people.first_lastname, people.second_lastname) as student_name"),
                'students.curp'
            )
            ->orderBy('student_withdrawals.created_at', 'desc');

        if ($scholarYearId) {
            $query->where('student_withdrawals.scholar_year_id', $scholarYearId);
        }

        return $query->get();
    }

    /**
     * Verifica si un alumno tiene baja definitiva activa (útil para reinscripción).
     */
    public function hasDefinitiveWithdrawal(int $studentId): bool
    {
        return StudentWithdrawal::where([
            ['student_id', '=', $studentId],
            ['type', '=', 'definitiva'],
            ['status', '=', 1],
        ])->exists();
    }
}
