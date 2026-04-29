<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Interfaces\PaymentRepositoryInterface;
use App\Models\ApiResponse;
use App\Repositories\DocumentRepository;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    private DocumentRepository $repository;
    private PaymentRepositoryInterface $paymentRepository;

    public function __construct(
        DocumentRepository $documentRepository,
        PaymentRepositoryInterface $paymentRepository
    )
    {
        $this->repository = $documentRepository;
        $this->paymentRepository = $paymentRepository;
    }

    public function get_ticket(string $folioTicket)
    {
        $ticket = $this->repository->get_ticket($folioTicket);
        return response($ticket)->header('Content-Type', 'application/pdf');
    }

    public function close_ticket(string $selectedDay)
    {
        $tickets = $this->paymentRepository->get_close_ticket($selectedDay);

        $html = $this->repository->close_ticket($tickets, $selectedDay);
        $pdf = $this->repository->get_pdf($html, 'letter');

        return response($pdf)->header('Content-Type', 'application/pdf');
    }

    public function get_pending_payments_report(Request $request)
    {
        $returnFile = boolval($request->query('return-file'));

        $pendingPaymentReport = $this->repository->get_pending_payment_report();

        if($returnFile){
            $csvFile = $this->repository->generate_csv_report($pendingPaymentReport);
            $filename = 'payments-report-' . date('d-m-Y') . '.csv';
            return response($csvFile)->header('Content-Type', 'text/csv')->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
        }

        return response()
                ->json(ApiResponse::success('Got report successfully', $pendingPaymentReport))
                ->setStatusCode(200);
    }
}
