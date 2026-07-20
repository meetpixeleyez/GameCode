<?php

namespace App\Lib;

use App\Models\Transaction;
use App\Models\User;

class Referral {
    public static function processReferralCommission($buyer, $totalGetSellerAmount, $order) {
        $refAmount = gs('referral_fixed') + ($totalGetSellerAmount * gs('referral_percentage') / 100);

        $refUser = User::active()->find($buyer->ref_by);

        if ($refUser) {
            $refUser->balance += $refAmount;
            $refUser->save();

            $transaction               = new Transaction();
            $transaction->user_id      = $refUser->id;
            $transaction->amount       = $refAmount;
            $transaction->post_balance = $refUser->balance;
            $transaction->charge       = 0;
            $transaction->trx_type     = '+';
            $transaction->details      = 'Referral commission for Purchase Item by @' . $buyer->username;
            $transaction->trx          = $order->trx;
            $transaction->remark       = 'referral_commission';
            $transaction->save();
        }
    }
}
