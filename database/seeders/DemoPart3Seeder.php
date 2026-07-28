<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoPart3Seeder extends Seeder
{
    public function run(): void
    {
        // ===== BECAS (20 alumnos) =====
        // Resetear secuencia para evitar conflicto con seeder base
        DB::statement("SELECT setval('scholarships_id_seq', (SELECT MAX(id) FROM scholarships))");

        $becaStudents = range(100, 119);
        $becaTypes = [
            ['name' => 'Beca Excelencia',    'amount' => 15.00],
            ['name' => 'Beca Hermano',       'amount' => 10.00],
            ['name' => 'Beca Trabajador',    'amount' => 20.00],
            ['name' => 'Beca Necesidad',     'amount' => 25.00],
        ];

        $scholarships = [];
        foreach ($becaStudents as $idx => $studentId) {
            $beca = $becaTypes[$idx % 4];
            $scholarships[] = [
                'status'         => 1,
                'name'           => $beca['name'],
                'amount'         => $beca['amount'],
                'student_id'     => $studentId,
                'scholar_year_id'=> 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }
        DB::table('scholarships')->insert($scholarships);

        // ===== BAJAS TEMPORALES (5 alumnos) =====
        $temporalStudents = [120, 121, 122, 123, 124];
        $reasons = [
            'Cambio de domicilio temporal',
            'Enfermedad prolongada',
            'Viaje familiar',
            'Situación económica temporal',
            'Trámites de documentación',
        ];

        $withdrawals = [];
        foreach ($temporalStudents as $idx => $studentId) {
            // Desactivar su student_group
            DB::table('student_groups')
                ->where('student_id', $studentId)
                ->update(['status' => 0]);

            $withdrawals[] = [
                'student_id'     => $studentId,
                'scholar_year_id'=> 1,
                'type'           => 'temporal',
                'reason'         => $reasons[$idx],
                'effective_date' => '2026-03-01',
                'reactivated_at' => null,
                'status'         => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        // ===== BAJAS DEFINITIVAS (3 alumnos) =====
        $definitiveStudents = [125, 126, 127];
        $definitiveReasons = [
            'Cambio de escuela',
            'Traslado a otra ciudad',
            'Decisión familiar',
        ];

        foreach ($definitiveStudents as $idx => $studentId) {
            DB::table('student_groups')
                ->where('student_id', $studentId)
                ->update(['status' => 0]);

            $withdrawals[] = [
                'student_id'     => $studentId,
                'scholar_year_id'=> 1,
                'type'           => 'definitiva',
                'reason'         => $definitiveReasons[$idx],
                'effective_date' => '2026-02-15',
                'reactivated_at' => null,
                'status'         => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        DB::table('student_withdrawals')->insert($withdrawals);

        // ===== REINSCRIPCIÓN REGISTRADA (proceso ejecutado) =====
        // Ciclo destino 2026-2027
        DB::table('scholar_years')->insert([
            'id'         => 2,
            'status'     => 0, // inactivo, es el próximo ciclo
            'year'       => '2026-2027',
            'starts_at'  => '2026-08-01',
            'ends_at'    => '2027-07-31',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('enrollment_processes')->insert([
            [
                'from_scholar_year_id' => 1,
                'to_scholar_year_id'   => 2,
                'academic_level_id'    => 1,
                'students_promoted'    => 75,
                'students_graduated'   => 3,
                'students_excluded'    => 2,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'from_scholar_year_id' => 1,
                'to_scholar_year_id'   => 2,
                'academic_level_id'    => 2,
                'students_promoted'    => 92,
                'students_graduated'   => 5,
                'students_excluded'    => 3,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'from_scholar_year_id' => 1,
                'to_scholar_year_id'   => 2,
                'academic_level_id'    => 3,
                'students_promoted'    => 62,
                'students_graduated'   => 5,
                'students_excluded'    => 3,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
        ]);
    }
}
