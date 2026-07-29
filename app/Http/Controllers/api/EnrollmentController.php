<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ApiResponse;
use App\Models\CustomException;
use App\Models\GradePromotion;
use App\Services\AuditService;
use App\Services\EnrollmentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    private EnrollmentService $service;
    private AuditService $auditService;

    public function __construct(EnrollmentService $service, AuditService $auditService)
    {
        $this->service = $service;
        $this->auditService = $auditService;
    }

    /**
     * Vista previa de la reinscripción masiva (no modifica BD).
     */
    public function preview(Request $request)
    {
        $request->validate([
            'from_scholar_year_id' => 'required|exists:scholar_years,id',
            'to_scholar_year_id' => 'required|exists:scholar_years,id',
            'academic_level_id' => 'required|exists:cat_academic_levels,id',
        ]);

        try {
            $preview = $this->service->previewBatch(
                $request->from_scholar_year_id,
                $request->to_scholar_year_id,
                $request->academic_level_id
            );

            return response()
                ->json(ApiResponse::success('Enrollment preview generated', $preview))
                ->setStatusCode(200);
        } catch (Exception $e) {
            return response()
                ->json(ApiResponse::internalError('Failed to generate preview', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }

    /**
     * Ejecuta la reinscripción masiva.
     */
    public function promoteBatch(Request $request)
    {
        $request->validate([
            'from_scholar_year_id' => 'required|exists:scholar_years,id',
            'to_scholar_year_id' => 'required|exists:scholar_years,id',
            'academic_level_id' => 'required|exists:cat_academic_levels,id',
        ]);

        DB::beginTransaction();
        try {
            $result = $this->service->promoteBatch(
                $request->from_scholar_year_id,
                $request->to_scholar_year_id,
                $request->academic_level_id
            );

            DB::commit();
            $this->auditService->log(
                $request->auth_user_id ?? null,
                'ENROLLMENT_BATCH',
                'enrollment_processes',
                $result['process_id'] ?? null,
                ['from' => $request->from_scholar_year_id, 'to' => $request->to_scholar_year_id, 'level' => $request->academic_level_id]
            );
            return response()
                ->json(ApiResponse::success('Enrollment process completed', $result))
                ->setStatusCode(201);
        } catch (CustomException $e) {
            DB::rollBack();
            return response()
                ->json(ApiResponse::badRequest($e->getMessage(), []))
                ->setStatusCode($e->getStatusCode());
        } catch (Exception $e) {
            DB::rollBack();
            return response()
                ->json(ApiResponse::internalError('Failed to execute enrollment', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }

    /**
     * Reinscripción individual de un alumno.
     */
    public function promoteStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'from_scholar_year_id' => 'required|exists:scholar_years,id',
            'to_scholar_year_id' => 'required|exists:scholar_years,id',
            'academic_level_id' => 'required|exists:cat_academic_levels,id',
        ]);

        DB::beginTransaction();
        try {
            $result = $this->service->promoteStudent(
                $request->student_id,
                $request->from_scholar_year_id,
                $request->to_scholar_year_id,
                $request->academic_level_id
            );

            DB::commit();
            return response()
                ->json(ApiResponse::success('Student promoted', $result))
                ->setStatusCode(201);
        } catch (CustomException $e) {
            DB::rollBack();
            return response()
                ->json(ApiResponse::badRequest($e->getMessage(), []))
                ->setStatusCode($e->getStatusCode());
        } catch (Exception $e) {
            DB::rollBack();
            return response()
                ->json(ApiResponse::internalError('Failed to promote student', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }

    /**
     * Historial de procesos de reinscripción.
     */
    public function history()
    {
        try {
            $history = $this->service->getProcessHistory();

            return response()
                ->json(ApiResponse::success('Enrollment history retrieved', $history))
                ->setStatusCode(200);
        } catch (Exception $e) {
            return response()
                ->json(ApiResponse::internalError('Failed to retrieve history', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }

    // ========== CRUD de Grade Promotions (configuración de grados) ==========

    public function getGradePromotions(Request $request)
    {
        $academicLevelId = $request->query('academic_level_id');

        $query = GradePromotion::with('academicLevel');

        if ($academicLevelId) {
            $query->where('academic_level_id', $academicLevelId);
        }

        return response()
            ->json(ApiResponse::success('Grade promotions retrieved', $query->get()))
            ->setStatusCode(200);
    }

    public function storeGradePromotion(Request $request)
    {
        $request->validate([
            'academic_level_id' => 'required|exists:cat_academic_levels,id',
            'from_grade' => 'required|string|max:6',
            'to_grade' => 'required|string|max:6',
            'is_final_grade' => 'required|boolean',
        ]);

        $promotion = GradePromotion::create($request->only([
            'academic_level_id', 'from_grade', 'to_grade', 'is_final_grade'
        ]));

        return response()
            ->json(ApiResponse::success('Grade promotion created', $promotion))
            ->setStatusCode(201);
    }

    public function updateGradePromotion(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:grade_promotions,id',
            'from_grade' => 'required|string|max:6',
            'to_grade' => 'required|string|max:6',
            'is_final_grade' => 'required|boolean',
        ]);

        $promotion = GradePromotion::findOrFail($request->id);
        $promotion->update($request->only(['from_grade', 'to_grade', 'is_final_grade']));

        return response()
            ->json(ApiResponse::success('Grade promotion updated', $promotion))
            ->setStatusCode(200);
    }

    public function deleteGradePromotion(int $id)
    {
        GradePromotion::findOrFail($id)->delete();

        return response()
            ->json(ApiResponse::success('Grade promotion deleted', []))
            ->setStatusCode(200);
    }
}
