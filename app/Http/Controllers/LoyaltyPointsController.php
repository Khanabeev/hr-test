<?php

namespace App\Http\Controllers;

use App\Http\Requests\CancelRequest;
use App\Http\Requests\DepositRequest;
use App\Http\Resources\LoyaltyPointsTransactionResource;
use Illuminate\Support\Facades\Log;
use Src\Loyalty\Actions\CancelTransactionAction;
use Src\Loyalty\Actions\GetLoyaltyAccountAction;
use Src\Loyalty\Actions\PerformPaymentLoyaltyPointsAction;
use Src\Loyalty\Actions\SendNotificationAction;
use Src\Loyalty\DataTransferObject\PaymentLoyaltyPointsDTO;
use Src\Loyalty\Exceptions\AccountIsNotActiveException;
use Src\Loyalty\Exceptions\AccountNotFoundException;
use Src\Loyalty\Exceptions\TransactionNotFoundException;
use Src\Loyalty\Models\LoyaltyAccount;
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
        Log::info('Deposit transaction input: ' . print_r($request->all(), true));

        $data = $request->validated();

        $account = GetLoyaltyAccountAction::execute(
            $data['account_type'],
            $data['account_id']
        );

        $dto = PaymentLoyaltyPointsDTO::fromArray($data);
        $transaction = PerformPaymentLoyaltyPointsAction::execute($dto);

        SendNotificationAction::execute($account, $transaction);

        return new LoyaltyPointsTransactionResource($transaction);
    }

    /**
     * @throws TransactionNotFoundException
     */
    public function cancel(CancelRequest $request): void
    {
        $data = $request->validated();

        CancelTransactionAction::execute(
            $data['transaction_id'],
            $data['cancellation_reason']
        );
    }

    public function withdraw()
    {
        $data = $_POST;

        Log::info('Withdraw loyalty points transaction input: ' . print_r($data, true));

        $type = $data['account_type'];
        $id = $data['account_id'];
        if (($type == 'phone' || $type == 'card' || $type == 'email') && $id != '') {
            if ($account = LoyaltyAccount::where($type, '=', $id)->first()) {
                if ($account->active) {
                    if ($data['points_amount'] <= 0) {
                        Log::info('Wrong loyalty points amount: ' . $data['points_amount']);
                        return response()->json(['message' => 'Wrong loyalty points amount'], 400);
                    }
                    if ($account->getBalance() < $data['points_amount']) {
                        Log::info('Insufficient funds: ' . $data['points_amount']);
                        return response()->json(['message' => 'Insufficient funds'], 400);
                    }

                    $transaction = LoyaltyPointsTransaction::withdrawLoyaltyPoints($account->id, $data['points_amount'], $data['description']);
                    Log::info($transaction);
                    return $transaction;
                } else {
                    Log::info('Account is not active: ' . $type . ' ' . $id);
                    return response()->json(['message' => 'Account is not active'], 400);
                }
            } else {
                Log::info('Account is not found:' . $type . ' ' . $id);
                return response()->json(['message' => 'Account is not found'], 400);
            }
        } else {
            Log::info('Wrong account parameters');
            throw new \InvalidArgumentException('Wrong account parameters');
        }
    }
}
