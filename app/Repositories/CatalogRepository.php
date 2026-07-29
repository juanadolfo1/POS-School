<?php

namespace App\Repositories;

use App\Interfaces\CatalogRepositoryInterface;
use App\Models\CatAcademicLevel;
use App\Models\CatPayConcept;
use App\Models\Group;
use App\Models\PayConceptPrice;
use App\Models\ScholarYear;

class CatalogRepository implements CatalogRepositoryInterface{

    /**
     * Pay Concepts
     */

    public function get_pay_concepts(): array
    {
        $payConcepts = CatPayConcept::select('id', 'label', 'status')->get()->toArray();

        return $payConcepts;
    }

    public function create_pay_concept($data): CatPayConcept
    {
        $newPayConcept = new CatPayConcept();
        $newPayConcept->label = $data->label;
        $newPayConcept->status = 1;
        $newPayConcept->save();

        return $newPayConcept;
    }

    public function update_pay_concept($data): CatPayConcept
    {
        $payConcept = CatPayConcept::find($data->id);
        if($payConcept){
            $payConcept->label = $data->label;
            $payConcept->status = $data->status;
            $payConcept->save();
        }

        return $payConcept;
    }

    public function delete_pay_concept($id): CatPayConcept
    {
        $payConcept = CatPayConcept::find($id);
        if($payConcept){
            $payConcept->delete();
        }

        return $payConcept;
    }

    /**
     * Pay Concepts
     */
    /**
     * Pay Concept Prices
     */

    public function get_pay_concept_prices($priceConceptId): array
    {
        $payConceptPrices = PayConceptPrice::where('pay_concept_id', '=', $priceConceptId)
                                ->select('id', 'price', 'status')
                                ->get()
                                ->toArray();

        return $payConceptPrices;
    }

    public function create_pay_concept_price($data): PayConceptPrice
    {
        // Set all existing pay_concept_prices for this pay_concept_id to status 0
        PayConceptPrice::where('pay_concept_id', $data->pay_concept_id)
                        ->update(['status' => 0]);

        // Create the new pay_concept_price with status 1
        $newPayConceptPrice = new PayConceptPrice();
        $newPayConceptPrice->price = $data->price;
        $newPayConceptPrice->scholar_year_id = $data->scholar_year_id;
        $newPayConceptPrice->pay_concept_id = $data->pay_concept_id;
        $newPayConceptPrice->status = 1;
        $newPayConceptPrice->save();

        return $newPayConceptPrice;
    }

    public function update_pay_concept_price($data): PayConceptPrice
    {
        $payConceptPrice = PayConceptPrice::find($data->id);
        if($payConceptPrice){
            $payConceptPrice->price = $data->price;
            $payConceptPrice->scholar_year_id = $data->scholar_year_id;
            $payConceptPrice->pay_concept_id = $data->pay_concept_id;
            $payConceptPrice->status = $data->status;
            $payConceptPrice->save();
        }

        return $payConceptPrice;
    }

    public function delete_pay_concept_price($id): PayConceptPrice
    {
        $payConceptPrice = PayConceptPrice::find($id);
        if($payConceptPrice){
            $payConceptPrice->delete();
        }

        return $payConceptPrice;
    }

    /**
     * Pay Concept Prices
     */
    /**
     * Scholar Years
     */

    public function get_scholar_years(): array
    {
        $scholarYears = ScholarYear::select('id', 'year', 'status')->get()->toArray();

        return $scholarYears;
    }

    public function get_active_scholar_year(): ?ScholarYear
    {
        return ScholarYear::where('status', 1)
            ->select('id', 'year', 'starts_at', 'ends_at', 'status')
            ->orderBy('starts_at', 'desc')
            ->first();
    }

    public function create_scholar_year($data): ScholarYear
    {
        $newSchoolarYear = new ScholarYear();
        $newSchoolarYear->year = $data->year;
        $newSchoolarYear->starts_at = $data->starts_at;
        $newSchoolarYear->ends_at = $data->ends_at;
        $newSchoolarYear->status = 1;
        $newSchoolarYear->save();

        return $newSchoolarYear;
    }

    public function update_scholar_year($data): ScholarYear
    {
        $scholarYear = ScholarYear::find($data->id);
        if($scholarYear){
            $scholarYear->year = $data->year;
            $scholarYear->status = $data->status;
            $scholarYear->save();
        }

        return $scholarYear;
    }

    public function delete_scholar_year($id): ScholarYear
    {
        $scholarYear = ScholarYear::find($id);
        if($scholarYear){
            $scholarYear->delete();
        }

        return $scholarYear;
    }

    /**
     * Scholar Years
     */
    /**
     * Academic Level
     */

    public function get_academic_levels(): array
    {
        $academicLevels = CatAcademicLevel::select('id', 'label', 'status')->get()->toArray();

        return $academicLevels;
    }

    public function create_academic_level($data): CatAcademicLevel
    {
        $newAcademicLevel = new CatAcademicLevel();
        $newAcademicLevel->label = $data->label;
        $newAcademicLevel->status = 1;
        $newAcademicLevel->save();

        return $newAcademicLevel;
    }

    public function update_academic_level($data): CatAcademicLevel
    {
        $academicLevel = CatAcademicLevel::find($data->id);
        if($academicLevel){
            $academicLevel->label = $data->label;
            $academicLevel->status = $data->status;
            $academicLevel->save();
        }

        return $academicLevel;
    }

    public function delete_academic_level($id): CatAcademicLevel
    {
        $academicLevel = CatAcademicLevel::find($id);
        if($academicLevel){
            $academicLevel->delete();
        }

        return $academicLevel;
    }

    /**
     * Academic Level
     */
    /**
     * Groups
     */

    public function get_groups(int $scholarYearId = 0, int $academicLevelId = 0): array
    {
        if($scholarYearId > 0 && $academicLevelId > 0){
            $groups = Group::where('scholar_year_id', '=', $scholarYearId)
                        ->where('academic_level_id', '=', $academicLevelId)
                        ->select('id', 'label', 'status')
                        ->get()
                        ->toArray();
            return $groups;
        }

        $groups = Group::join('cat_academic_levels', 'groups.academic_level_id', '=', 'cat_academic_levels.id')
                    ->join('scholar_years', 'groups.scholar_year_id', '=', 'scholar_years.id')
                    ->select(
                        'groups.id',
                        'groups.label',
                        'groups.status',
                        'cat_academic_levels.label as academic_level',
                        'scholar_years.year as scholar_year'
                        )->get()->toArray();

        return $groups;
    }

    public function create_group($data): Group
    {
        $newGroup = new Group();
        $newGroup->label = $data->label;
        $newGroup->academic_level_id = $data->academic_level_id;
        $newGroup->scholar_year_id = $data->scholar_year_id;
        $newGroup->status = 1;
        $newGroup->save();

        return $newGroup;
    }

    public function update_group($data): Group
    {
        $group = Group::find($data->id);
        if($group){
            $group->label = $data->label;
            $group->academic_level_id = $data->academic_level_id;
            $group->scholar_year_id = $data->scholar_year_id;
            $group->status = $data->status;
            $group->save();
        }

        return $group;
    }

    public function delete_group($id): Group
    {
        $group = Group::find($id);
        if($group){
            $group->delete();
        }

        return $group;
    }

    /**
     * Groups
     */

}
