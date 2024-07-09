<?php

namespace Src\Loyalty\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyPointsTransaction extends Model
{
    protected $table = 'loyalty_points_transaction';

    protected $fillable = [
        'account_id',
        'points_rule',
        'points_amount',
        'description',
        'payment_id',
        'payment_amount',
        'payment_time',
    ];



    public static function withdrawLoyaltyPoints($account_id, $points_amount, $description) {
        return LoyaltyPointsTransaction::create([
            'account_id' => $account_id,
            'points_rule' => 'withdraw',
            'points_amount' => -$points_amount,
            'description' => $description,
        ]);
    }
}
