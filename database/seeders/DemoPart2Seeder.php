<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoPart2Seeder extends Seeder
{
    public function run(): void
    {
        // Meses a generar: enero(1) a mayo(5) 2026
        // concept_id por nivel y mes
        $conceptsByLevel = [
            1 => [1 => 17, 2 => 18, 3 => 19, 4 => 20, 5 => 21], // Preescolar
            2 => [1 => 7,  2 => 8,  3 => 9,  4 => 10, 5 => null], // Primaria (solo hasta abril)
            3 => [1 => 37, 2 => 38, 3 => 39, 4 => 40, 5 => 41], // Secundaria
        ];

        $amountByLevel = [
            1 => ['full' => 1800.00, 'disc' => 1600.00],
            2 => ['full' => 2500.00, 'disc' => 2200.00],
            3 => ['full' => 3000.00, 'disc' => 2700.00],
        ];

        $payMethods = [1, 1, 1, 2, 3]; // mayoría efectivo

        // Obtener student_groups con su nivel
        $studentGroups = DB::table('student_groups as sg')
            ->join('groups as g', 'g.id', '=', 'sg.group_id')
            ->where('sg.id', '>=', 100)
            ->select('sg.id as sg_id', 'sg.student_id', 'g.academic_level_id')
            ->get();

        $ticketId    = 1000;
        $tpId        = 1000;
        $paymentId   = 1000;
        $folioCounter = 1000;

        $tickets   = [];
        $tps       = [];
        $payments  = [];

        $payMethodKeys = ['EF', 'TR', 'TJ'];

        foreach ($studentGroups as $idx => $sg) {
            $levelId   = $sg->academic_level_id;
            $amounts   = $amountByLevel[$levelId];
            $concepts  = $conceptsByLevel[$levelId];

            // 70% pagan puntual (antes del 10), 30% tarde
            $isPunctual = ($idx % 10) < 7;

            foreach ($concepts as $month => $conceptId) {
                if (!$conceptId) continue;

                $payMethodId  = $payMethods[$idx % 5];
                $pmKey        = $payMethodKeys[$payMethodId - 1];
                $hasDiscount  = $isPunctual;
                $amount       = $hasDiscount ? $amounts['disc'] : $amounts['full'];
                $discount     = $hasDiscount ? ($amounts['full'] - $amounts['disc']) : 0;
                $dayPaid      = $isPunctual ? rand(1, 9) : rand(12, 28);
                $paidAt       = '2026-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($dayPaid, 2, '0', STR_PAD_LEFT);
                $folio        = 'TK' . $pmKey . '2526' . str_pad($folioCounter, 5, '0', STR_PAD_LEFT);

                $tickets[] = [
                    'id'               => $ticketId,
                    'is_full_payed'    => true,
                    'amount'           => $amount,
                    'has_discount'     => $hasDiscount,
                    'discount_type'    => $hasDiscount ? 'early_payment' : null,
                    'discount_amount'  => $discount,
                    'folio_ticket'     => $folio,
                    'payment_method_id'=> $payMethodId,
                    'student_group_id' => $sg->sg_id,
                    'is_cancelled'     => false,
                    'created_at'       => $paidAt,
                    'updated_at'       => $paidAt,
                ];

                $tps[] = [
                    'id'            => $tpId,
                    'quantity'      => 1,
                    'discount'      => $discount,
                    'total'         => $amount,
                    'ticket_id'     => $ticketId,
                    'pay_concept_id'=> $conceptId,
                    'created_at'    => $paidAt,
                    'updated_at'    => $paidAt,
                ];

                $payments[] = [
                    'id'               => $paymentId,
                    'is_full_payment'  => 1,
                    'paid_amount'      => $amount,
                    'paid_at'          => $paidAt,
                    'applied_discount' => $hasDiscount,
                    'ticket_product_id'=> $tpId,
                    'created_at'       => $paidAt,
                    'updated_at'       => $paidAt,
                ];

                $ticketId++;
                $tpId++;
                $paymentId++;
                $folioCounter++;
            }
        }

        // Insertar en chunks de 70
        foreach (array_chunk($tickets, 70) as $chunk) {
            DB::table('tickets')->insert($chunk);
        }
        foreach (array_chunk($tps, 70) as $chunk) {
            DB::table('ticket_products')->insert($chunk);
        }
        foreach (array_chunk($payments, 70) as $chunk) {
            DB::table('payments')->insert($chunk);
        }

        // Actualizar folio counter
        DB::table('cat_folios')->where('id', 1)->update(['counter' => $folioCounter]);
    }
}
