<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ApiResponse;
use App\Models\CustomException;
use App\Services\WithdrawalService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    private WithdrawalService $service;

    public function __construct(WithdrawalService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $scholarYearId = $request->query('scholar_year_id');

        try {
            $withdrawals = $this->service->getWithdrawals($scholarYearId ? (int) $scholarYearId : null);

            return response()
                ->json(ApiResponse::success('Withdrawals retrieved', $withdrawals))
                ->setStatusCode(200);
        } catch (Exception $e) {
            return response()
                ->json(ApiResponse::internalError('Failed to retrieve withdrawals', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'scholar_year_id' => 'required|exists:scholar_years,id',
            'type' => 'required|in:temporal,definitiva',
            'reason' => 'required|string|max:255',
            'effective_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $withdrawal = $this->service->withdraw(
                $request->student_id,
                $request->scholar_year_id,
                $request->type,
                $request->reason,
                $request->effective_date
            );

            DB::commit();
            return response()
                ->json(ApiResponse::success('Student withdrawal registered', $withdrawal))
                ->setStatusCode(201);
        } catch (CustomException $e) {
            DB::rollBack();
            return response()
                ->json(ApiResponse::badRequest($e->getMessage(), []))
                ->setStatusCode($e->getStatusCode());
        } catch (Exception $e) {
            DB::rollBack();
            return response()
                ->json(ApiResponse::internalError('Failed to register withdrawal', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }

    public function reactivate(Request $request, int $id)
    {
        DB::beginTransaction();
        try {
            $withdrawal = $this->service->reactivate($id);

            DB::commit();
            return response()
                ->json(ApiResponse::success('Student reactivated', $withdrawal))
                ->setStatusCode(200);
        } catch (CustomException $e) {
            DB::rollBack();
            return response()
                ->json(ApiResponse::badRequest($e->getMessage(), []))
                ->setStatusCode($e->getStatusCode());
        } catch (Exception $e) {
            DB::rollBack();
            return response()
                ->json(ApiResponse::internalError('Failed to reactivate student', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }
}
