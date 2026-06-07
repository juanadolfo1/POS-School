<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ApiResponse;
use App\Services\DashboardService;
use Exception;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private DashboardService $service;

    public function __construct(DashboardService $service)
    {
        $this->service = $service;
    }

    public function summary(Request $request)
    {
        $request->validate([
            'scholar_year_id' => 'required|exists:scholar_years,id',
        ]);

        try {
            $summary = $this->service->getSummary((int) $request->scholar_year_id);

            return response()
                ->json(ApiResponse::success('Dashboard summary retrieved', $summary))
                ->setStatusCode(200);
        } catch (Exception $e) {
            return response()
                ->json(ApiResponse::internalError('Failed to retrieve summary', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }

    public function accountStatement(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'scholar_year_id' => 'required|exists:scholar_years,id',
        ]);

        try {
            $statement = $this->service->getStudentAccountStatement(
                (int) $request->student_id,
                (int) $request->scholar_year_id
            );

            return response()
                ->json(ApiResponse::success('Account statement retrieved', $statement))
                ->setStatusCode(200);
        } catch (Exception $e) {
            return response()
                ->json(ApiResponse::internalError('Failed to retrieve statement', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }
}
