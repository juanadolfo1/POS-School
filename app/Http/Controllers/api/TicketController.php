<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ApiResponse;
use App\Models\CustomException;
use App\Services\TicketCancelService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    private TicketCancelService $service;

    public function __construct(TicketCancelService $service)
    {
        $this->service = $service;
    }

    public function cancel(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'reason' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $ticket = $this->service->cancel(
                $request->ticket_id,
                $request->reason,
                $request->auth_user_id
            );

            DB::commit();
            return response()
                ->json(ApiResponse::success('Ticket cancelled', $ticket))
                ->setStatusCode(200);
        } catch (CustomException $e) {
            DB::rollBack();
            return response()
                ->json(ApiResponse::badRequest($e->getMessage(), []))
                ->setStatusCode($e->getStatusCode());
        } catch (Exception $e) {
            DB::rollBack();
            return response()
                ->json(ApiResponse::internalError('Failed to cancel ticket', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }

    public function cancelled(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        try {
            $tickets = $this->service->getCancelledTickets($startDate, $endDate);

            return response()
                ->json(ApiResponse::success('Cancelled tickets retrieved', $tickets))
                ->setStatusCode(200);
        } catch (Exception $e) {
            return response()
                ->json(ApiResponse::internalError('Failed to retrieve cancelled tickets', [$e->getMessage()]))
                ->setStatusCode(500);
        }
    }
}
