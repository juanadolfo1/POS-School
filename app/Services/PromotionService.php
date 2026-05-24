<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\CatPayConcept;
use Carbon\Carbon;

class PromotionService
{
    /**
     * Verifica si el alumno es elegible para descuento.
     * Revisa que todos los meses anteriores hayan sido pagados
     * antes de su last_day_with_discount considerando solo días hábiles (L-V).
     */
    public function isEligibleForDiscount(int $studentId, int $scholarYearId, int $academicLevelId): bool
    {
        $today = now()->toDateString();

        // Colegiaturas cuya fecha límite ya pasó
        $dueConcepts = CatPayConcept::where([
            ['scholar_year_id', '=', $scholarYearId],
            ['id_cat_academic_level', '=', $academicLevelId],
            ['pay_concept_type', '=', 'tuition'],
            ['last_day_with_discount', '<', $today],
        ])->get();

        if ($dueConcepts->isEmpty()) {
            return true;
        }

        foreach ($dueConcepts as $concept) {
            $deadline = $this->adjustToBusinessDay($concept->last_day_with_discount);

            $paidOnTime = DB::table('payments')
                ->join('ticket_products as tp', 'payments.ticket_product_id', '=', 'tp.id')
                ->join('tickets as t', 't.id', '=', 'tp.ticket_id')
                ->join('student_groups as sg', 'sg.id', '=', 't.student_group_id')
                ->where([
                    ['sg.student_id', '=', $studentId],
                    ['tp.pay_concept_id', '=', $concept->id],
                    ['payments.paid_at', '<=', $deadline],
                ])
                ->exists();

            if (!$paidOnTime) {
                return false;
            }
        }

        return true;
    }

    /**
     * Si la fecha límite cae en sábado o domingo,
     * se recorre al viernes anterior (último día hábil).
     */
    private function adjustToBusinessDay(string $date): string
    {
        $carbon = Carbon::parse($date);

        if ($carbon->isSaturday()) {
            $carbon->subDay(); // viernes
        } elseif ($carbon->isSunday()) {
            $carbon->subDays(2); // viernes
        }

        return $carbon->toDateString();
    }
}
