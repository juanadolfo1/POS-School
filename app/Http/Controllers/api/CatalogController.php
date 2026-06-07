<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Interfaces\CatalogRepositoryInterface;
use App\Models\ApiResponse;
use App\Models\CustomException;
use App\Services\PayConceptCloneService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CatalogController extends Controller
{
    protected $catalogRepository;
    protected PayConceptCloneService $cloneService;

    public function __construct(CatalogRepositoryInterface $catalogRepository, PayConceptCloneService $cloneService){
        $this->catalogRepository = $catalogRepository;
        $this->cloneService = $cloneService;
    }

    /**
     * Schoolar years
     */
    public function get_scholar_years()
    {
        try {
            $scholarYears = $this->catalogRepository->get_scholar_years();

            if(count($scholarYears) == 0){
                return response()
                        ->json(ApiResponse::notFound('Schoolar years not found', $scholarYears))
                        ->setStatusCode(404);
            }

            return response()
                    ->json(ApiResponse::success('Schoolar years retrieved successfully', $scholarYears))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error fetching scholar years: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to retrieve scholar years', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function create_scholar_year(Request $request)
    {
        $data = $request;
        try {
            $scholarYear = $this->catalogRepository->create_scholar_year($data);

            return response()
                    ->json(ApiResponse::success('Schoolar year created successfully', $scholarYear))
                    ->setStatusCode(201);
        } catch (Exception $e) {
            Log::error('Error creating scholar year: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to create scholar year', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function update_scholar_year(Request $request)
    {
        $data = $request;
        try {
            $scholarYear = $this->catalogRepository->update_scholar_year($data);

            if(!$scholarYear){
                return response()
                        ->json(ApiResponse::notFound('Schoolar year not found', $scholarYear))
                        ->setStatusCode(404);
            }

            return response()
                    ->json(ApiResponse::success('Schoolar year updated successfully', $scholarYear))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error updating scholar year: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to update scholar year', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function delete_scholar_year(Request $request)
    {
        $id = $request->id;
        try {
            $scholarYear = $this->catalogRepository->delete_scholar_year($id);

            if(!$scholarYear){
                return response()
                        ->json(ApiResponse::notFound('Schoolar year not found', $scholarYear))
                        ->setStatusCode(404);
            }

            return response()
                    ->json(ApiResponse::success('Schoolar year deleted successfully', $scholarYear))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error deleting scholar year: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to delete scholar year', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    /**
     * Academic levels
     */
    public function get_academic_levels()
    {
        try {
            $academicLevels = $this->catalogRepository->get_academic_levels();
            if(count($academicLevels) == 0){
                return response()
                        ->json(ApiResponse::notFound('Academic levels not found', $academicLevels))
                        ->setStatusCode(404);
            }


            return response()
                    ->json(ApiResponse::success('Academic levels retrieved successfully', $academicLevels))
                    ->setStatusCode(200);

        } catch (Exception $e) {
            Log::error('Error fetching academic levels: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to retrieve academic levels', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function create_academic_level(Request $request)
    {
        $data = $request;
        try {
            $academicLevel = $this->catalogRepository->create_academic_level($data);

            return response()
                    ->json(ApiResponse::success('Academic level created successfully', $academicLevel))
                    ->setStatusCode(201);
        } catch (Exception $e) {
            Log::error('Error creating academic level: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to create academic level', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function update_academic_level(Request $request)
    {
        $data = $request;
        try {
            $academicLevel = $this->catalogRepository->update_academic_level($data);

            if(!$academicLevel){
                return response()
                        ->json(ApiResponse::notFound('Academic level not found', $academicLevel))
                        ->setStatusCode(404);
            }

            return response()
                    ->json(ApiResponse::success('Academic level updated successfully', $academicLevel))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error updating academic level: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to update academic level', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function delete_academic_level(Request $request)
    {
        $id = $request->id;
        try {
            $academicLevel = $this->catalogRepository->delete_academic_level($id);

            if(!$academicLevel){
                return response()
                        ->json(ApiResponse::notFound('Academic level not found', $academicLevel))
                        ->setStatusCode(404);
            }

            return response()
                    ->json(ApiResponse::success('Academic level deleted successfully', $academicLevel))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error deleting academic level: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to delete academic level', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    /**
     * Groups
     */
    public function get_groups(Request $request)
    {
        $scholarYearId = (int) $request->scholar_year_id;
        $academicLevelId = (int) $request->academic_level_id;
        try {
            $groups = $this->catalogRepository->get_groups($scholarYearId, $academicLevelId);

            if(count($groups) == 0){
                return response()
                        ->json(ApiResponse::notFound('Groups not found', $groups))
                        ->setStatusCode(404);
            }

            return response()
                    ->json(ApiResponse::success('Groups retrieved successfully', $groups))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error fetching groups: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to retrieve groups', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function create_group(Request $request)
    {
        $data = $request;
        try {
            $group = $this->catalogRepository->create_group($data);

            return response()
                    ->json(ApiResponse::success('Group created successfully', $group))
                    ->setStatusCode(201);
        } catch (Exception $e) {
            Log::error('Error creating group: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to create group', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function update_group(Request $request)
    {
        $data = $request;
        try {
            $group = $this->catalogRepository->update_group($data);

            if(!$group){
                return response()
                        ->json(ApiResponse::notFound('Group not found', $group))
                        ->setStatusCode(404);
            }

            return response()
                    ->json(ApiResponse::success('Group updated successfully', $group))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error updating group: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to update group', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function delete_group(Request $request)
    {
        $id = $request->id;
        try {
            $group = $this->catalogRepository->delete_group($id);
            if(!$group){
                return response()
                        ->json(ApiResponse::notFound('Group not found', $group))
                        ->setStatusCode(404);
            }

            return response()
                    ->json(ApiResponse::success('Group deleted successfully', $group))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error deleting group: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to delete group', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    /**
     * Pay Concepts
     */
    public function get_pay_concepts(Request $request)
    {
        try {
            $payConcepts = $this->catalogRepository->get_pay_concepts();
            if(count($payConcepts) == 0){
                return response()
                        ->json(ApiResponse::notFound('Pay concepts not found', $payConcepts))
                        ->setStatusCode(404);
            }

            return response()
                    ->json(ApiResponse::success('Pay concepts retrieved successfully', $payConcepts))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error fetching pay concepts: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to retrieve pay concepts', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function create_pay_concept(Request $request)
    {
        $data = $request;
        try {
            $payConcept = $this->catalogRepository->create_pay_concept($data);

            return response()
                    ->json(ApiResponse::success('Pay concept created successfully', $payConcept))
                    ->setStatusCode(201);
        } catch (Exception $e) {
            Log::error('Error creating pay concept: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to create pay concept', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function update_pay_concept(Request $request)
    {
        $data = $request;
        try {
            $payConcept = $this->catalogRepository->update_pay_concept($data);

            return response()
                    ->json(ApiResponse::success('Pay concept updated successfully', $payConcept))
                    ->setStatusCode(200);
        } catch (Exception $e) {

            Log::error('Error updating pay concept: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to update pay concept', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function get_pay_concept_prices(int $id){
        try {
            $payConceptPrices = $this->catalogRepository->get_pay_concept_prices($id);
            if(count($payConceptPrices) == 0){
                return response()
                        ->json(ApiResponse::notFound('Pay concept prices not found', $payConceptPrices))
                        ->setStatusCode(404);
            }

            return response()
                    ->json(ApiResponse::success('Pay concept prices retrieved successfully', $payConceptPrices))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            Log::error('Error fetching pay concept prices: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to retrieve pay concept prices', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function create_pay_concept_price(Request $request){
        $data = $request;

        try {
            $payConceptPrice = $this->catalogRepository->create_pay_concept_price($data);

            return response()
                    ->json(ApiResponse::success('Pay concept price created successfully', $payConceptPrice))
                    ->setStatusCode(201);
        } catch (Exception $e) {
            Log::error('Error creating pay concept price: ' . $e->getTraceAsString());
            return response()
                    ->json(ApiResponse::internalError('Failed to create pay concept price', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function clone_pay_concepts(Request $request)
    {
        $request->validate([
            'from_scholar_year_id' => 'required|exists:scholar_years,id',
            'to_scholar_year_id' => 'required|exists:scholar_years,id',
            'academic_level_id' => 'required|exists:cat_academic_levels,id',
            'increase_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            $result = $this->cloneService->clone(
                $request->from_scholar_year_id,
                $request->to_scholar_year_id,
                $request->academic_level_id,
                $request->increase_percent ?? 0
            );

            return response()
                ->json(ApiResponse::success('Pay concepts cloned successfully', $result))
                ->setStatusCode(201);
        } catch (CustomException $e) {
            return response()
                ->json(ApiResponse::badRequest($e->getMessage(), []))
                ->setStatusCode($e->getStatusCode());
        } catch (Exception $e) {
            Log::error('Error cloning pay concepts: ' . $e->getTraceAsString());
            return response()
                ->json(ApiResponse::internalError('Failed to clone pay concepts', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }

}
