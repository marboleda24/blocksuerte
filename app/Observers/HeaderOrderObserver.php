<?php

namespace App\Observers;

use App\Mail\SystemNotificationMail;
use App\Models\HeaderOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class HeaderOrderObserver
{
    /**
     * @var bool
     */
    public $afterCommit = true;

    /**
     * updating
     *
     * @param  mixed  $headerOrder
     * @return void
     */
    public function updated(HeaderOrder $headerOrder): void
    {
       /**
        *
        */
    }

    /**
     * @param  HeaderOrder  $headerOrder
     * @return void
     */
    public function creating(HeaderOrder $headerOrder): void
    {
        $headerOrder->consecutive = getLastConsecutive(HeaderOrder::class);
    }

    /**
     * @param $customer_code
     * @param $product_code
     * @param $price
     * @return bool
     */
    protected function verify_price($customer_code, $product_code, $price): bool
    {
        $query = DB::connection('MAX')
            ->table('Invoice_Detail')
            ->where('CUSTID_32', '=', $customer_code)
            ->where('PRTNUM_32', '=', $product_code)
            ->orderBy('INVDTE_32', 'desc')
            ->take(3)
            ->get();

        if (count($query) >= 3) {
            if (floatval($price) >= intval(($query->sum('PRICE_32') / count($query)))) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
