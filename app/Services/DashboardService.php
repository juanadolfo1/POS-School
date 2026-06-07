<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Resumen ejecutivo del ciclo escolar.
     */
    public function getSummary(int $scholarYearId): array
    {
        // Total alumnos inscritos por nivel
        $studentsByLevel = DB::table('student_groups as sg')
            ->join('groups as g', 'g.id', '=', 'sg.group_id')
            ->join('cat_academic_levels as cal', 'cal.id', '=', 'g.academic_level_id')
            ->where([
                ['g.scholar_year_id', '=', $scholarYearId],
                ['sg.status', '=', 1],
            ])
            ->groupBy('cal.id', 'cal.label')
            ->select(
                'cal.id as academic_level_id',
                'cal.label as academic_level',
                DB::raw('COUNT(DISTINCT sg.student_id) as total_students')
            )
            ->get();

        // Cobranza del ciclo
        $revenue = DB::table('payments')
            ->join('ticket_products as tp', 'tp.id', '=', 'payments.ticket_product_id')
            ->join('tickets as t', 't.id', '=', 'tp.ticket_id')
            ->join('student_groups as sg', 'sg.id', '=', 't.student_group_id')
            ->join('groups as g', 'g.id', '=', 'sg.group_id')
            ->where('g.scholar_year_id', $scholarYearId)
            ->where('t.is_cancelled', false)
            ->whereNull('payments.deleted_at')
            ->select(
                DB::raw('SUM(payments.paid_amount) as total_collected'),
                DB::raw('COUNT(DISTINCT t.id) as total_tickets')
            )
            ->first();

        // Pagos pendientes (conceptos sin ticket)
        $totalExpected = DB::table('cat_pay_concepts')
            ->where([
                ['scholar_year_id', '=', $scholarYearId],
                ['pay_concept_type', '=', 'tuition'],
                ['status', '=', 1],
            ])
            ->sum('amount');

        $totalStudents = $studentsByLevel->sum('total_students');
        $expectedRevenue = $totalExpected * $totalStudents;

        // Pronto pago stats del mes actual
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $promotionStats = DB::table('payments')
            ->join('ticket_products as tp', 'tp.id', '=', 'payments.ticket_product_id')
            ->join('tickets as t', 't.id', '=', 'tp.ticket_id')
            ->where('t.is_cancelled', false)
            ->whereMonth('payments.paid_at', $currentMonth)
            ->whereYear('payments.paid_at', $currentYear)
            ->whereNull('payments.deleted_at')
            ->select(
                DB::raw('COUNT(CASE WHEN payments.applied_discount = true THEN 1 END) as with_discount'),
                DB::raw('COUNT(CASE WHEN payments.applied_discount = false THEN 1 END) as without_discount')
            )
            ->first();

        // Bajas del ciclo
        $withdrawals = DB::table('student_withdrawals')
            ->where('scholar_year_id', $scholarYearId)
            ->select(
                DB::raw("COUNT(CASE WHEN type = 'temporal' AND status = 1 THEN 1 END) as temporal_active"),
                DB::raw("COUNT(CASE WHEN type = 'definitiva' THEN 1 END) as definitiva"),
                DB::raw("COUNT(CASE WHEN status = 0 THEN 1 END) as reactivated")
            )
            ->first();

        // Tickets cancelados del ciclo
        $cancelledTickets = DB::table('tickets')
            ->join('student_groups as sg', 'sg.id', '=', 'tickets.student_group_id')
            ->join('groups as g', 'g.id', '=', 'sg.group_id')
            ->where([
                ['g.scholar_year_id', '=', $scholarYearId],
                ['tickets.is_cancelled', '=', true],
            ])
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('COALESCE(SUM(tickets.amount), 0) as total_amount')
            )
            ->first();

        return [
            'students_by_level' => $studentsByLevel,
            'total_students' => $totalStudents,
            'revenue' => [
                'collected' => round((float) ($revenue->total_collected ?? 0), 2),
                'expected' => round($expectedRevenue, 2),
                'percentage' => $expectedRevenue > 0 ? round(((float) ($revenue->total_collected ?? 0) / $expectedRevenue) * 100, 1) : 0,
                'total_tickets' => (int) ($revenue->total_tickets ?? 0),
            ],
            'promotion' => [
                'with_discount' => (int) ($promotionStats->with_discount ?? 0),
                'without_discount' => (int) ($promotionStats->without_discount ?? 0),
            ],
            'withdrawals' => [
                'temporal_active' => (int) ($withdrawals->temporal_active ?? 0),
                'definitiva' => (int) ($withdrawals->definitiva ?? 0),
                'reactivated' => (int) ($withdrawals->reactivated ?? 0),
            ],
            'cancelled_tickets' => [
                'total' => (int) ($cancelledTickets->total ?? 0),
                'total_amount' => round((float) ($cancelledTickets->total_amount ?? 0), 2),
            ],
        ];
    }

    /**
     * Estado de cuenta de un alumno.
     */
    public function getStudentAccountStatement(int $studentId, int $scholarYearId): array
    {
        // Info del alumno
        $student = DB::table('students')
            ->join('people', 'people.id', '=', 'students.person_id')
            ->where('students.id', $studentId)
            ->select(
                'students.id',
                'students.curp',
                DB::raw("CONCAT_WS(' ', people.name, people.first_lastname, people.second_lastname) as name")
            )
            ->first();

        // Todos los conceptos de colegiatura del ciclo para su nivel
        $studentLevel = DB::table('student_groups as sg')
            ->join('groups as g', 'g.id', '=', 'sg.group_id')
            ->where([
                ['sg.student_id', '=', $studentId],
                ['g.scholar_year_id', '=', $scholarYearId],
                ['sg.status', '=', 1],
            ])
            ->select('g.academic_level_id', 'g.label as group_label')
            ->first();

        if (!$studentLevel) {
            return [
                'student' => $student,
                'group' => null,
                'concepts' => [],
                'totals' => ['total_due' => 0, 'total_paid' => 0, 'balance' => 0],
            ];
        }

        $concepts = DB::table('cat_pay_concepts')
            ->where([
                ['scholar_year_id', '=', $scholarYearId],
                ['id_cat_academic_level', '=', $studentLevel->academic_level_id],
                ['pay_concept_type', '=', 'tuition'],
                ['status', '=', 1],
            ])
            ->orderBy('last_day_with_discount')
            ->select('id', 'label', 'amount', 'discount_amount', 'last_day_with_discount')
            ->get();

        // Pagos realizados por concepto
        $payments = DB::table('payments')
            ->join('ticket_products as tp', 'tp.id', '=', 'payments.ticket_product_id')
            ->join('tickets as t', 't.id', '=', 'tp.ticket_id')
            ->join('student_groups as sg', 'sg.id', '=', 't.student_group_id')
            ->where([
                ['sg.student_id', '=', $studentId],
                ['t.is_cancelled', '=', false],
            ])
            ->whereNull('payments.deleted_at')
            ->select(
                'tp.pay_concept_id',
                'payments.paid_amount',
                'payments.paid_at',
                'payments.is_full_payment',
                'payments.applied_discount',
                't.folio_ticket'
            )
            ->get();

        $totalDue = 0;
        $totalPaid = 0;

        $statement = $concepts->map(function ($concept) use ($payments, &$totalDue, &$totalPaid) {
            $conceptPayments = $payments->where('pay_concept_id', $concept->id);
            $paidAmount = $conceptPayments->sum('paid_amount');
            $status = 'pending';

            if ($paidAmount >= $concept->amount) {
                $status = 'paid';
            } elseif ($paidAmount > 0) {
                $status = 'partial';
            }

            $totalDue += $concept->amount;
            $totalPaid += $paidAmount;

            return [
                'concept_id' => $concept->id,
                'label' => $concept->label,
                'amount' => round((float) $concept->amount, 2),
                'discount_amount' => round((float) $concept->discount_amount, 2),
                'last_day_with_discount' => $concept->last_day_with_discount,
                'paid_amount' => round((float) $paidAmount, 2),
                'balance' => round((float) ($concept->amount - $paidAmount), 2),
                'status' => $status,
                'payments' => $conceptPayments->map(function ($p) {
                    return [
                        'amount' => round((float) $p->paid_amount, 2),
                        'date' => $p->paid_at,
                        'folio' => $p->folio_ticket,
                        'applied_discount' => (bool) $p->applied_discount,
                    ];
                })->values(),
            ];
        });

        return [
            'student' => $student,
            'group' => $studentLevel->group_label,
            'concepts' => $statement,
            'totals' => [
                'total_due' => round($totalDue, 2),
                'total_paid' => round($totalPaid, 2),
                'balance' => round($totalDue - $totalPaid, 2),
            ],
        ];
    }
}
