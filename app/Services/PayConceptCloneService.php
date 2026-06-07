<?php

namespace App\Services;

use App\Models\CatPayConcept;
use App\Models\CustomException;
use Illuminate\Support\Facades\DB;

class PayConceptCloneService
{
    /**
     * Copia los conceptos de pago de un ciclo a otro.
     * Permite ajustar montos con un porcentaje de incremento.
     *
     * @param int $fromScholarYearId Ciclo origen
     * @param int $toScholarYearId Ciclo destino
     * @param int $academicLevelId Nivel académico
     * @param float $increasePercent Porcentaje de incremento (ej: 5 = +5%)
     * @return array Resumen de conceptos clonados
     */
    public function clone(int $fromScholarYearId, int $toScholarYearId, int $academicLevelId, float $increasePercent = 0): array
    {
        if ($fromScholarYearId === $toScholarYearId) {
            throw new CustomException('El ciclo origen y destino no pueden ser el mismo', 422);
        }

        // Verificar que no existan ya conceptos en el destino para ese nivel
        $existing = CatPayConcept::where([
            ['scholar_year_id', '=', $toScholarYearId],
            ['id_cat_academic_level', '=', $academicLevelId],
        ])->count();

        if ($existing > 0) {
            throw new CustomException('Ya existen conceptos de pago para este nivel en el ciclo destino', 409);
        }

        // Obtener conceptos del ciclo origen
        $sourceConcepts = CatPayConcept::where([
            ['scholar_year_id', '=', $fromScholarYearId],
            ['id_cat_academic_level', '=', $academicLevelId],
            ['status', '=', 1],
        ])->get();

        if ($sourceConcepts->isEmpty()) {
            throw new CustomException('No hay conceptos de pago en el ciclo origen para este nivel', 404);
        }

        $multiplier = 1 + ($increasePercent / 100);
        $cloned = 0;

        foreach ($sourceConcepts as $concept) {
            // Calcular nueva fecha (mismo día/mes, año siguiente)
            $originalDate = \Carbon\Carbon::parse($concept->last_day_with_discount);
            $newDate = $originalDate->addYear();

            CatPayConcept::create([
                'label' => $concept->label,
                'amount' => round($concept->amount * $multiplier, 2),
                'discount_amount' => round($concept->discount_amount * $multiplier, 2),
                'last_day_with_discount' => $newDate->toDateString(),
                'pay_concept_type' => $concept->pay_concept_type,
                'scholar_year_id' => $toScholarYearId,
                'id_cat_academic_level' => $academicLevelId,
                'status' => 1,
            ]);
            $cloned++;
        }

        return [
            'cloned_concepts' => $cloned,
            'increase_percent' => $increasePercent,
            'from_scholar_year_id' => $fromScholarYearId,
            'to_scholar_year_id' => $toScholarYearId,
        ];
    }
}
