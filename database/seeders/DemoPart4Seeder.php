<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoPart4Seeder extends Seeder
{
    public function run(): void
    {
        // ===== BRANDING =====
        DB::table('school_config')->insert([
            'school_name'  => 'Colegio Demo 2025',
            'favicon_path' => null,
            'logo_path'    => null,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // ===== TICKET CANCELADO (para demo del módulo documentos) =====
        // Usamos student_group_id 100 (primer alumno del demo, Preescolar)
        DB::table('tickets')->insert([
            'id'               => 9999,
            'is_full_payed'    => true,
            'amount'           => 1800.00,
            'has_discount'     => false,
            'discount_type'    => null,
            'discount_amount'  => 0.00,
            'folio_ticket'     => 'TKEF2526CANCEL',
            'payment_method_id'=> 1,
            'student_group_id' => 100,
            'is_cancelled'     => true,
            'cancel_reason'    => 'Error en el cobro - demo',
            'cancelled_at'     => now(),
            'cancelled_by'     => 1,
            'created_at'       => '2026-03-05',
            'updated_at'       => now(),
        ]);

        DB::table('ticket_products')->insert([
            'id'             => 9999,
            'quantity'       => 1,
            'discount'       => 0.00,
            'total'          => 1800.00,
            'ticket_id'      => 9999,
            'pay_concept_id' => 19,
            'created_at'     => '2026-03-05',
            'updated_at'     => '2026-03-05',
        ]);

        DB::table('payments')->insert([
            'id'               => 9999,
            'is_full_payment'  => 1,
            'paid_amount'      => 1800.00,
            'paid_at'          => '2026-03-05',
            'applied_discount' => false,
            'ticket_product_id'=> 9999,
            'deleted_at'       => now(), // soft deleted por cancelación
            'created_at'       => '2026-03-05',
            'updated_at'       => now(),
        ]);
    }
}
