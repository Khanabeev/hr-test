<?php

namespace Src\Loyalty\Actions;

use Illuminate\Support\Facades\Log;
use Src\Loyalty\DataTransferObject\PaymentLoyaltyPointsDTO;
use Src\Loyalty\Models\LoyaltyPointsRule;
use Src\Loyalty\Models\LoyaltyPointsTransaction;

class DepositAction
{
    public static function execute(PaymentLoyaltyPointsDTO $dto): LoyaltyPointsTransaction
    {
        $points_amount = 0;

        $pointsRule = LoyaltyPointsRule::where('points_rule', '=', $dto->points_rule)->first();

        if ($pointsRule) {
            $points_amount = match ($pointsRule->accrual_type) {
                LoyaltyPointsRule::ACCRUAL_TYPE_RELATIVE_RATE => ($dto->payment_amount / 100) * $pointsRule->accrual_value,
                LoyaltyPointsRule::ACCRUAL_TYPE_ABSOLUTE_POINTS_AMOUNT => $pointsRule->accrual_value
            };
        }

        $transaction = LoyaltyPointsTransaction::create([
            'account_id' => $dto->account_id,
            'points_rule' => $pointsRule?->id,
            'points_amount' => $points_amount,
            'description' => $dto->description,
            'payment_id' => $dto->payment_id,
            'payment_amount' => $dto->payment_amount,
            'payment_time' => $dto->payment_time,
        ]);

        Log::info($transaction);

        return $transaction;
    }

}
