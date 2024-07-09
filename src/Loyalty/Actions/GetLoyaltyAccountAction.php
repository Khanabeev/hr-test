<?php

namespace Src\Loyalty\Actions;

use Illuminate\Support\Facades\Log;
use Src\Loyalty\Exceptions\AccountIsNotActiveException;
use Src\Loyalty\Exceptions\AccountNotFoundException;
use Src\Loyalty\Models\LoyaltyAccount;
use Symfony\Component\HttpFoundation\Response;

class GetLoyaltyAccountAction
{
    /**
     * @throws AccountIsNotActiveException
     * @throws AccountNotFoundException
     */
    public static function execute(string $accountType, string $accountId): LoyaltyAccount
    {
        $account = LoyaltyAccount::where($accountType, $accountId)->first();

        if (!$account) {
            Log::info('Account is not found');
            throw new AccountNotFoundException('Account not found', Response::HTTP_BAD_REQUEST);
        }

        if (!$account->active) {
            Log::info('Account is not active');
            throw new AccountIsNotActiveException('Account is not active', Response::HTTP_BAD_REQUEST);
        }

        return $account;
    }

}
