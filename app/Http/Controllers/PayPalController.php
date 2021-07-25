<?php

namespace App\Http\Controllers;

use App\Mail\OrderPaid;
use App\Models\Order;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\ExpressCheckout;
use Illuminate\Support\Facades\Mail;

class PayPalController extends Controller
{
    //
    public function getExpressCheckout($orderid){
        $checkoutData=$this->checkoutData($orderid);
        $provider=new ExpressCheckout();
        $response=$provider->setExpressCheckout($checkoutData);
        //dd($response);
        return redirect($response['paypal_link']);
    }
    public function checkoutData($orderid){
        $cartItems = array_map(function($item){
            return [
                'name'=>$item['name'],
                'price'=>$item['price'],
                'qty'=>$item['quantity']
            ];
        },\Cart::session(auth()->id())->getContent()->toarray());
        //dd($cartItems);
        $checkoutData=[
            'items'=>$cartItems,
            'return_url'=>route('paypal.success',$orderid),
            'cancel_url'=>route('paypal.cancel'),
            'invoice_id'=>uniqid(),
            'invoice_description'=>'order description',
            'total'=>\Cart::session(auth()->id())->getTotal()

        ];
        return $checkoutData;
    }
    public function cancelPage(){
        dd('payment failed');
    }
    public function getExpressCheckoutSuccess(Request $request,$orderid){
        $token=$request->get('token');
        $payerId=$request->get('PayerID');
        $provider=new ExpressCheckout();
        $response=$provider->getExpressCheckoutDetails($token);
        $checkoutData=$this->checkoutData($orderid);
        if(in_array(strtoupper($response['ACK']),['SUCCESS','SUCCESSWITHWARNING'])){
            $payment_status=$provider->doExpressCheckoutPayment($checkoutData,$token,$payerId);
            $status=$payment_status['PAYMENTINFO_0_PAYMENTSTATUS'];
            if(in_array($status,['Completed','Processed'])){
                $order=Order::find($orderid);
                $order->is_paid=1;
                $order->save();
                //Mail::to($order->user->email)-send(new OrderPaid($order));
                Mail::to($order->user->email)->send(new OrderPaid($order));
                return redirect()->route('home')->withMessage('Payment Succesfull');


            }

        }
        //dd('Payment Succesfull');
        return redirect()->route('home')->withError('Payment Not Succesfull');

    }
}
