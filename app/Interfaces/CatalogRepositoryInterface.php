<?php

namespace App\Interfaces;

use App\Models\CatAcademicLevel;
use App\Models\CatPayConcept;
use App\Models\Group;
use App\Models\PayConceptPrice;
use App\Models\ScholarYear;

interface CatalogRepositoryInterface{
    /** @return CatPayConcept[] */
    function get_pay_concepts(): array;
    function create_pay_concept($data): CatPayConcept;
    function update_pay_concept($data): CatPayConcept;
    function delete_pay_concept($id): CatPayConcept;

    /** @return PayConceptPrice[] */
    function get_pay_concept_prices($priceConceptId): array;
    function create_pay_concept_price($data): PayConceptPrice;
    function update_pay_concept_price($data): PayConceptPrice;
    function delete_pay_concept_price($id): PayConceptPrice;

    /** @return ScholarYear[] */
    function get_scholar_years(): array;
    function get_active_scholar_year(): ?ScholarYear;
    function create_scholar_year($data): ScholarYear;
    function update_scholar_year($data): ScholarYear;
    function delete_scholar_year($id): ScholarYear;

    /** @return CatAcademicLevel[] */
    function get_academic_levels(): array;
    function create_academic_level($data): CatAcademicLevel;
    function update_academic_level($data): CatAcademicLevel;
    function delete_academic_level($id): CatAcademicLevel;

    /** @return Group[] */
    function get_groups(int $ScholarYearId = 0, int $academicLevelId = 0): array;
    function create_group($data): Group;
    function update_group($data): Group;
    function delete_group($id): Group;

}
