<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ========== ROLES ==========
        DB::table('roles')->insert([
            ['id' => 1, 'role_name' => 'Administrador', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'role_name' => 'Cajero', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'role_name' => 'Secretaria', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ========== MODULES ==========
        DB::table('modules')->insert([
            ['id' => 1, 'module_name' => 'Alumnos',      'path' => 'dashboards/students',   'icon' => 'pi pi-users',        'order' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'module_name' => 'Tutores',      'path' => 'dashboards/tutors',     'icon' => 'pi pi-id-card',      'order' => 2, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'module_name' => 'Pagos',        'path' => 'dashboards/payments',   'icon' => 'pi pi-credit-card',  'order' => 3, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'module_name' => 'Documentos',   'path' => 'dashboards/documents',  'icon' => 'pi pi-file',         'order' => 4, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'module_name' => 'Cat\u00e1logos',    'path' => 'dashboards/catalogs',   'icon' => 'pi pi-cog',          'order' => 5, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'module_name' => 'Usuarios',     'path' => 'dashboards/users',      'icon' => 'pi pi-user-edit',    'order' => 6, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ========== OPERATIONS ==========
        DB::table('operations')->insert([
            // Alumnos (module 1)
            ['id' => 1,  'operation_name' => 'Ver',      'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2,  'operation_name' => 'Crear',    'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3,  'operation_name' => 'Editar',   'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4,  'operation_name' => 'Eliminar', 'module_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            // Tutores (module 2)
            ['id' => 5,  'operation_name' => 'Ver',      'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6,  'operation_name' => 'Crear',    'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7,  'operation_name' => 'Editar',   'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8,  'operation_name' => 'Eliminar', 'module_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            // Pagos (module 3)
            ['id' => 9,  'operation_name' => 'Ver',      'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'operation_name' => 'Cobrar',   'module_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            // Documentos (module 4)
            ['id' => 11, 'operation_name' => 'Ver',      'module_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'operation_name' => 'Generar',  'module_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            // Catálogos (module 5)
            ['id' => 13, 'operation_name' => 'Ver',      'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'operation_name' => 'Crear',    'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'operation_name' => 'Editar',   'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'operation_name' => 'Eliminar', 'module_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            // Usuarios (module 6)
            ['id' => 17, 'operation_name' => 'Ver',      'module_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 18, 'operation_name' => 'Crear',    'module_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'operation_name' => 'Editar',   'module_id' => 6, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ========== ROLE_OPERATIONS ==========
        // Administrador → TODO
        DB::table('role_operations')->insert([
            ['role_id' => 1, 'operation_id' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 1, 'operation_id' => 2,  'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 1, 'operation_id' => 3,  'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 1, 'operation_id' => 4,  'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 1, 'operation_id' => 5,  'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 1, 'operation_id' => 6,  'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 1, 'operation_id' => 7,  'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 1, 'operation_id' => 8,  'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 1, 'operation_id' => 9,  'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 1, 'operation_id' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 1, 'operation_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 1, 'operation_id' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 1, 'operation_id' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 1, 'operation_id' => 14, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 1, 'operation_id' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 1, 'operation_id' => 16, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 1, 'operation_id' => 17, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 1, 'operation_id' => 18, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 1, 'operation_id' => 19, 'created_at' => now(), 'updated_at' => now()],
        ]);
        // Cajero → Solo Pagos (ver + cobrar) y Documentos (ver + generar ticket)
        DB::table('role_operations')->insert([
            ['role_id' => 2, 'operation_id' => 9,  'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 2, 'operation_id' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 2, 'operation_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 2, 'operation_id' => 12, 'created_at' => now(), 'updated_at' => now()],
        ]);
        // Secretaria → Alumnos (ver, crear, editar), Tutores (ver, crear, editar), Pagos (ver), Documentos (ver, generar)
        DB::table('role_operations')->insert([
            ['role_id' => 3, 'operation_id' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 3, 'operation_id' => 2,  'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 3, 'operation_id' => 3,  'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 3, 'operation_id' => 5,  'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 3, 'operation_id' => 6,  'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 3, 'operation_id' => 7,  'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 3, 'operation_id' => 9,  'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 3, 'operation_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['role_id' => 3, 'operation_id' => 12, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ========== USERS ==========
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'email' => 'admin@school.com',
                'password' => Hash::make('password123'),
                'role_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cajero',
                'email' => 'cajero@school.com',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Secretaria',
                'email' => 'secretaria@school.com',
                'password' => Hash::make('password123'),
                'role_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ========== SCHOLAR YEARS ==========
        DB::table('scholar_years')->insert([
            ['id' => 1, 'status' => 1, 'year' => '2025-2026', 'starts_at' => '2025-08-01', 'ends_at' => '2026-07-31', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ========== ACADEMIC LEVELS ==========
        DB::table('cat_academic_levels')->insert([
            ['id' => 1, 'status' => 1, 'label' => 'Preescolar', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'status' => 1, 'label' => 'Primaria', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'status' => 1, 'label' => 'Secundaria', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ========== PAYMENT METHODS ==========
        DB::table('cat_payment_methods')->insert([
            ['id' => 1, 'name' => 'Efectivo', 'key' => 'EF', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Transferencia', 'key' => 'TR', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Tarjeta', 'key' => 'TJ', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ========== PAY CONCEPTS (Colegiaturas Primaria 2025-2026) ==========
        DB::table('cat_pay_concepts')->insert([
            ['id' => 1, 'status' => 1, 'label' => 'Inscripción', 'amount' => 3000.00, 'discount_amount' => 2700.00, 'last_day_with_discount' => '2025-07-15', 'pay_concept_type' => 'service', 'scholar_year_id' => 1, 'id_cat_academic_level' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'status' => 1, 'label' => 'Colegiatura Agosto', 'amount' => 2500.00, 'discount_amount' => 2200.00, 'last_day_with_discount' => '2025-08-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'status' => 1, 'label' => 'Colegiatura Septiembre', 'amount' => 2500.00, 'discount_amount' => 2200.00, 'last_day_with_discount' => '2025-09-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'status' => 1, 'label' => 'Colegiatura Octubre', 'amount' => 2500.00, 'discount_amount' => 2200.00, 'last_day_with_discount' => '2025-10-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'status' => 1, 'label' => 'Colegiatura Noviembre', 'amount' => 2500.00, 'discount_amount' => 2200.00, 'last_day_with_discount' => '2025-11-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'status' => 1, 'label' => 'Colegiatura Diciembre', 'amount' => 2500.00, 'discount_amount' => 2200.00, 'last_day_with_discount' => '2025-12-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'status' => 1, 'label' => 'Colegiatura Enero', 'amount' => 2500.00, 'discount_amount' => 2200.00, 'last_day_with_discount' => '2026-01-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'status' => 1, 'label' => 'Colegiatura Febrero', 'amount' => 2500.00, 'discount_amount' => 2200.00, 'last_day_with_discount' => '2026-02-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'status' => 1, 'label' => 'Colegiatura Marzo', 'amount' => 2500.00, 'discount_amount' => 2200.00, 'last_day_with_discount' => '2026-03-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'status' => 1, 'label' => 'Colegiatura Abril', 'amount' => 2500.00, 'discount_amount' => 2200.00, 'last_day_with_discount' => '2026-04-10', 'pay_concept_type' => 'tuition', 'scholar_year_id' => 1, 'id_cat_academic_level' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ========== PEOPLE ==========
        DB::table('people')->insert([
            ['id' => 1, 'status' => 1, 'email' => 'juan.papa@mail.com', 'name' => 'Juan', 'first_lastname' => 'García', 'second_lastname' => 'López', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'status' => 1, 'email' => 'pedro.papa@mail.com', 'name' => 'Pedro', 'first_lastname' => 'Martínez', 'second_lastname' => 'Hernández', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'status' => 1, 'email' => 'maria.mama@mail.com', 'name' => 'María', 'first_lastname' => 'Sánchez', 'second_lastname' => 'Ruiz', 'created_at' => now(), 'updated_at' => now()],
            // Tutores
            ['id' => 4, 'status' => 1, 'email' => 'tutor.juan@mail.com', 'name' => 'Roberto', 'first_lastname' => 'García', 'second_lastname' => 'Pérez', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'status' => 1, 'email' => 'tutor.pedro@mail.com', 'name' => 'Luis', 'first_lastname' => 'Martínez', 'second_lastname' => 'Gómez', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ========== STUDENTS ==========
        DB::table('students')->insert([
            ['id' => 1, 'status' => 1, 'gender' => 'M', 'birthday' => '2015-03-15', 'curp' => 'GALJ150315HDFRPN01', 'uuid' => Str::uuid(), 'person_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'status' => 1, 'gender' => 'M', 'birthday' => '2015-06-20', 'curp' => 'MAHP150620HDFRRD02', 'uuid' => Str::uuid(), 'person_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'status' => 1, 'gender' => 'F', 'birthday' => '2015-01-10', 'curp' => 'SARM150110MDFRZA03', 'uuid' => Str::uuid(), 'person_id' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ========== STUDENT TUTORS ==========
        DB::table('student_tutors')->insert([
            ['status' => 1, 'tutor_type' => 'P', 'relation' => 'Padre', 'student_id' => 1, 'person_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['status' => 1, 'tutor_type' => 'P', 'relation' => 'Padre', 'student_id' => 2, 'person_id' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ========== GROUPS ==========
        DB::table('groups')->insert([
            ['id' => 1, 'status' => 1, 'label' => '4A', 'scholar_year_id' => 1, 'academic_level_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'status' => 1, 'label' => '4B', 'scholar_year_id' => 1, 'academic_level_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ========== STUDENT GROUPS ==========
        DB::table('student_groups')->insert([
            ['id' => 1, 'status' => 1, 'group_id' => 1, 'student_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'status' => 1, 'group_id' => 1, 'student_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'status' => 1, 'group_id' => 2, 'student_id' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ========== SCHOLARSHIPS ==========
        DB::table('scholarships')->insert([
            ['id' => 1, 'status' => 1, 'name' => 'Beca Excelencia', 'amount' => 15.00, 'student_id' => 3, 'scholar_year_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ========== CAT FOLIOS ==========
        DB::table('cat_folios')->insert([
            ['id' => 1, 'key' => 'TK', 'counter' => 3, 'scholar_year_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ========== TICKETS (Juan pagó puntual, Pedro pagó tarde) ==========
        // Juan - Agosto (puntual)
        DB::table('tickets')->insert([
            ['id' => 1, 'is_full_payed' => true, 'amount' => 2200.00, 'has_discount' => true, 'discount_type' => 'early_payment', 'discount_amount' => 300.00, 'folio_ticket' => 'TKEF252600001', 'payment_method_id' => 1, 'student_group_id' => 1, 'created_at' => '2025-08-05', 'updated_at' => '2025-08-05'],
        ]);
        // Pedro - Agosto (tarde)
        DB::table('tickets')->insert([
            ['id' => 2, 'is_full_payed' => true, 'amount' => 2500.00, 'has_discount' => false, 'discount_type' => null, 'discount_amount' => 0.00, 'folio_ticket' => 'TKEF252600002', 'payment_method_id' => 1, 'student_group_id' => 2, 'created_at' => '2025-08-15', 'updated_at' => '2025-08-15'],
        ]);
        // Juan - Septiembre (puntual)
        DB::table('tickets')->insert([
            ['id' => 3, 'is_full_payed' => true, 'amount' => 2200.00, 'has_discount' => true, 'discount_type' => 'early_payment', 'discount_amount' => 300.00, 'folio_ticket' => 'TKEF252600003', 'payment_method_id' => 1, 'student_group_id' => 1, 'created_at' => '2025-09-08', 'updated_at' => '2025-09-08'],
        ]);

        // ========== TICKET PRODUCTS ==========
        // Juan - Agosto
        DB::table('ticket_products')->insert([
            ['id' => 1, 'quantity' => 1, 'discount' => 300.00, 'total' => 2200.00, 'ticket_id' => 1, 'pay_concept_id' => 2, 'created_at' => '2025-08-05', 'updated_at' => '2025-08-05'],
        ]);
        // Pedro - Agosto
        DB::table('ticket_products')->insert([
            ['id' => 2, 'quantity' => 1, 'discount' => 0.00, 'total' => 2500.00, 'ticket_id' => 2, 'pay_concept_id' => 2, 'created_at' => '2025-08-15', 'updated_at' => '2025-08-15'],
        ]);
        // Juan - Septiembre
        DB::table('ticket_products')->insert([
            ['id' => 3, 'quantity' => 1, 'discount' => 300.00, 'total' => 2200.00, 'ticket_id' => 3, 'pay_concept_id' => 3, 'created_at' => '2025-09-08', 'updated_at' => '2025-09-08'],
        ]);

        // ========== PAYMENTS ==========
        // Juan - Agosto (pagó el 5, límite el 10) → PUNTUAL
        DB::table('payments')->insert([
            ['id' => 1, 'is_full_payment' => 1, 'paid_amount' => 2200.00, 'paid_at' => '2025-08-05', 'applied_discount' => true, 'ticket_product_id' => 1, 'created_at' => '2025-08-05', 'updated_at' => '2025-08-05'],
        ]);
        // Pedro - Agosto (pagó el 15, límite el 10) → TARDE
        DB::table('payments')->insert([
            ['id' => 2, 'is_full_payment' => 1, 'paid_amount' => 2500.00, 'paid_at' => '2025-08-15', 'applied_discount' => false, 'ticket_product_id' => 2, 'created_at' => '2025-08-15', 'updated_at' => '2025-08-15'],
        ]);
        // Juan - Septiembre (pagó el 8, límite el 10) → PUNTUAL
        DB::table('payments')->insert([
            ['id' => 3, 'is_full_payment' => 1, 'paid_amount' => 2200.00, 'paid_at' => '2025-09-08', 'applied_discount' => true, 'ticket_product_id' => 3, 'created_at' => '2025-09-08', 'updated_at' => '2025-09-08'],
        ]);
    }
}
