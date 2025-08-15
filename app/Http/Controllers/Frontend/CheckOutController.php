<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Traits\GetGlobalInformationTrait;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Session;

class CheckOutController extends Controller
{
    use GetGlobalInformationTrait;
    function index()
    {
        $user = userAuth();
        $cart_count = $user->cart_count;
    
        if($cart_count == 0){
            return redirect()->route('courses')->with(['messege' => __('Please add some courses in your cart.'), 'alert-type' => 'error']);
        }

        $cartTotal = $user->cart_total;
        $discountPercent = Session::has('offer_percentage') ? Session::get('offer_percentage') : 0;
        $discountAmount = ($cartTotal * $discountPercent) / 100;
        $total = currency($cartTotal - $discountAmount);
        $coupon = Session::has('coupon_code') ? Session::get('coupon_code') : '';

        $payable_amount = $cartTotal - $discountAmount;
        Session::put('payable_amount', $payable_amount);

        $paymentService = app(\Modules\BasicPayment\app\Services\PaymentMethodService::class);
        $activeGateways = $paymentService->getActiveGatewaysWithDetails();


        return view('frontend.pages.checkout')->with([
            'cart_count' => $cart_count,
            'total' => $total,
            'discountAmount' => $discountAmount,
            'discountPercent' => $discountPercent,
            'coupon' => $coupon,
            'payable_amount' => $payable_amount,
            'paymentService' => $paymentService,
            'activeGateways' => $activeGateways,
        ]);
    }
}
