<?php

namespace Src\Loyalty\DataTransferObject;

class PaymentLoyaltyPointsDTO
{
    public int $account_id;
    public int $points_rule;
    public string $description;
    public string $payment_id;
    public float $payment_amount;
    public int $payment_time;

    public function __construct(
        int    $account_id,
        int    $points_rule,
        string $description,
        string $payment_id,
        float  $payment_amount,
        int    $payment_time
    )
    {
        $this->account_id = $account_id;
        $this->points_rule = $points_rule;
        $this->description = $description;
        $this->payment_id = $payment_id;
        $this->payment_amount = $payment_amount;
        $this->payment_time = $payment_time;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['account_id'],
            $data['points_rule'],
            $data['description'],
            $data['payment_id'],
            $data['payment_amount'],
            $data['payment_time']
        );
    }

}
