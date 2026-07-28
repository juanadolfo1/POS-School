<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoPart1Seeder extends Seeder
{
    public function run(): void
    {
        // ===== CONCEPTOS DE PAGO PREESCOLAR (nivel 1) =====
        DB::table('cat_pay_concepts')->insert([
            ['id' => 11, 'status' => 1, 'label' => 'Inscripción Preescolar',       'amount' => 2000.00, 'discount_amount' => 1800.00, 'last_day_with_discount' => '2025-07-15', 'pay_concept_type' => 'service', 'scholar_year_id' => 1, 'id_cat_academic_level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'status' => 1, 'label' => 'Colegiatura Agosto',           'amount' => 1800.00, 'discount_amount' => 1600.00, 'last_day_with_discount' => '2025-08-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'status' => 1, 'label' => 'Colegiatura Septiembre',       'amount' => 1800.00, 'discount_amount' => 1600.00, 'last_day_with_discount' => '2025-09-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'status' => 1, 'label' => 'Colegiatura Octubre',          'amount' => 1800.00, 'discount_amount' => 1600.00, 'last_day_with_discount' => '2025-10-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'status' => 1, 'label' => 'Colegiatura Noviembre',        'amount' => 1800.00, 'discount_amount' => 1600.00, 'last_day_with_discount' => '2025-11-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'status' => 1, 'label' => 'Colegiatura Diciembre',        'amount' => 1800.00, 'discount_amount' => 1600.00, 'last_day_with_discount' => '2025-12-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'status' => 1, 'label' => 'Colegiatura Enero',            'amount' => 1800.00, 'discount_amount' => 1600.00, 'last_day_with_discount' => '2026-01-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 18, 'status' => 1, 'label' => 'Colegiatura Febrero',          'amount' => 1800.00, 'discount_amount' => 1600.00, 'last_day_with_discount' => '2026-02-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'status' => 1, 'label' => 'Colegiatura Marzo',            'amount' => 1800.00, 'discount_amount' => 1600.00, 'last_day_with_discount' => '2026-03-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'status' => 1, 'label' => 'Colegiatura Abril',            'amount' => 1800.00, 'discount_amount' => 1600.00, 'last_day_with_discount' => '2026-04-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 21, 'status' => 1, 'label' => 'Colegiatura Mayo',             'amount' => 1800.00, 'discount_amount' => 1600.00, 'last_day_with_discount' => '2026-05-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ===== CONCEPTOS DE PAGO SECUNDARIA (nivel 3) =====
        DB::table('cat_pay_concepts')->insert([
            ['id' => 31, 'status' => 1, 'label' => 'Inscripción Secundaria',       'amount' => 3500.00, 'discount_amount' => 3200.00, 'last_day_with_discount' => '2025-07-15', 'pay_concept_type' => 'service', 'scholar_year_id' => 1, 'id_cat_academic_level' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 32, 'status' => 1, 'label' => 'Colegiatura Agosto',           'amount' => 3000.00, 'discount_amount' => 2700.00, 'last_day_with_discount' => '2025-08-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 33, 'status' => 1, 'label' => 'Colegiatura Septiembre',       'amount' => 3000.00, 'discount_amount' => 2700.00, 'last_day_with_discount' => '2025-09-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 34, 'status' => 1, 'label' => 'Colegiatura Octubre',          'amount' => 3000.00, 'discount_amount' => 2700.00, 'last_day_with_discount' => '2025-10-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 35, 'status' => 1, 'label' => 'Colegiatura Noviembre',        'amount' => 3000.00, 'discount_amount' => 2700.00, 'last_day_with_discount' => '2025-11-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 36, 'status' => 1, 'label' => 'Colegiatura Diciembre',        'amount' => 3000.00, 'discount_amount' => 2700.00, 'last_day_with_discount' => '2025-12-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 37, 'status' => 1, 'label' => 'Colegiatura Enero',            'amount' => 3000.00, 'discount_amount' => 2700.00, 'last_day_with_discount' => '2026-01-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 38, 'status' => 1, 'label' => 'Colegiatura Febrero',          'amount' => 3000.00, 'discount_amount' => 2700.00, 'last_day_with_discount' => '2026-02-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 39, 'status' => 1, 'label' => 'Colegiatura Marzo',            'amount' => 3000.00, 'discount_amount' => 2700.00, 'last_day_with_discount' => '2026-03-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 40, 'status' => 1, 'label' => 'Colegiatura Abril',            'amount' => 3000.00, 'discount_amount' => 2700.00, 'last_day_with_discount' => '2026-04-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 41, 'status' => 1, 'label' => 'Colegiatura Mayo',             'amount' => 3000.00, 'discount_amount' => 2700.00, 'last_day_with_discount' => '2026-05-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ===== GRUPOS =====
        // Preescolar: 1A, 1B, 2A, 2B, 3A (nivel 1) — id 3 ya existe del DailyIncomeSeeder, empezamos en 10
        DB::table('groups')->insert([
            ['id' => 11, 'status' => 1, 'label' => '1B', 'scholar_year_id' => 1, 'academic_level_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'status' => 1, 'label' => '2A', 'scholar_year_id' => 1, 'academic_level_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'status' => 1, 'label' => '2B', 'scholar_year_id' => 1, 'academic_level_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'status' => 1, 'label' => '3A', 'scholar_year_id' => 1, 'academic_level_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
        // Actualizar el grupo 3 (1A Preescolar) que ya existe del DailyIncomeSeeder
        DB::table('groups')->where('id', 3)->update(['label' => '1A', 'academic_level_id' => 1]);
        // Primaria: 1A,2A,3A,4A,5A,6A (nivel 2) — ya existen 1,2 del seeder base, agregamos más
        DB::table('groups')->insert([
            ['id' => 20, 'status' => 1, 'label' => '1A', 'scholar_year_id' => 1, 'academic_level_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 21, 'status' => 1, 'label' => '2A', 'scholar_year_id' => 1, 'academic_level_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 22, 'status' => 1, 'label' => '3A', 'scholar_year_id' => 1, 'academic_level_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 23, 'status' => 1, 'label' => '5A', 'scholar_year_id' => 1, 'academic_level_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 24, 'status' => 1, 'label' => '6A', 'scholar_year_id' => 1, 'academic_level_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
        // Secundaria: 1A,1B,2A,2B,3A (nivel 3) — id 4 ya existe del DailyIncomeSeeder
        DB::table('groups')->insert([
            ['id' => 31, 'status' => 1, 'label' => '1B', 'scholar_year_id' => 1, 'academic_level_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 32, 'status' => 1, 'label' => '2A', 'scholar_year_id' => 1, 'academic_level_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 33, 'status' => 1, 'label' => '2B', 'scholar_year_id' => 1, 'academic_level_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 34, 'status' => 1, 'label' => '3A', 'scholar_year_id' => 1, 'academic_level_id' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);
        // Actualizar el grupo 4 (1A Secundaria) que ya existe del DailyIncomeSeeder
        DB::table('groups')->where('id', 4)->update(['label' => '1A', 'academic_level_id' => 3]);

        // ===== PROMOTION CONFIGS =====
        DB::table('promotion_configs')->insert([
            ['id' => 1, 'default_day' => 10, 'academic_level_id' => 1, 'scholar_year_id' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'default_day' => 10, 'academic_level_id' => 2, 'scholar_year_id' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'default_day' => 10, 'academic_level_id' => 3, 'scholar_year_id' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ===== GRADE PROMOTIONS =====
        DB::table('grade_promotions')->insert([
            // Preescolar
            ['academic_level_id' => 1, 'from_grade' => '1A', 'to_grade' => '2A', 'is_final_grade' => false, 'created_at' => now(), 'updated_at' => now()],
            ['academic_level_id' => 1, 'from_grade' => '1B', 'to_grade' => '2B', 'is_final_grade' => false, 'created_at' => now(), 'updated_at' => now()],
            ['academic_level_id' => 1, 'from_grade' => '2A', 'to_grade' => '3A', 'is_final_grade' => false, 'created_at' => now(), 'updated_at' => now()],
            ['academic_level_id' => 1, 'from_grade' => '2B', 'to_grade' => '3A', 'is_final_grade' => false, 'created_at' => now(), 'updated_at' => now()],
            ['academic_level_id' => 1, 'from_grade' => '3A', 'to_grade' => '3A', 'is_final_grade' => true,  'created_at' => now(), 'updated_at' => now()],
            // Primaria
            ['academic_level_id' => 2, 'from_grade' => '1A', 'to_grade' => '2A', 'is_final_grade' => false, 'created_at' => now(), 'updated_at' => now()],
            ['academic_level_id' => 2, 'from_grade' => '2A', 'to_grade' => '3A', 'is_final_grade' => false, 'created_at' => now(), 'updated_at' => now()],
            ['academic_level_id' => 2, 'from_grade' => '3A', 'to_grade' => '4A', 'is_final_grade' => false, 'created_at' => now(), 'updated_at' => now()],
            ['academic_level_id' => 2, 'from_grade' => '4A', 'to_grade' => '5A', 'is_final_grade' => false, 'created_at' => now(), 'updated_at' => now()],
            ['academic_level_id' => 2, 'from_grade' => '4B', 'to_grade' => '5A', 'is_final_grade' => false, 'created_at' => now(), 'updated_at' => now()],
            ['academic_level_id' => 2, 'from_grade' => '5A', 'to_grade' => '6A', 'is_final_grade' => false, 'created_at' => now(), 'updated_at' => now()],
            ['academic_level_id' => 2, 'from_grade' => '6A', 'to_grade' => '6A', 'is_final_grade' => true,  'created_at' => now(), 'updated_at' => now()],
            // Secundaria
            ['academic_level_id' => 3, 'from_grade' => '1A', 'to_grade' => '2A', 'is_final_grade' => false, 'created_at' => now(), 'updated_at' => now()],
            ['academic_level_id' => 3, 'from_grade' => '1B', 'to_grade' => '2B', 'is_final_grade' => false, 'created_at' => now(), 'updated_at' => now()],
            ['academic_level_id' => 3, 'from_grade' => '2A', 'to_grade' => '3A', 'is_final_grade' => false, 'created_at' => now(), 'updated_at' => now()],
            ['academic_level_id' => 3, 'from_grade' => '2B', 'to_grade' => '3A', 'is_final_grade' => false, 'created_at' => now(), 'updated_at' => now()],
            ['academic_level_id' => 3, 'from_grade' => '3A', 'to_grade' => '3A', 'is_final_grade' => true,  'created_at' => now(), 'updated_at' => now()],
        ]);

        // ===== PEOPLE + STUDENTS + TUTORS (250 alumnos) =====
        $nombres   = ['Sofia','Valentina','Camila','Isabella','Valeria','Lucia','Daniela','Fernanda','Mariana','Gabriela','Alejandra','Andrea','Paola','Diana','Laura','Karen','Monica','Patricia','Claudia','Veronica','Santiago','Mateo','Sebastian','Nicolas','Alejandro','Diego','Andres','Carlos','Luis','Jorge','Miguel','Roberto','Eduardo','Fernando','Ricardo','Arturo','Hector','Raul','Ernesto','Gustavo'];
        $apellidosP = ['Garcia','Martinez','Lopez','Gonzalez','Hernandez','Perez','Sanchez','Ramirez','Torres','Flores','Rivera','Gomez','Diaz','Cruz','Reyes','Morales','Ortiz','Gutierrez','Chavez','Ramos'];
        $apellidosM = ['Luna','Vega','Mendoza','Castillo','Jimenez','Vargas','Rojas','Medina','Aguilar','Herrera','Nunez','Ruiz','Mora','Delgado','Fuentes','Rios','Guerrero','Salinas','Pena','Lara'];

        $peopleRows   = [];
        $studentRows  = [];
        $tutorRows    = [];
        $sgRows       = [];

        // Grupos por nivel con sus IDs
        $groupsByLevel = [
            1 => [3, 11, 12, 13, 14],        // Preescolar
            2 => [1, 2, 20, 21, 22, 23, 24], // Primaria
            3 => [4, 31, 32, 33, 34],         // Secundaria
        ];

        // Distribución: 80 preescolar, 100 primaria, 70 secundaria
        $distribution = [];
        for ($i = 1; $i <= 80;  $i++) $distribution[] = 1;
        for ($i = 1; $i <= 100; $i++) $distribution[] = 2;
        for ($i = 1; $i <= 70;  $i++) $distribution[] = 3;

        $personId  = 100; // empieza en 100 para no chocar con seeder base
        $studentId = 100;
        $sgId      = 100;
        $folioBase = 1000;

        $genderCycle = ['M','F','M','F','F','M'];

        foreach ($distribution as $idx => $levelId) {
            $nombre    = $nombres[array_rand($nombres)];
            $apP       = $apellidosP[array_rand($apellidosP)];
            $apM       = $apellidosM[array_rand($apellidosM)];
            $gender    = $genderCycle[$idx % 6];
            $year      = rand(2010, 2018);
            $birthday  = $year . '-' . str_pad(rand(1,12),2,'0',STR_PAD_LEFT) . '-' . str_pad(rand(1,28),2,'0',STR_PAD_LEFT);
            $curpBase  = strtoupper(substr($apP,0,2) . substr($apM,0,1) . substr($nombre,0,1)) . substr($birthday,2,2) . substr($birthday,5,2) . substr($birthday,8,2) . 'H' . 'DF' . strtoupper(substr($apP,1,2) . substr($nombre,1,1)) . str_pad($studentId,2,'0',STR_PAD_LEFT);
            $curp      = preg_replace('/[^A-Z0-9]/', 'X', $curpBase);

            // Persona alumno
            $peopleRows[] = [
                'id'             => $personId,
                'status'         => 1,
                'email'          => 'alumno' . $studentId . '@demo.com',
                'name'           => $nombre,
                'first_lastname' => $apP,
                'second_lastname'=> $apM,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];

            // Persona tutor
            $tutorPersonId = $personId + 5000;
            $peopleRows[] = [
                'id'             => $tutorPersonId,
                'status'         => 1,
                'email'          => 'tutor' . $studentId . '@demo.com',
                'name'           => $apellidosP[array_rand($apellidosP)],
                'first_lastname' => $apP,
                'second_lastname'=> $apM,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];

            $studentRows[] = [
                'id'         => $studentId,
                'status'     => 1,
                'gender'     => $gender,
                'birthday'   => $birthday,
                'curp'       => substr($curp, 0, 18),
                'uuid'       => Str::uuid(),
                'person_id'  => $personId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $tutorRows[] = [
                'status'     => 1,
                'tutor_type' => 'P',
                'relation'   => 'Padre',
                'student_id' => $studentId,
                'person_id'  => $tutorPersonId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Asignar grupo del nivel correspondiente
            $groups  = $groupsByLevel[$levelId];
            $groupId = $groups[$idx % count($groups)];

            $sgRows[] = [
                'id'         => $sgId,
                'status'     => 1,
                'group_id'   => $groupId,
                'student_id' => $studentId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $personId++;
            $studentId++;
            $sgId++;
        }

        // Insertar en chunks de 70
        foreach (array_chunk($peopleRows, 70) as $chunk) {
            DB::table('people')->insert($chunk);
        }
        foreach (array_chunk($studentRows, 70) as $chunk) {
            DB::table('students')->insert($chunk);
        }
        foreach (array_chunk($tutorRows, 70) as $chunk) {
            DB::table('student_tutors')->insert($chunk);
        }
        foreach (array_chunk($sgRows, 70) as $chunk) {
            DB::table('student_groups')->insert($chunk);
        }
    }
}
