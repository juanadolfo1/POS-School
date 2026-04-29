<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Interfaces\PaymentRepositoryInterface;
use App\Models\ApiResponse;
use App\Models\CustomException;
use App\Models\Dto\CheckoutDTO;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected PaymentRepositoryInterface $repository;

    public function __construct(PaymentRepositoryInterface $paymentRepository){
        $this->repository = $paymentRepository;
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

            return response()
                    ->json(ApiResponse::success('Pending payments retrieved successfully', [
                        'payments' => $payments,
                        'paymentMethods' => $paymentMethods,
                        'scholarship' => $scholarship,
                        'is_up_to_date' => $firstPaymentMonth >= $currentMonth
                    ]))
                    ->setStatusCode(200);
        } catch (Exception $e) {
            return response()
                    ->json(ApiResponse::internalError('Failed to retrieve pending payments', [$e->getMessage()]))
                    ->setStatusCode(500);
        }
    }

    public function get_all_service_payments_by_academic_level(Request $request){
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

}
