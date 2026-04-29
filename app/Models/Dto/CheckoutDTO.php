<?php

namespace App\Models\Dto;

class CheckoutDTO
{
    private ?int $ticket_id;
    private bool $is_full_payed;
    private float $amount;
    private bool $has_discount;
    private ?string $discount_type;
    private float $discount_amount;
    private ?string $folio_ticket;
    private int $student_group_id;
    private int $scholar_year_id;
    private PaymentMethodDTO $payment_method;
    /** @var PayConceptDTO[] */
    private array $pay_concepts;

    /**
     * @param bool $is_full_payed
     * @param float $amount
     * @param bool $has_discount
     * @param ?string $discount_type
     * @param float $discount_amount
     * @param int $student_group_id
     * @param int $scholar_year_id
     * @param PaymentMethodDTO $payment_method
     * @param PayConceptDTO[] $pay_concepts
     * @param ?int $ticket_id
     * @param ?string $folio_ticket
     */
    public function __construct(
        bool $is_full_payed,
        float $amount,
        bool $has_discount,
        ?string $discount_type,
        float $discount_amount,
        int $student_group_id,
        int $scholar_year_id,
        PaymentMethodDTO $payment_method,
        array $pay_concepts,
        int $ticket_id = null,
        string $folio_ticket = null
    )
    {
        $this->is_full_payed = $is_full_payed;
        $this->amount = $amount;
        $this->has_discount = $has_discount;
        $this->discount_type = $discount_type;
        $this->discount_amount = $discount_amount;
        $this->student_group_id = $student_group_id;
        $this->scholar_year_id = $scholar_year_id;
        $this->payment_method = $payment_method;
        $this->pay_concepts = $pay_concepts;
        $this->ticket_id = $ticket_id;
        $this->folio_ticket = $folio_ticket;
    }

    public static function from_request($request): CheckoutDTO
    {
        $paymentMethod = new PaymentMethodDTO($request->payment_method["id"]);

        $payConcepts = array_map(function ($concept){
            $payments = array_map(function ($payment){
                return new PaymentDTO(
                    $payment["paid_at"],
                    $payment["has_discount"],
                    $payment["received_payment"],
                    $payment["is_full_payed"],
                );
            }, $concept["payments"]);

            return new PayConceptDTO(
                $concept["pay_concept_id"],
                $concept["pay_concept_name"],
                $concept["pay_concept_type"],
                $concept["amount"],
                $concept["last_day_with_discount"] ?? '',
                $concept["discount"],
                $concept["scholar_year_id"],
                $payments,
                $concept["quantity"] ?? 1
            );
        }, $request->pay_concepts);

        return new CheckoutDTO(
            $request->is_full_payed,
            $request->amount,
            $request->has_discount,
            $request->discount_type,
            $request->discount_amount,
            $request->student_group_id,
            $request->scholar_year_id,
            $paymentMethod,
            $payConcepts,
        );
    }

    public function getTicketId(): int
    {
        return $this->ticket_id;
    }

    public function setTicketId(int $ticket_id): void
    {
        $this->ticket_id = $ticket_id;
    }

    public function isFullPayed(): bool
    {
        return $this->is_full_payed;
    }

    public function setIsFullPayed(bool $is_full_payed): void
    {
        $this->is_full_payed = $is_full_payed;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function isHasDiscount(): bool
    {
        return $this->has_discount;
    }

    public function setHasDiscount(bool $has_discount): void
    {
        $this->has_discount = $has_discount;
    }

    public function getDiscountType(): null | string
    {
        return $this->discount_type;
    }

    public function setDiscountType(string $discount_type): void
    {
        $this->discount_type = $discount_type;
    }

    public function getDiscountAmount(): float
    {
        return $this->discount_amount;
    }

    public function setDiscountAmount(float $discount_amount): void
    {
        $this->discount_amount = $discount_amount;
    }

    public function getFolioTicket(): string
    {
        return $this->folio_ticket;
    }

    public function setFolioTicket(string $folio_ticket): void
    {
        $this->folio_ticket = $folio_ticket;
    }

    public function getStudentGroupId(): int
    {
        return $this->student_group_id;
    }

    public function setStudentGroupId(int $student_group_id): void
    {
        $this->student_group_id = $student_group_id;
    }

    public function getScholarYearId(): int
    {
        return $this->scholar_year_id;
    }

    public function setScholarYearId(int $scholar_year_id): void
    {
        $this->scholar_year_id = $scholar_year_id;
    }

    public function getPaymentMethod(): PaymentMethodDTO
    {
        return $this->payment_method;
    }

    public function setPaymentMethod(PaymentMethodDTO $payment_method): void
    {
        $this->payment_method = $payment_method;
    }

    public function getPayConcepts(): array
    {
        return $this->pay_concepts;
    }

    public function setPayConcepts(array $pay_concepts): void
    {
        $this->pay_concepts = $pay_concepts;
    }
}
