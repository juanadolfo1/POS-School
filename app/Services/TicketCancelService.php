<?php

namespace App\Services;

use App\Models\CustomException;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\TicketProduct;
use Illuminate\Support\Facades\DB;

class TicketCancelService
{
    private AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Cancela un ticket:
     * - Marca el ticket como cancelado
     * - Soft-delete de los payments asociados
     * - Soft-delete de los ticket_products
     * - Registra auditoría
     */
    public function cancel(int $ticketId, string $reason, int $userId): Ticket
    {
        $ticket = Ticket::findOrFail($ticketId);

        if ($ticket->is_cancelled) {
            throw new CustomException('Este ticket ya está cancelado', 409);
        }

        // Soft-delete payments del ticket
        $productIds = TicketProduct::where('ticket_id', $ticketId)->pluck('id');
        Payment::whereIn('ticket_product_id', $productIds)->delete();

        // Soft-delete ticket_products
        TicketProduct::where('ticket_id', $ticketId)->delete();

        // Marcar ticket como cancelado (no soft-delete, para conservar folio)
        $ticket->update([
            'is_cancelled' => true,
            'cancel_reason' => $reason,
            'cancelled_at' => now(),
            'cancelled_by' => $userId,
        ]);

        // Auditoría
        $this->auditService->log($userId, 'cancel', 'ticket', $ticketId, [
            'folio' => $ticket->folio_ticket,
            'amount' => $ticket->amount,
            'reason' => $reason,
        ]);

        return $ticket->fresh();
    }

    /**
     * Obtiene tickets cancelados para consulta.
     */
    public function getCancelledTickets(?string $startDate = null, ?string $endDate = null)
    {
        $query = Ticket::join('student_groups as sg', 'sg.id', '=', 'tickets.student_group_id')
            ->join('students as s', 's.id', '=', 'sg.student_id')
            ->join('people as p', 'p.id', '=', 's.person_id')
            ->where('tickets.is_cancelled', true)
            ->select(
                'tickets.id',
                'tickets.folio_ticket',
                'tickets.amount',
                'tickets.cancel_reason',
                'tickets.cancelled_at',
                DB::raw("CONCAT_WS(' ', p.name, p.first_lastname, p.second_lastname) as student_name")
            )
            ->orderBy('tickets.cancelled_at', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('tickets.cancelled_at', [$startDate, $endDate]);
        }

        return $query->get();
    }
}
