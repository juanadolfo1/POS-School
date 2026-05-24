<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DailyIncomeSeeder extends Seeder
{
    public function run(): void
    {
        $today = now()->toDateString();

        // Grupo de Preescolar
        DB::table('groups')->insert([
            ['id' => 3, 'status' => 1, 'label' => '1A', 'scholar_year_id' => 1, 'academic_level_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Grupo de Secundaria
        DB::table('groups')->insert([
            ['id' => 4, 'status' => 1, 'label' => '1A', 'scholar_year_id' => 1, 'academic_level_id' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Personas adicionales
        DB::table('people')->insert([
            ['id' => 6, 'status' => 1, 'email' => 'ana@mail.com', 'name' => 'Ana', 'first_lastname' => 'López', 'second_lastname' => 'Díaz', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'status' => 1, 'email' => 'carlos@mail.com', 'name' => 'Carlos', 'first_lastname' => 'Ruiz', 'second_lastname' => 'Mora', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'status' => 1, 'email' => 'sofia@mail.com', 'name' => 'Sofía', 'first_lastname' => 'Torres', 'second_lastname' => 'Vega', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Estudiantes adicionales
        DB::table('students')->insert([
            ['id' => 4, 'status' => 1, 'gender' => 'F', 'birthday' => '2018-05-10', 'curp' => 'LODA180510MDFPNA04', 'uuid' => Str::uuid(), 'person_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'status' => 1, 'gender' => 'M', 'birthday' => '2012-09-22', 'curp' => 'RUMC120922HDFRRA05', 'uuid' => Str::uuid(), 'person_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'status' => 1, 'gender' => 'F', 'birthday' => '2011-11-03', 'curp' => 'TOVS111103MDFRGA06', 'uuid' => Str::uuid(), 'person_id' => 8, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Student groups
        DB::table('student_groups')->insert([
            ['id' => 4, 'status' => 1, 'group_id' => 3, 'student_id' => 4, 'created_at' => now(), 'updated_at' => now()], // Ana - Preescolar
            ['id' => 5, 'status' => 1, 'group_id' => 4, 'student_id' => 5, 'created_at' => now(), 'updated_at' => now()], // Carlos - Secundaria
            ['id' => 6, 'status' => 1, 'group_id' => 4, 'student_id' => 6, 'created_at' => now(), 'updated_at' => now()], // Sofía - Secundaria
        ]);

        // Tickets de HOY
        // Ticket 4: Juan (Primaria) - con descuento pronto pago
        DB::table('tickets')->insert([
            ['id' => 4, 'is_full_payed' => true, 'amount' => 2200.00, 'has_discount' => true, 'discount_type' => 'early_payment', 'discount_amount' => 300.00, 'folio_ticket' => 'TKEF252600004', 'payment_method_id' => 1, 'student_group_id' => 1, 'created_at' => $today, 'updated_at' => $today],
        ]);
        // Ticket 5: Pedro (Primaria) - sin descuento
        DB::table('tickets')->insert([
            ['id' => 5, 'is_full_payed' => true, 'amount' => 2500.00, 'has_discount' => false, 'discount_type' => null, 'discount_amount' => 0.00, 'folio_ticket' => 'TKEF252600005', 'payment_method_id' => 1, 'student_group_id' => 2, 'created_at' => $today, 'updated_at' => $today],
        ]);
        // Ticket 6: Ana (Preescolar) - con descuento pronto pago
        DB::table('tickets')->insert([
            ['id' => 6, 'is_full_payed' => true, 'amount' => 1800.00, 'has_discount' => true, 'discount_type' => 'early_payment', 'discount_amount' => 200.00, 'folio_ticket' => 'TKEF252600006', 'payment_method_id' => 2, 'student_group_id' => 4, 'created_at' => $today, 'updated_at' => $today],
        ]);
        // Ticket 7: Carlos (Secundaria) - con descuento pronto pago
        DB::table('tickets')->insert([
            ['id' => 7, 'is_full_payed' => true, 'amount' => 2800.00, 'has_discount' => true, 'discount_type' => 'early_payment', 'discount_amount' => 400.00, 'folio_ticket' => 'TKTJ252600007', 'payment_method_id' => 3, 'student_group_id' => 5, 'created_at' => $today, 'updated_at' => $today],
        ]);
        // Ticket 8: Sofía (Secundaria) - sin descuento
        DB::table('tickets')->insert([
            ['id' => 8, 'is_full_payed' => true, 'amount' => 3200.00, 'has_discount' => false, 'discount_type' => null, 'discount_amount' => 0.00, 'folio_ticket' => 'TKTR252600008', 'payment_method_id' => 2, 'student_group_id' => 6, 'created_at' => $today, 'updated_at' => $today],
        ]);

        // Ticket products
        DB::table('ticket_products')->insert([
            ['id' => 4, 'quantity' => 1, 'discount' => 300.00, 'total' => 2200.00, 'ticket_id' => 4, 'pay_concept_id' => 4, 'created_at' => $today, 'updated_at' => $today],
            ['id' => 5, 'quantity' => 1, 'discount' => 0.00, 'total' => 2500.00, 'ticket_id' => 5, 'pay_concept_id' => 4, 'created_at' => $today, 'updated_at' => $today],
            ['id' => 6, 'quantity' => 1, 'discount' => 200.00, 'total' => 1800.00, 'ticket_id' => 6, 'pay_concept_id' => 2, 'created_at' => $today, 'updated_at' => $today],
            ['id' => 7, 'quantity' => 1, 'discount' => 400.00, 'total' => 2800.00, 'ticket_id' => 7, 'pay_concept_id' => 3, 'created_at' => $today, 'updated_at' => $today],
            ['id' => 8, 'quantity' => 1, 'discount' => 0.00, 'total' => 3200.00, 'ticket_id' => 8, 'pay_concept_id' => 3, 'created_at' => $today, 'updated_at' => $today],
        ]);

        // Payments de HOY
        DB::table('payments')->insert([
            ['id' => 4, 'is_full_payment' => 1, 'paid_amount' => 2200.00, 'paid_at' => $today, 'applied_discount' => true, 'ticket_product_id' => 4, 'created_at' => $today, 'updated_at' => $today],
            ['id' => 5, 'is_full_payment' => 1, 'paid_amount' => 2500.00, 'paid_at' => $today, 'applied_discount' => false, 'ticket_product_id' => 5, 'created_at' => $today, 'updated_at' => $today],
            ['id' => 6, 'is_full_payment' => 1, 'paid_amount' => 1800.00, 'paid_at' => $today, 'applied_discount' => true, 'ticket_product_id' => 6, 'created_at' => $today, 'updated_at' => $today],
            ['id' => 7, 'is_full_payment' => 1, 'paid_amount' => 2800.00, 'paid_at' => $today, 'applied_discount' => true, 'ticket_product_id' => 7, 'created_at' => $today, 'updated_at' => $today],
            ['id' => 8, 'is_full_payment' => 1, 'paid_amount' => 3200.00, 'paid_at' => $today, 'applied_discount' => false, 'ticket_product_id' => 8, 'created_at' => $today, 'updated_at' => $today],
        ]);
    }
}
