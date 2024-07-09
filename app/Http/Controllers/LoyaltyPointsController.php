<?php

namespace App\Http\Controllers;

use App\Http\Requests\CancelRequest;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\WithdrawRequest;
use App\Http\Resources\LoyaltyPointsTransactionResource;
use Illuminate\Support\Facades\Log;
use Src\Loyalty\Actions\CancelAction;
use Src\Loyalty\Actions\CheckLoyaltyPointAction;
use Src\Loyalty\Actions\GetLoyaltyAccountAction;
use Src\Loyalty\Actions\DepositAction;
use Src\Loyalty\Actions\SendNotificationAction;
use Src\Loyalty\Actions\WithdrawAction;
use Src\Loyalty\DataTransferObject\PaymentLoyaltyPointsDTO;
use Src\Loyalty\Exceptions\AccountIsNotActiveException;
use Src\Loyalty\Exceptions\AccountNotFoundException;
use Src\Loyalty\Exceptions\BalanceException;
use Src\Loyalty\Exceptions\LoyaltyPointsException;
use Src\Loyalty\Exceptions\TransactionNotFoundException;
use Src\Loyalty\Models\LoyaltyPointsTransaction;

class LoyaltyPointsController extends Controller
{
    /**
     * @param DepositRequest $request
     * @return LoyaltyPointsTransactionResource
     * @throws AccountIsNotActiveException
     * @throws AccountNotFoundException
     */
    public function deposit(DepositRequest $request): LoyaltyPointsTransactionResource
    {
        $data = $request->validated();

        Log::info('Deposit transaction input: ' . print_r($data, true));

        $account = GetLoyaltyAccountAction::execute(
            $data['account_type'],
            $data['account_id']
        );

        $dto = PaymentLoyaltyPointsDTO::fromArray($data);
        $transaction = DepositAction::execute($dto);

        SendNotificationAction::execute($account, $transaction);

        return new LoyaltyPointsTransactionResource($transaction);
    }

    /**
     * @throws TransactionNotFoundException
     */
    public function cancel(CancelRequest $request): void
    {
        $data = $request->validated();

        CancelAction::execute(
            $data['transaction_id'],
            $data['cancellation_reason']
        );
    }

    /**
     * @throws AccountIsNotActiveException
     * @throws AccountNotFoundException
     * @throws LoyaltyPointsException
     * @throws BalanceException
     */
    public function withdraw(WithdrawRequest $request): LoyaltyPointsTransactionResource
    {
        $data = $request->validated();

        Log::info('Withdraw loyalty points transaction input: ' . print_r($data, true));

        $account = GetLoyaltyAccountAction::execute(
            $data['account_type'],
            $data['account_id']
        );

        CheckLoyaltyPointAction::execute($account->id, $data['payment_amount']);

        $transaction = WithdrawAction::execute(
            $account->id,
            $data['points_amount'],
            $data['description']
        );

        return new LoyaltyPointsTransactionResource($transaction);
    }
}
