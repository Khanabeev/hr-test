<?php

namespace Src\Loyalty\Actions;

use Src\Loyalty\Models\LoyaltyPointsTransaction;

class GetBalanceAction
{
    public static function execute(int $accountId): float
    {
        return LoyaltyPointsTransaction::where('canceled', '=', 0)
            ->where('account_id', '=',$accountId)
            ->sum('points_amount');
    }

}
