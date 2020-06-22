<?php
namespace App;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Morilog\Jalali\Jalalian;

class Zarrin
{

    public $request;
    protected $connection = 'mysql';

    public function __construct(array $request)
    {
        $this->request = $request;
    }
    public function create($repo){


        $cart = $this->request['cart'];
        $total_price = 0;
        for($i=0;$i<count($cart);$i++){

            $total_price = $total_price + $cart[$i]['num']*$cart[$i]['price'];
        }
        $amount = $total_price;
        $callback = env('ZARRIN_CALLBACK');
        $data = array('MerchantID' => env('ZARRIN_TOKEN'),
            'Amount' => $amount,
            'CallbackURL' => $callback,
            'Description' => 'خرید سرویس شبکه شخصی مجازی');
        $jsonData = json_encode($data);
        $ch = curl_init('https://www.zarinpal.com/pg/rest/WebGate/PaymentRequest.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));
        $result = curl_exec($ch);
        $err = curl_error($ch);
        $result = json_decode($result, true);
        curl_close($ch);
        if ($err) {
            return 404;
        } else {
            if ($result["Status"] == '100' ) {


                $trans = new Transaction();
                $trans->trans_id = 'Zarrin_' . strtoupper(uniqid());
                $trans->status = 'unpaid';
                $trans->amount = $total_price;
                $trans->authority = $result['Authority'];
                if(Auth::guard('user')->check()){

                    $trans->user_id = Auth::guard('user')->id();

                }elseif(Auth::guard('admin')->check()){

                    $trans->admin_id = Auth::guard('admin')->id();
                }

                $trans->save();

                $basket = new Cart();
                $basket->cart = serialize($this->request);
                $basket->amount = $total_price;
                $basket->code = uniqid();
                $basket->address = $this->request['address'];
                if(Auth::guard('user')->check()){

                    $basket->user_id = Auth::guard('user')->id();

                }elseif(Auth::guard('admin')->check()){

                    $basket->admin_id = Auth::guard('admin')->id();
                }
                $basket->trans_id = $trans->id;
                $basket->email = Auth::guard($repo->getGuard())->user()->email;

                if (isset($this->request['email'])) {

                    $basket->email = Auth::guard('user')->user()->email;
                }
                elseif (isset($this->request['phone'])) {

                    $basket->phone = $this->request['phone'];
                }
                $basket->save();
                return $result;
            } else {
                return 404;
            }
        }

    }

    public function verify(){

        $transactionId = $this->request['Authority'];
        $trans = Transaction::where('authority',$transactionId)->first();
        if(is_null($trans)){
            return 'کد تراکنش نادرست است';
        }
        if($trans->status == 'paid'){
            return 'تراکنش تکراری است';
        }

        $data = array('MerchantID' => env('ZARRIN_TOKEN'), 'Authority' => $transactionId, 'Amount'=>$trans->amount);
        $jsonData = json_encode($data);
        $ch = curl_init('https://www.zarinpal.com/pg/rest/WebGate/PaymentVerification.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        $result = json_decode($result, true);
        if ($err) {
            return "cURL Error #:" . $err;
        } else {

            if ($result['Status'] == '100') {

                $this->ZarrinPaymentConfirm($trans);

                return redirect()->route('PaymentSuccess',['transid'=>$trans->trans_id]);

            } else {

                DB::table('transactions')->where('trans_id', $trans->trans_id)->update([
                    'status' => 'canceled'
                ]);
                return redirect()->route('PaymentCanceled', ['transid' => $trans->trans_id]);
            }
        }
    }
    private function ZarrinPaymentConfirm($trans)
    {

        DB::table('transactions')->where('trans_id', $trans->id)->update([
            'status' => 'paid'
        ]);

        $cart = Cart::where('trans_id',$trans->id)->first();
        $cart->update(['completed' => 1]);

        $cart->cart = unserialize($cart->cart);
        if(!is_null($cart->user_id)){
            $user = $cart->user;
        }else{
            $user = $cart->admin;
        }
        $data = ['cart'=>$cart,'trans'=>$trans,'user'=>$user];

        Mail::send('email.invoiceMail',$data,function($message)use($cart){

            $message->to($cart->email);
            $message->from(env('NoReply'));
            $message->subject('فاکتور خرید');
        });

        Mail::send('email.invoiceMail',$data,function($message)use($cart){
//
            $message->to(env('SAILS_MAIL'));
            $message->from(env('NoReply'));
            $message->subject('فاکتور خرید');
        });


    }

}
