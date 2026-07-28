<?php

namespace App\Repositories;

use App\Interfaces\PaymentRepositoryInterface;
use App\Models\CatFolios;
use App\Models\CatPayConcept;
use App\Models\CatPaymentMethod;
use App\Models\CustomException;
use App\Models\Dto\CheckoutDTO;
use App\Models\Payment;
use App\Models\Scholarship;
use App\Models\Student;
use App\Models\Ticket;
use App\Models\TicketProduct;
use App\Models\TicketScholarship;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DateTimeImmutable;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function get_payments_by_student_id(int $studentId, int $year): Collection
    {
        return Payment::join('ticket_products as tp', 'tp.id', '=', 'payments.ticket_product_id')
            ->join('cat_pay_concepts as cpc', 'cpc.id', '=', 'tp.pay_concept_id')
            ->join('tickets as t', 't.id', '=', 'tp.ticket_id')
            ->join('student_groups as sg', 'sg.id', '=', 't.student_group_id')
            ->join('groups as gp', 'gp.id', '=', 'sg.group_id')
            ->where('sg.student_id', '=', $studentId)
            ->where('gp.scholar_year_id', '=', $year)
            ->where('t.is_cancelled', false)
            ->whereNull('payments.deleted_at')
            ->orderBy('payments.id', 'desc')
            ->select(
                'payments.id',
                'payments.is_full_payment',
                'payments.paid_amount',
                'payments.paid_at',
                'payments.applied_discount',
                't.folio_ticket',
                'cpc.label as pay_concept_name',
                'cpc.pay_concept_type'
            )->get();
    }

    public function get_pending_payments_by_student_id(int $studentId, int $yearId, int $academicLevelId): Collection
    {
        Log::info('Obteniendo información de pagos pendientes');
        Log::info(json_encode(['studentId' => $studentId, 'yearId' => $yearId, 'academicLevelId' => $academicLevelId]));

        //Se busca la existencia de tickets generados previamente
        $student = Student::join('student_groups as sg', 'sg.student_id', '=', 'students.id')
            ->join('groups as gp', 'gp.id', '=', 'sg.group_id')
            ->join('tickets as t', 't.student_group_id', '=', 'sg.id')
            ->where([
                ['gp.scholar_year_id', '=', $yearId],
                ['sg.student_id', '=', $studentId],
            ])->first();

        $now = date("Y-m-d");
        if ($student == null) {
            // En caso de no encontrarse se devuelve el catalogo completo
            return CatPayConcept::where('pay_concept_type', '=', 'tuition')->where('scholar_year_id', '=', $yearId)->where('id_cat_academic_level', '=', $academicLevelId)
                ->select(
                    'id',
                    'label',
                    'pay_concept_type',
                    'amount',
                    'discount_amount',
                    'last_day_with_discount',
                    DB::raw('CASE WHEN last_day_with_discount < \'' . $now . '\' THEN false ELSE true END as is_up_to_date'),
                )
                ->get();
        } else {
            // En caso de encontrar tickets, se valida la existencia de abonos sin liquidar
            $partialPayment = Payment::join('ticket_products as tp', 'payments.ticket_product_id', '=', 'tp.id')
                ->join('cat_pay_concepts as cpc', 'cpc.id', '=', 'tp.pay_concept_id')
                ->join('tickets as t', 't.id', '=', 'tp.ticket_id')
                ->join('student_groups as sg', 'sg.id', '=', 't.student_group_id')
                ->join('groups as gp', 'gp.id', '=', 'sg.group_id')
                ->where([
                    ['sg.student_id', '=', $studentId],
                    ['gp.scholar_year_id', '=', $yearId],
                    ['gp.academic_level_id', '=', $academicLevelId],
                    ['payments.is_full_payment', '=', false],
                    ['pay_concept_type', '=', 'tuition']
                ])
                ->select(
                    'cpc.id',
                    'cpc.label',
                    'cpc.pay_concept_type',
                    'cpc.amount',
                    'cpc.discount_amount',
                    'cpc.last_day_with_discount',
                    DB::raw('(cpc.amount - SUM(payments.paid_amount)) AS to_liquidate'),
                    DB::raw('CASE WHEN last_day_with_discount < \'' . $now . '\' THEN false ELSE true END as is_up_to_date'),
                )
                ->groupBy('cpc.id')->get();
            if(count($partialPayment) > 0) {
                // En caso de haber abonos, son devueltos al cliente
                return $partialPayment;
            }

            // En caso de estar al día se devuelven los pagos no cubiertos
            $sub = DB::table('ticket_products as tp')
                ->join('tickets as t', 't.id', '=', 'tp.ticket_id')
                ->join('student_groups as sg', 'sg.id', '=', 't.student_group_id')
                ->join('groups as gp', 'gp.id', '=', 'sg.group_id')
                ->where('sg.student_id', $studentId)
                ->where('gp.scholar_year_id', $yearId)
                ->where('gp.academic_level_id', $academicLevelId)
                ->select('tp.pay_concept_id', 'tp.id');

            $results = CatPayConcept::leftJoinSub($sub, 'x', function($join) {
                $join->on('x.pay_concept_id', '=', 'cat_pay_concepts.id');
            })
                ->whereNull('x.id') // los que NO tienen ticket_products para ese alumno/año/nivel
                ->where('cat_pay_concepts.pay_concept_type', 'tuition')
                ->where('cat_pay_concepts.id_cat_academic_level', '=', $academicLevelId)
                ->where('cat_pay_concepts.scholar_year_id', '=', $yearId)
                ->select(
                    'cat_pay_concepts.id',
                    'cat_pay_concepts.label',
                    'cat_pay_concepts.pay_concept_type',
                    'cat_pay_concepts.amount',
                    'cat_pay_concepts.discount_amount',
                    'cat_pay_concepts.last_day_with_discount',
                    DB::raw("CASE WHEN last_day_with_discount < '{$now}' THEN false ELSE true END as is_up_to_date")
                )
                ->get();


            return $results;
        }
    }

    public function get_all_service_payments_by_academic_level(int $yearId, int $academicLevelId): Collection
    {
        $catPayments = CatPayConcept::where([
            ['id_cat_academic_level', '=', $academicLevelId],
            ['scholar_year_id', '=', $yearId],
            ['pay_concept_type', '=', 'service'],
            ['status', '=', true],
        ])
            ->select(
                'id',
                'label',
                'amount',
                'pay_concept_type',
            )->get();

        return $catPayments;
    }

    /**
     * @throws CustomException
     */
    public function save_payment(CheckoutDTO $checkout): Ticket
    {
        Log::info('Empieza guardado de pago: ' . json_encode($checkout));

        $folioTicket = $this->get_next_folio('TK', $checkout->getScholarYearId(), $checkout->getPaymentMethod()->getId());
        Log::info(json_encode(['folioTicket' => $folioTicket]));

        Log::info('Se recupera el metodo de pago');
        $paymentMethod = CatPaymentMethod::select(
            'id',
            'name',
            'key'
        )
            ->where('id', '=', $checkout->getPaymentMethod()->getId())->firstOrFail();

        Log::info('Se genera un nuevo ticket');
        $newTicket = new Ticket();
        $newTicket->is_full_payed = $checkout->isFullPayed();
        $newTicket->amount = $checkout->getAmount();
        $newTicket->has_discount = $checkout->isHasDiscount();
        $newTicket->discount_type = $checkout->getDiscountType();
        $newTicket->discount_amount = $checkout->getDiscountAmount();
        $newTicket->folio_ticket = $folioTicket->folio;
        $newTicket->payment_method_id = $paymentMethod->id;
        $newTicket->student_group_id = $checkout->getStudentGroupId();
        $newTicket->save();
        Log::info('Ticket generado ' . json_encode($newTicket));
        Log::info('Se relacionan los conceptos de pago al ticket');
        $payConcepts = [];
        foreach ($checkout->getPayConcepts() as $payConcept) {
            $payments = array();

            $ticketProduct = new TicketProduct();
            $ticketProduct->discount = $payConcept->getDiscount();
            $ticketProduct->total = $payConcept->getAmount();
            $ticketProduct->ticket_id = $newTicket->id;
            $ticketProduct->pay_concept_id = $payConcept->getPayConceptId();
            $ticketProduct->quantity = $payConcept->getQuantity();
            $ticketProduct->save();
            Log::info('Producto generado ' . json_encode($ticketProduct));

            foreach ($payConcept->getPayments() as $payment) {
                $newPayment = new Payment();
                $newPayment->is_full_payment = $payment->isFullPayment();
                $newPayment->paid_amount = $payment->getPaidAmount();
                $newPayment->paid_at = $payment->getPaidAt();
                $newPayment->ticket_product_id = $ticketProduct->id;

                if($payment->isFullPayment()){
                    $newPayment->applied_discount = $payConcept->getLastDayWithDiscount() >= $payment->getPaidAt();
                }

                $newPayment->save();
                Log::info('Pago guardado ' . json_encode($newPayment));
                $payments[] = $newPayment;
            }
            $ticketProduct->payments = $payments;
            $payConcepts[] = $ticketProduct;
        }

        Log::info('Se formatea la respuesta');
        $newTicket->payment_method = $paymentMethod;
        $newTicket->pay_concepts = $payConcepts;

        // Guardar beca aplicada si viene en el checkout
        if ($checkout->getScholarshipId()) {
            TicketScholarship::create([
                'scholarship_id' => $checkout->getScholarshipId(),
                'ticket_id' => $newTicket->id,
            ]);
        }

        return $newTicket;
    }

    private function get_next_folio(string $key, int $scholarYearId, int $payMethodId): CatFolios
    {
        $currentTicketFolio = CatFolios::join('scholar_years AS sy', 'sy.id', '=', 'cat_folios.scholar_year_id')
            ->join('cat_payment_methods AS cpm', 'cpm.id', '=', DB::raw("'" . $payMethodId . "'"))
            ->select(
                'cat_folios.id',
                'cat_folios.counter',
                DB::raw('CONCAT(
                    cat_folios.key,
                    cpm.key,
                    to_char(sy.starts_at, \'YY\'),
                    to_char(sy.ends_at, \'YY\'),
                    LPAD(cat_folios.counter::TEXT, 5, \'0\')
                    ) AS folio')
            )
            ->where([
                ['cat_folios.key', '=', $key],
                ['scholar_year_id', '=', $scholarYearId]
            ])
            ->lockForUpdate()
            ->first();

        if ($currentTicketFolio == null) {
            $currentTicketFolio = new CatFolios();
            $currentTicketFolio->key = $key;
            $currentTicketFolio->counter = 1;
            $currentTicketFolio->scholar_year_id = $scholarYearId;
            $currentTicketFolio->save();

            return $this->get_next_folio($key, $scholarYearId, $payMethodId);
        } else {
            $currentTicketFolio->counter = $currentTicketFolio->counter + 1;
            $currentTicketFolio->save();
        }


        return $currentTicketFolio;
    }

    public function get_payment_methods():Collection
    {
        return CatPaymentMethod::select('id', 'name', 'key')->get();
    }

    public function get_scholarship(int $student_id, int $scholar_year_id): ?Scholarship
    {
        return Scholarship::where([
            ['student_id', '=', $student_id],
            ['scholar_year_id', '=', $scholar_year_id]
        ])
        ->select(
            'id',
            'name',
            'amount'
        )
        ->first();
    }

    /**
     * @throws Exception
     */
    public function get_close_ticket(string $selectedDay): Collection
    {
        $parsedSelectedDay = new DateTimeImmutable($selectedDay);
        $startingDay = $parsedSelectedDay->setTime(0, 0, 0);
        $endingDay = $parsedSelectedDay->setTime(23, 59, 59);
        $tickets = Ticket::join('cat_payment_methods AS cpm', 'cpm.id', '=', 'tickets.payment_method_id')
            ->join('student_groups AS sg', 'sg.id', '=', 'tickets.student_group_id')
            ->join('students AS s', 's.id', '=', 'sg.student_id')
            ->join('people AS p', 'p.id', '=', 's.person_id')
        ->where([
            ['tickets.created_at', '>=', $startingDay],
            ['tickets.created_at', '<=', $endingDay]
        ])
        ->select(
            'tickets.id',
            'tickets.amount',
            'tickets.has_discount',
            'tickets.discount_amount',
            'tickets.folio_ticket',
            'cpm.name AS payment_method',
            DB::raw('CONCAT_WS(\' \', p.name, p.first_lastname, p.second_lastname) AS complete_name')
        )->get();

        $ticketIds = $tickets->map(function ($ticket) {
            return $ticket->id;
        })->toArray();

        $payedProducts = TicketProduct::join('cat_pay_concepts AS cpc', 'cpc.id', '=', 'ticket_products.pay_concept_id')
            ->join('payments AS p', 'p.ticket_product_id', '=', 'ticket_products.id')
            ->whereIn('ticket_products.ticket_id', $ticketIds)
            ->select(
                'ticket_products.id',
                'p.paid_amount',
                'cpc.label AS product_name',
                'ticket_products.ticket_id',
            )->get();

        foreach ($tickets as $ticket) {
            $ticket->products = $payedProducts->filter(function ($product) use ($ticket) {
                return $product->ticket_id == $ticket->id;
            });
        }

        return $tickets;
    }
}
