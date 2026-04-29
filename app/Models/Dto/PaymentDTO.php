<?php

namespace App\Models\Dto;

class PaymentDTO
{
    private string $paid_at;
    private bool $has_discount;
    private float $paid_amount;
    private bool $is_full_payed;

    /**
     * @param string $paid_at
     * @param bool $has_discount
     * @param float $paid_amount
     * @param bool $is_full_payed
     */
    public function __construct(
        string $paid_at,
        bool   $has_discount,
        float  $paid_amount,
        bool   $is_full_payed
    )
    {
        $this->paid_at = $paid_at;
        $this->has_discount = $has_discount;
        $this->paid_amount = $paid_amount;
        $this->is_full_payed = $is_full_payed;
    }

    public function getPaidAt(): string
    {
        return $this->paid_at;
    }

    public function setPaidAt(string $paid_at): void
    {
        $this->paid_at = $paid_at;
    }

    public function isHasDiscount(): bool
    {
        return $this->has_discount;
    }

    public function setHasDiscount(bool $has_discount): void
    {
        $this->has_discount = $has_discount;
    }

    public function getPaidAmount(): float
    {
        return $this->paid_amount;
    }

    public function setPaidAmount(float $paid_amount): void
    {
        $this->paid_amount = $paid_amount;
    }

    public function isFullPayment(): bool
    {
        return $this->is_full_payed;
    }

    public function setFullPayment(bool $is_full_payed): void
    {
        $this->is_full_payed = $is_full_payed;
    }
}
