<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Modules\InstructorRequest\app\Models\InstructorRequest;
use Modules\Order\app\Models\OrderItem;
use Modules\PaymentWithdraw\app\Models\WithdrawMethod;
use Modules\PaymentWithdraw\app\Models\WithdrawRequest;

class InstructorPayoutController extends Controller
{
    public function index() {

        $courseIds = Course::where('instructor_id', userAuth()->id)->pluck('id')->toArray(); 
        $totalCourseSold = OrderItem::whereIn('course_id', $courseIds)->whereRelation('order', 'payment_status', 'paid')->count();
        $withdrawRequests = WithdrawRequest::where('user_id', userAuth()->id)->orderBy('id', 'desc')->paginate(30);
        $totalWithdraw = WithdrawRequest::where(['user_id' => userAuth()->id, 'status' => 'approved'])->sum('withdraw_amount');
        return view('frontend.instructor-dashboard.payout.index', compact('totalCourseSold', 'withdrawRequests', 'totalWithdraw')); 
    }

    public function create() {
        $courseIds = Course::where('instructor_id', userAuth()->id)->pluck('id')->toArray(); 
        $totalCourseSold = OrderItem::whereIn('course_id', $courseIds)->whereRelation('order', 'payment_status', 'paid')->count();
        $gateway = InstructorRequest::where('user_id', userAuth()->id)->first();
        $withdrawMethod = WithdrawMethod::where('name', $gateway->payout_account)->first();
        if (!$withdrawMethod) {
            return to_route('instructor.setting.index')->with(['alert-type'=>'error','messege'=>__('Please set up a withdrawal method first.')]);
        }
        $totalWithdraw = WithdrawRequest::where(['user_id' => userAuth()->id, 'status' => 'approved'])->sum('withdraw_amount');
       return view('frontend.instructor-dashboard.payout.create', compact('totalCourseSold', 'gateway', 'totalWithdraw', 'withdrawMethod')); 
    }

    public function store(Request $request) {
        $request->validate([
            'amount' => 'required|numeric',
        ], [
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
        ]); 

        $gateway = InstructorRequest::where('user_id', userAuth()->id)->first();
        $withdrawMethod = WithdrawMethod::where('name', $gateway->payout_account)->first();

        if($request->amount < 0 || $request->amount > userAuth()->wallet_balance) {
            return redirect()->back()->with(['alert-type' => 'error', 'messege' => __('Invalid amount')]);
        }elseif($withdrawMethod->min_amount > $request->amount) {
            return redirect()->back()->with(['alert-type' => 'error', 'messege' => __('Minimum payout amount is :amount', ['amount' => $withdrawMethod->min_amount])]);
        }elseif($withdrawMethod->max_amount < $request->amount) {
            return redirect()->back()->with(['alert-type' => 'error', 'messege' => __('Maximum payout amount is :amount', ['amount' => $withdrawMethod->max_amount])]);
        }elseif(WithdrawRequest::where('user_id', userAuth()->id)->where('status', 'pending')->exists()) {
            return redirect()->back()->with(['alert-type' => 'error', 'messege' => __('You already have a pending payout request.')]);
        }

        WithdrawRequest::create([
            'user_id' => userAuth()->id,
            'withdraw_amount' => $request->amount,
            'method' => $gateway->payout_account,
            'account_info' => $gateway->payout_information,
            'status' => 'pending',
            'current_amount' => userAuth()->wallet_balance,
        ]);


        return redirect()->route('instructor.payout.index')->with(['alert-type' => 'success', 'messege' => __('Payout request sent successfully.')]);

    }

    public function destroy(string $id) {
        $request = WithdrawRequest::where('id', $id)->where(['user_id' => userAuth()->id, 'status' => 'pending'])->first();

        if($request) {
            $request->delete();
            return response(['status' => 'success', 'message' => __('Deleted Successfully')]);
        }
        return response(['status' => 'success', 'message' => __('Request not found')]);
    }
}

