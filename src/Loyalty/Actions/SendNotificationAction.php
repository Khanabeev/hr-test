<?php

namespace Src\Loyalty\Actions;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Src\Loyalty\Mail\LoyaltyPointsReceived;
use Src\Loyalty\Models\LoyaltyAccount;
use Src\Loyalty\Models\LoyaltyPointsTransaction;

class SendNotificationAction
{
    public static function execute(LoyaltyAccount $account, LoyaltyPointsTransaction $transaction): void
    {
        if ($account->email != '' && $account->email_notification) {
            Mail::to($account)->send(new LoyaltyPointsReceived(
                $transaction->points_amount,
                $account->getBalance()));
        }
        if ($account->phone != '' && $account->phone_notification) {
            // instead SMS component
            Log::info('You received' . $transaction->points_amount . 'Your balance' . $account->getBalance());
        }
    }

}
