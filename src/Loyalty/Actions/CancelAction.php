<?php

namespace Src\Loyalty\Actions;

use Src\Loyalty\Exceptions\TransactionNotFoundException;
use Src\Loyalty\Models\LoyaltyPointsTransaction;
use Symfony\Component\HttpFoundation\Response;

class CancelAction
{
    /**
     * @throws TransactionNotFoundException
     */
    public static function execute(int $transactionId, string $reason): LoyaltyPointsTransaction
    {
        $transaction = LoyaltyPointsTransaction::where('id', '=', $transactionId)
            ->where('canceled', '=', 0)
            ->first();

        if (!$transaction) {
            throw new TransactionNotFoundException(
                'Transaction is not found',
                Response::HTTP_BAD_REQUEST
            );
        }

        $transaction->canceled = time();
        $transaction->cancellation_reason = $reason;
        $transaction->save();

        return $transaction;
    }

}
