<?php

namespace Src\Loyalty\Actions;

use Illuminate\Support\Facades\Log;
use Src\Loyalty\Exceptions\BalanceException;
use Src\Loyalty\Exceptions\LoyaltyPointsException;
use Symfony\Component\HttpFoundation\Response;

class CheckLoyaltyPointAction
{
    /**
     * @throws BalanceException
     * @throws LoyaltyPointsException
     */
    public static function execute(int $accountId, float $pointsAmount): void
    {
        $balance = GetBalanceAction::execute($accountId);

        if ($pointsAmount <= 0) {
            Log::info('Wrong loyalty points amount: ' . $pointsAmount);

            throw new LoyaltyPointsException(
                'Wrong loyalty points amount',
                Response::HTTP_BAD_REQUEST
            );
        }
        if ($balance < $pointsAmount) {
            Log::info('Insufficient funds: ' . $pointsAmount);

            throw new BalanceException(
                'Insufficient funds',
                Response::HTTP_BAD_REQUEST
            );
        }
    }

}
