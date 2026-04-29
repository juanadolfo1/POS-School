<?php

namespace App\Models\Dto;

class PayConceptDTO
{

    private int $pay_concept_id;
    private string $pay_concept_name;
    private string $pay_concept_type;
    private int $quantity;
    private float $amount;
    private string $last_day_with_discount;
    private float $discount;
    private int $scholar_year_id;
    /** @var PaymentDTO[] */
    private array $payments;

    /**
     * @param int $pay_concept_id
     * @param string $pay_concept_name
     * @param string $pay_concept_type
     * @param float $amount
     * @param string $last_day_with_discount
     * @param float $discount
     * @param int $scholar_year_id
     * @param ?int $quantity
     * @param ?PaymentDTO[] $payments
     */
    public function __construct(
        int    $pay_concept_id,
        string $pay_concept_name,
        string $pay_concept_type,
        float $amount,
        string $last_day_with_discount,
        float $discount,
        int $scholar_year_id,
        ?array $payments = null,
        ?int $quantity = 1
    )
    {
        $this->pay_concept_id = $pay_concept_id;
        $this->pay_concept_name = $pay_concept_name;
        $this->pay_concept_type = $pay_concept_type;
        $this->amount = $amount;
        $this->last_day_with_discount = $last_day_with_discount;
        $this->discount = $discount;
        $this->scholar_year_id = $scholar_year_id;
        $this->payments = $payments;
        $this->quantity = $quantity;
    }

    public static function from_JSON($pay_concept): PayConceptDTO
    {
        return new PayConceptDTO(
            $pay_concept->pay_concept_id,
            $pay_concept->pay_concept_name,
            $pay_concept->type,
            $pay_concept->amount,
            $pay_concept->last_day_with_discount,
            $pay_concept->discount,
            $pay_concept->scholar_year_id,
        );
    }

    public function getPayConceptId(): int
    {
        return $this->pay_concept_id;
    }

    public function setPayConceptId(int $pay_concept_id): void
    {
        $this->pay_concept_id = $pay_concept_id;
    }

    public function getPayConceptName(): string
    {
        return $this->pay_concept_name;
    }

    public function setPayConceptName(string $pay_concept_name): void
    {
        $this->pay_concept_name = $pay_concept_name;
    }

    public function getPayConceptType(): string
    {
        return $this->pay_concept_type;
    }

    public function setPayConceptType(string $pay_concept_type): void
    {
        $this->pay_concept_type = $pay_concept_type;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getLastDayWithDiscount(): string
    {
        return $this->last_day_with_discount;
    }

    public function setLastDayWithDiscount(string $last_day_with_discount): void
    {
        $this->last_day_with_discount = $last_day_with_discount;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function setDiscount(float $discount): void
    {
        $this->discount = $discount;
    }

    public function getScholarYearId(): int
    {
        return $this->scholar_year_id;
    }

    public function setScholarYearId(int $scholar_year_id): void
    {
        $this->scholar_year_id = $scholar_year_id;
    }

    /** @returns PaymentDTO[] */
    public function getPayments(): array
    {
        return $this->payments;
    }

    public function setPayments(array $payments): void
    {
        $this->payments = $payments;
    }
}
