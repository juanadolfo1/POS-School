<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Interfaces\PaymentRepositoryInterface;
use App\Models\ApiResponse;
use App\Models\CustomException;
use App\Models\Dto\CheckoutDTO;
use App\Services\PromotionService;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected PaymentRepositoryInterface $repository;
    protected PromotionService $promotionService;

    public function __construct(PaymentRepositoryInterface $paymentRepository, PromotionService $promotionService){
        $this->repository = $paymentRepository;
        $this->promotionService = $promotionService;
    }

    public function get_payments_by_student_id(Request $request){
       $studentId = (int) $request->query('student_id');
       $yearId = (int) $request->query('year_id');

       try{
        $payments = $this->repository->get_payments_by_student_id($studentId, $yearId);

        if(count($payments) == 0){
            return response()
                    ->json(ApiResponse::notFound('Payments not found', $payments))
                    ->setStatusCode(404);
        }

        return response()
                ->json(ApiResponse::success('Payments retrieved successfully', $payments))
                ->setStatusCode(200);
       } catch (Exception $e) {

        return response()
                ->json(ApiResponse::internalError('Failed to retrieve payments', [$e->getMessage()]))
                ->setStatusCode(500);
       }
    }

    public function get_pending_payments_by_student_id(Request $request){
        $request->validate([
            'student_id' => 'required|integer',
            'year_id' => 'required|integer',
            'academic_level_id' => 'required|integer',
        ]);

        $studentId = (int) $request->student_id;
        $yearId = (int) $request->year_id;
        $academicLevelId = (int) $request->academic_level_id;

        try{
            $payments = $this->repository->get_pending_payments_by_student_id($studentId, $yearId, $academicLevelId);
            $paymentMethods = $this->repository->get_payment_methods();
            $scholarship = $this->repository->get_scholarship($studentId, $yearId);

            if(count($payments) == 0){
                return response()
                        ->json(ApiResponse::notFound('There is no pending payments', $payments))
                        ->setStatusCode(404);
            }

            $currentMonth = (int) (new DateTime())->format('n');
            $firstPaymentMonth = (int) (new DateTime($payments[0]->last_day_with_discount))->format('n');

            $isEligibleForPromotion = $this->promotionService->isEligibleForDiscount($studentId, $yearId, $academicLevelId);

            return response()
                    ->json(ApiResponse::success('Pending payments retrieved successfully', [
                        'payments' => $payments,
                        'paymentMethods' => $paymentMethods,
                        'scholarship' => $scholarship,
                        'is_up_to_date' => $firstPaymentMonth >= $currentMonth,
                        'promotion_eligible' => $isEligibleForPromotion
                    ]))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            return response()
                    ->json(ApiResponse::internalError('Failed to retrieve pending payments', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function get_all_service_payments_by_academic_level(Request $request){
        $request->validate([
            'year_id' => 'required|integer',
            'academic_level_id' => 'required|integer',
        ]);

        $yearId = (int) $request->year_id;
        $academicLevelId = (int) $request->academic_level_id;
        try{
            $payments = $this->repository->get_all_service_payments_by_academic_level($yearId, $academicLevelId);
            $paymentMethods = $this->repository->get_payment_methods();

            return response()
                    ->json(ApiResponse::success('Service payments retrieved successfully', [
                        'payments' => $payments,
                        'paymentMethods' => $paymentMethods,
                    ]))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            return response()
                    ->json(ApiResponse::internalError('Failed to retrieve service payments', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function save_payment(Request $request){
        $payment = CheckoutDTO::from_request($request);
        DB::beginTransaction();
        try {
            $newPayment = $this->repository->save_payment($payment);
            DB::commit();
            return response()
                    ->json(ApiResponse::success('Payment saved successfully', $newPayment))
                    ->setStatusCode(201);
        } catch (CustomException $e){
            DB::rollBack();
            return response()
                    ->json(ApiResponse::badRequest('Something went wrong', [$e->getMessage()]))
                    ->setStatusCode($e->getStatusCode());
        } catch (Exception $e) {
            DB::rollBack();
            return response()
                    ->json(ApiResponse::internalError('Failed to save payment', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function daily_income(Request $request){
        $startDate = $request->query('start_date', now()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());

        try {
            $byLevel = DB::table('payments')
                ->join('ticket_products', 'payments.ticket_product_id', '=', 'ticket_products.id')
                ->join('tickets', 'ticket_products.ticket_id', '=', 'tickets.id')
                ->join('student_groups', 'tickets.student_group_id', '=', 'student_groups.id')
                ->join('groups', 'student_groups.group_id', '=', 'groups.id')
                ->join('cat_academic_levels', 'groups.academic_level_id', '=', 'cat_academic_levels.id')
                ->whereBetween('payments.paid_at', [$startDate, $endDate])
                ->whereNull('payments.deleted_at')
                ->whereNull('ticket_products.deleted_at')
                ->whereNull('tickets.deleted_at')
                ->groupBy('cat_academic_levels.id', 'cat_academic_levels.label')
                ->select(
                    'cat_academic_levels.id as academic_level_id',
                    'cat_academic_levels.label as academic_level_name',
                    DB::raw('SUM(payments.paid_amount) as total_income'),
                    DB::raw('COUNT(DISTINCT student_groups.student_id) as total_students_paid'),
                    DB::raw("COUNT(DISTINCT CASE WHEN tickets.has_discount = true AND tickets.discount_type = 'early_payment' THEN student_groups.student_id END) as promotion_eligible_count")
                )
                ->get()
                ->map(function ($row) {
                    return [
                        'academic_level_id' => (int) $row->academic_level_id,
                        'academic_level_name' => $row->academic_level_name,
                        'total_income' => round((float) $row->total_income, 2),
                        'total_students_paid' => (int) $row->total_students_paid,
                        'promotion_eligible_count' => (int) $row->promotion_eligible_count,
                    ];
                });

            $data = [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_income' => round($byLevel->sum('total_income'), 2),
                'total_transactions' => DB::table('payments')->whereBetween('paid_at', [$startDate, $endDate])->whereNull('deleted_at')->count(),
                'promotion_eligible_total' => $byLevel->sum('promotion_eligible_count'),
                'by_level' => $byLevel->values()->toArray(),
            ];

            return response()->json(['status' => 'success', 'message' => 'ok', 'data' => $data], 200);
        } catch (Exception $e) {
            return response()
                ->json(ApiResponse::internalError('Failed to retrieve daily income', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }

    public function check_promotion(Request $request){
        $studentId = (int) $request->query('student_id');
        $scholarYearId = (int) $request->query('scholar_year_id');
        $academicLevelId = (int) $request->query('academic_level_id');

        try {
            $eligible = $this->promotionService->isEligibleForDiscount($studentId, $scholarYearId, $academicLevelId);
            return response()
                    ->json(ApiResponse::success('Promotion status retrieved', ['eligible' => $eligible]))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            return response()
                    ->json(ApiResponse::internalError('Failed to check promotion', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }
}
