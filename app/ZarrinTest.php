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
use SoapClient;

class ZarrinTest
{

    public $request;
    protected $connection = 'mysql';

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function create()
    {

        $cart = $this->request['cart'];
        $total_price = 0;
        for ($i = 0; $i < count($cart); $i++) {

            $total_price = $total_price + $cart[$i]['num'] * $cart[$i]['price'];
        }

        $amount = $total_price;
        $callback = env('ZARRIN_CALLBACK');
        $data = array('MerchantID' => 'DAsjsahdiuudsuhnuai',
            'Amount' => $amount,
            'CallbackURL' => $callback,
            'Description' => 'خرید سرویس شبکه شخصی مجازی');
        $jsonData = json_encode($data);
        $client = new SoapClient('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']);

//        $ch = curl_init('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl');
//        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//            'Content-Type: application/json',
//            'Content-Length: ' . strlen($jsonData)
//        ));
//        $result = curl_exec($ch);
//        $err = curl_error($ch);

        $result = $client->PaymentRequest(
            [
                'MerchantID' => "dasdasdsad",
                'Amount' => $total_price,
                'Description' => "Salam Cinam",
                'CallbackURL' => 'http://google.com',
            ]
        );
//        $result = json_decode($result, true);
//        curl_close($ch);
        if ($result->Status == '100') {


            $trans = new Transaction();
            $trans->trans_id = 'Zarrin_' . strtoupper(uniqid());
            $trans->status = 'unpaid';
            $trans->amount = $total_price;
            $trans->authority = $result->Authority;
            $trans->user_id = Auth::guard('user')->id();
            $trans->admin_id = DB::table('shared_keys')->where('user_id', Auth::guard('user')->id())->first()->admin_id;
            $trans->save();

            $basket = new Cart();
            $basket->cart = serialize($this->request);
            $basket->amount = $total_price;
            $basket->code = uniqid();
            $basket->address = $this->request['address'];
            $basket->user_id = Auth::guard('user')->id();
            $basket->trans_id = $trans->id;
            $basket->email = Auth::guard('user')->user()->email;

            if (isset($this->request['email'])) {

                $basket->email = Auth::guard('user')->user()->email;
            }
            elseif (isset($this->request['phone'])) {

                $basket->phone = $this->request['phone'];
            }
            $basket->save();
            return $result;
        }
        else {
            return 404;
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
        if(is_null($cached)){

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
            DB::beginTransaction();

            $cached->update(['closed'=>1]);
//            if(!is_null($cached)){
//                $cached->
//            }
            DB::commit();
            if ($result['Status'] == '100') {

                $this->ZarrinPaymentConfirm($trans);

                return redirect()->route('RemotePaymentSuccess',['transid'=>$trans->trans_id]);

            } else {
                DB::beginTransaction();
                $trans->update(['status'=>'canceled']);
                DB::commit();
                $char = Emoji::redCircle();
                $msg = [
                    'chat_id' => $trans->user_id,
                    'text' => " $char $char پرداخت شما ناموفق بود. شماره تراکنش : $trans->trans_id",
                    'parse_mode' => 'HTML',
                ];
                TelegramNotification::dispatch($msg);
                $telegram = new \App\Repo\Telegram(env('BOT_TOKEN'));
                $options = [array($telegram->buildInlineKeyBoardButton('شروع مجدد','','restart'))];
                $msg2 = [
                    'chat_id' => $trans->user_id,
                    'text' => 'جهت خرید مجدد، کلیک کنید',
                    'parse_mode' => 'HTML',
                    'reply_markup' => $telegram->buildInlineKeyboard($options),
                ];
                TelegramNotification::dispatch($msg2);
//                $data = array($msg);
//                $jsonData = json_encode($data);
//                $ch = curl_init('https://vitamin-g.ir/api/hook?type=canceled');
//                curl_setopt($ch, CURLOPT_USERAGENT, 'JOY VPN HandShake');
//                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
//                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
//                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//                    'Content-Type: application/json',
//                    'Content-Length: ' . strlen($jsonData)
//                ));
//                curl_exec($ch);
//                curl_close($ch);

                return redirect()->route('RemotePaymentCanceled', ['transid' => $trans->trans_id]);
            }
        }
    }
//    private function ZarrinPaymentConfirm($trans)
//    {
//        // if($trans->service == 'cisco'){
//        //     $account = Accounts::where('user_id',$trans->user_id)->where('plan_id','!=',3)->where('used',1)->first();
//        //     // it means that user updated his account. it's NOT a new account
//        //     if($account !== null){
//        //         $account->update(['expires_at'=> Carbon::now()->addMonths($trans->plan->month)]);
//        //     }else{
//
//
//        // }
//
//        // }elseif ($trans->service == 'openvpn'){
//        //     $account = Ovpn::where('user_id',$trans->user_id)->where('used',1)->first();
//        //     // it means that user updated his account. it's NOT a new account
//        //     if($account !== null){
//        //         $account->update(['expires_at'=> Carbon::now()->addMonths($trans->plan->month)]);
//        //     }else{
//
//        //         $account = Ovpn::where('plan_id',$trans->plan_id)->where('used',0)->first();
//        //         $account->update(['used'=>1,'user_id'=>$trans->user_id,'expires_at'=>Carbon::now()->addMonths($trans->plan->month)]);
//        //     }
//
//        // }
//        DB::beginTransaction();
//        DB::connection('mysql')->table('transactions')->where('trans_id', $trans->trans_id)->update([
//            'status' => 'paid'
//        ]);
//        $account = Accounts::where('plan_id',$trans->plan_id)->where('used',0)->first();
//        if($trans->plan_id == 6){
//            $account->update(['used'=>1,'user_id'=>$trans->user_id,'expires_at'=>Carbon::now()->addDays(21)]);
//        }else{
//            $account->update(['used'=>1,'user_id'=>$trans->user_id,'expires_at'=>Carbon::now()->addMonths($trans->plan->month)->addDays(7)]);
//        }
//
//        $trans->update(['account_id'=>$account->id]);
//
////  =================  Affiliation Part =================
//
//        $ip = IpFinder::find();
//        $affiliationBuy = Affiliate::where('invitee',$ip)->where('done',0)->first();
//        if(!is_null($affiliationBuy)){
//            $affiliationBuy->update(['invitee_id'=>$trans->user_id,'done'=>1]);
//            $inviterShares = Affiliate::where('inviter',$affiliationBuy->inviter)->get()->sum('done');
//            if($inviterShares == 1){
//
//                $msg = [
//                    'chat_id' => $affiliationBuy->inviter,
//                    'text' => Emoji::fire().'تبریک! فقط ۲ کاربر تا حساب رایگان ۱ ماهه فاصله دارید'.Emoji::fire()
//                ];
//                TelegramNotification::dispatch($msg);
//            }
//            elseif ($inviterShares == 4){
//                $msg = [
//                    'chat_id' => $affiliationBuy->inviter,
//                    'text' => Emoji::fire().'تبریک! فقط ۲ کاربر تا حساب رایگان ۳ ماهه فاصله دارید'.Emoji::fire()
//                ];
//                TelegramNotification::dispatch($msg);
//            }
//            elseif ($inviterShares == 3){
//                $this->affiliateReward5($affiliationBuy);
//            }
//            elseif ($inviterShares == 6){
//                $this->affiliateReward10($affiliationBuy);
//            }
//        }
//        DB::commit();
//        sendNotif::dispatch($trans,$account);
//
//    }




}
