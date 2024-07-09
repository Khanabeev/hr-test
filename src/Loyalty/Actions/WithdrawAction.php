<?php

namespace Src\Loyalty\Actions;


use Src\Loyalty\Models\LoyaltyPointsTransaction;

class WithdrawAction
{
    /**
     * @param $account_id
     * @param $points_amount
     * @param $description
     * @return mixed
     */
    public static function execute($account_id, $points_amount, $description): LoyaltyPointsTransaction
    {
        return LoyaltyPointsTransaction::create([
            'account_id' => $account_id,
            'points_rule' => 'withdraw',
            'points_amount' => -$points_amount,
            'description' => $description,
        ]);
    }

}
