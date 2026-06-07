<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\CatPayConcept;
use App\Models\PromotionConfig;
use Carbon\Carbon;

class PromotionService
{
    /**
     * Verifica si el alumno es elegible para descuento.
     * Revisa que todos los meses anteriores hayan sido pagados
     * antes de su fecha límite (configurable) considerando solo días hábiles (L-V).
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

        // Obtener configuración de pronto pago
        $config = PromotionConfig::where([
            ['scholar_year_id', '=', $scholarYearId],
            ['academic_level_id', '=', $academicLevelId],
            ['status', '=', 1],
        ])->with('overrides')->first();

        foreach ($dueConcepts as $concept) {
            $deadline = $this->resolveDeadline($concept, $config);

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
     * Resuelve la fecha límite para un concepto:
     * 1. Si hay override para ese mes → usa la fecha exacta del override
     * 2. Si hay config con default_day → calcula la fecha con ese día
     * 3. Fallback → usa last_day_with_discount del concepto
     * Siempre ajusta a día hábil.
     */
    private function resolveDeadline(CatPayConcept $concept, ?PromotionConfig $config): string
    {
        $conceptDate = Carbon::parse($concept->last_day_with_discount);
        $month = $conceptDate->month;
        $year = $conceptDate->year;

        if ($config) {
            // Buscar override específico para ese mes
            $override = $config->overrides->firstWhere('month', $month);

            if ($override) {
                return $this->adjustToBusinessDay($override->deadline_date->toDateString());
            }

            // Usar día default de la config
            $day = min($config->default_day, $conceptDate->daysInMonth);
            $deadline = Carbon::create($year, $month, $day);
            return $this->adjustToBusinessDay($deadline->toDateString());
        }

        // Fallback: usar last_day_with_discount del concepto
        return $this->adjustToBusinessDay($concept->last_day_with_discount);
    }

    /**
     * Si la fecha límite cae en sábado o domingo,
     * se recorre al viernes anterior (último día hábil).
     */
    private function adjustToBusinessDay(string $date): string
    {
        $carbon = Carbon::parse($date);

        if ($carbon->isSaturday()) {
            $carbon->subDay();
        } elseif ($carbon->isSunday()) {
            $carbon->subDays(2);
        }

        return $carbon->toDateString();
    }
}
