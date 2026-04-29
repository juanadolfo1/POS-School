<?php

namespace App\Interfaces;

use App\Models\CatPayConcept;
use App\Models\CatPaymentMethod;
use App\Models\Dto\CheckoutDTO;
use App\Models\Payment;
use App\Models\Scholarship;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Collection;

interface PaymentRepositoryInterface{
    /** @return Payment[] */
    function get_payments_by_student_id(int $studentId, int $yearId): Collection;
    /** @return CatPayConcept[] */
    function get_pending_payments_by_student_id(int $studentId, int $yearId, int $academicLevelId): Collection;
    /** @return Payment[] */
    function get_all_service_payments_by_academic_level(int $yearId, int $academicLevelId): Collection;
    /** @return CatPaymentMethod[] */
    function get_payment_methods():Collection;
    function get_scholarship(int $student_id, int $scholar_year_id): ?Scholarship;
    function save_payment(CheckoutDTO $checkout): Ticket;
    /** @return Ticket[] */
    function get_close_ticket(string $selectedDay): Collection;
}
