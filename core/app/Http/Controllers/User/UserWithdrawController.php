<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Lib\UserActionProcess;
use App\Traits\WithdrawProcess;
use App\Models\UserWithdrawMethod;
use App\Models\Withdrawal;
use App\Models\WithdrawMethod;
use App\Models\Gateway;
use App\Models\Currency;
use App\Models\Wallet;
use App\Models\AdminNotification;

use App\Models\GeneralSetting;
use App\Models\Transaction;

use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use App\Http\Helpers\Helper;



class UserWithdrawController extends Controller{
    
    use WithdrawProcess;


   

    public function withdraw(){
        $userMethods = UserWithdrawMethod::myWithdrawMethod()->with('withdrawMethod', 'currency')->get();
        $pageTitle = 'Withdraw Money';
        return view($this->activeTemplate . 'user.withdraw.withdraw_money', compact('pageTitle', 'userMethods'));
    }

    public function withdrawPreview(){
        $withdraw = Withdrawal::with('method', 'user')->where('trx', session()->get('wtrx'))->where('status', 0)->orderBy('id', 'desc')->firstOrFail();
        $pageTitle = 'Withdraw Preview';
        return view($this->activeTemplate . 'user.withdraw.preview', compact('pageTitle', 'withdraw'));
    }

    public function withdrawLog(Request $request){
        $pageTitle = "Withdraw Log";
        $withdraws = auth()->user()->withdrawals()->searchable(['trx'])->with(['method'])->orderBy('id','desc')->with('curr')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.withdraw.log', compact('pageTitle', 'withdraws'));
    }

    
    public function withdrawMethods(){

       // $userMethods = UserWithdrawMethod::myWithdrawMethod()->get();
        $userMethods = UserWithdrawMethod::myWithdrawMethod()->with('withdrawMethod', 'currency')->get();

        $withdrawMethods = WithdrawMethod::where('status',1)->get();
        $pageTitle = 'Withdraw Money';
        

//dd($userMethods);

       // $userMethods = UserWithdrawMethod::myWithdrawMethod()->whereHas('withdrawMethod')->with('withdrawMethod', 'currency')->paginate(getPaginate());
      //  $pageTitle = 'Withdraw Methods';
        return view($this->activeTemplate . 'user.withdraw.methods', compact('pageTitle', 'userMethods','withdrawMethods'));
    }

    public function fileDownload($fileHash){
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $general = gs();
        $title = slug($general->site_name) . '- attachments.' . $extension;
        $mimetype = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

     public function withdrawAddMethod(Request $request)
    {
        $method = WithdrawMethod::where('id', $request->id)->first();
        return view($this->activeTemplate.'user.withdraw.add_account', compact('method'));
    }

    public function withdrawSaveMethod(Request $request)
    {
        $userMethod = new UserWithdrawMethod();
        $userMethod->name =$request->name;
        $userMethod->user_id = userGuard()['user']->id;
        $userMethod->user_type = userGuard()['type'];
        $userMethod->method_id = $request->method_id;
        $userMethod->currency_id = $request->currency_id;
        $storeHelper = $this->storeHelper($request, []); // Call the storeHelper() method
        $userMethod->user_data = $storeHelper['user_data'];
        //$userMethod->user_data = $storeHelper['user_data'];
        $userMethod->save();
        $withdraw_method = WithdrawMethod::where('id', $request->method_id)->first();
        $html = '';

        $userMethods = UserWithdrawMethod::myWithdrawMethod()->with('withdrawMethod', 'currency')->get();


        foreach($userMethods as $user_account){
    if($user_account->method_id == $withdraw_method->id){


        //foreach($withdraw_method->MyWithdrawMethod() as $user_account){
            $html .= view($this->activeTemplate.'user.withdraw.account', compact('user_account', 'withdraw_method'))->render();
        }
        }
        return response()->json(['html' => $html]);
    }

     protected function storeHelper($request,$rules,$isUpdate = false)
    {        

       // $withdrawMethod = WithdrawMethod::where('id',$request->method_id)->whereJsonContains('user_guards',userGuard()['guard'])->where('status',1)->firstOrFail();

         $withdrawMethod = WithdrawMethod::where('id',$request->method_id)->where('status',1)->first(); 

        if (!$withdrawMethod) {
            $notify[]=['error','Something went wrong!'];
            return back()->withNotify($notify);
        }

        if ($withdrawMethod->user_data != null) {
            foreach ($withdrawMethod->user_data as $key => $cus) {
                if ($isUpdate && $cus->type == 'file') {
                    $rules[$key] = ['nullable'];
                }else{
                    $rules[$key] = [$cus->validation];
                }
                if ($cus->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], new FileTypeValidate(['jpg','jpeg','png']));
                    array_push($rules[$key], 'max:2048');
                }
                if ($cus->type == 'text') {

                    array_push($rules[$key], 'max:191');
                }
                if ($cus->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
            }
        }


        $directory = date("Y")."/".date("m")."/".date("d");
        //$path = imagePath()['verify']['withdraw']['path'].'/'.$directory;
        $path = '/images/withdraw/'.'/'.$directory;
//dd($path);
        $collection = collect($request);
        $reqField = [];
        if ($withdrawMethod->user_data != null) {
            foreach ($collection as $k => $v) {
                foreach ($withdrawMethod->user_data as $inKey => $inVal) {
                    if (($k != $inKey) && ($inVal->type != 'file')) {
                        continue;
                    } else {
                        if ($inVal->type == 'file') {
                            if ($request->hasFile($inKey)) {
                                try {
                                    $reqField[$inKey] = [
                                        'field_name' =>$directory.'/'.uploadImage($request[$inKey], $path),
                                        'type' => $inVal->type,
                                        'validation' => $inVal->validation
                                    ];

                                } catch (\Exception $exp) {
                                    $notify[] = ['error', 'Could not upload your ' . $request[$inKey]];
                                    return back()->withNotify($notify)->withInput();
                                }
                            }else{
                                $reqField[$inKey] = [
                                    'field_name' =>@$userMethod->user_data->$inKey->field_name,
                                    'type' => $inVal->type,
                                    'validation' => $inVal->validation
                                ];
                            }
                        } else {
                            $reqField[$inKey] = [
                                'field_name' => $v,
                                'type' => $inVal->type,
                                'validation' => $inVal->validation
                            ];

                        }
                    }
                }
            }

        }

        return ['user_data'=>$reqField,'rules'=>$rules];
    }


    public function withdrawDeleteMethod(Request $request)
    {
        UserWithdrawMethod::where('id', $request->id)->delete();
    }


       public function withdrawMethodEdit($id)
    {
        $pageTitle = 'Withdraw Method Edit';
        $userMethod = UserWithdrawMethod::myWithdrawMethod()->where('id',$id)->first();
        if (!$userMethod) {
            $notify[]=['error','Withdraw method not found'];
            return back()->withNotify($notify);
        }
        $withdrawMethod = WithdrawMethod::whereJsonContains('user_guards',userGuard()['guard'])->where('status',1)->get();
        $currencies = Currency::pluck('id','currency_code');
        return view($this->activeTemplate.strtolower(userGuard()['type']).'user.withdraw.withdraw_method_edit', compact('pageTitle','userMethod','withdrawMethod','currencies'));
    }

    public function withdrawMethodUpdate(Request $request)
    {
        $userMethod = UserWithdrawMethod::where('user_type',userGuard()['type'])->where('user_id',userGuard()['user']->id)->where('id',$request->id)->first();
        if (!$userMethod) {
            $notify[]=['error','Withdraw method not found'];
            return back()->withNotify($notify);
        }


        $rules = ['name'=>'required'];
        $storeHelper = $this->storeHelper($request, $rules,true);

        $this->validate($request, $storeHelper['rules']);

        $userMethod->name         = $request->name;
        $userMethod->user_id      = userGuard()['user']->id;
        $userMethod->user_type    = userGuard()['type'];
        $userMethod->user_data    = $storeHelper['user_data'];
        $userMethod->status       = $request->status ? 1:0;
        $userMethod->save();
        $notify[]=['success','Withdraw method added successfully'];
        return back()->withNotify($notify);
    }


    public function withdrawStore(Request $request)
    {//dd($request);

        $this->validate($request, [
            'method_id' => 'required',
            'user_method_id' => 'required',
            'amount' => 'required|numeric'
        ]);

       $user = userGuard()['user'];


        //$method = WithdrawMethod::where('id', $request->method_id)->where('status', 1)->whereJsonContains('user_guards',userGuard()['guard'])->firstOrFail();

    $method = WithdrawMethod::where('id', $request->method_id)->where('status', 1)->first();




        $userMethod = UserWithdrawMethod::myWithdrawMethod()->findOrFail($request->user_method_id);
        $currency = Currency::find($userMethod->currency_id);
        if(!$currency){
            $notify[] = ['error', 'Currency not found'];
            return back()->withNotify($notify);
        }
        $wallet = Wallet::hasCurrency()->where('user_type',userGuard()['type'])->where('user_id',userGuard()['user']->id)->where('currency_id',$currency->id)->first();
        if(!$wallet){
            $notify[] = ['error', 'Wallet not found'];
            return back()->withNotify($notify);
        }
        //dd($method);


        if ($method->min_limit/$currency->rate >  $request->amount || $method->max_limit/$currency->rate <  $request->amount) {
            $notify[] = ['error', 'Please follow the limits'];
            return back()->withNotify($notify);
        }

        if ($request->amount > $wallet->balance) {
            $notify[] = ['error', 'You do not have sufficient balance for withdraw.'];
            return back()->withNotify($notify);
        }


        $charge = ($method->fixed_charge/$currency->rate) + ($request->amount * $method->percent_charge / 100);
        $defaultCurrency = Currency::where('id', $method->accepted_currency)->first();
        if($currency->currency_code != $defaultCurrency->currency_code){
            $exchangeCharge = \App\Models\TransactionCharge::where('slug','exchange_charge')->first();
            if($exchangeCharge->percent_charge > 0){
                $charge += $request->amount*$exchangeCharge->percent_charge/100;
            }
            if($exchangeCharge->fixed_charge > 0){
                $charge += $exchangeCharge->fixed_charge*$defaultCurrency->rate;
            }
        }
        $finalAmount = $request->amount - $charge;

        $limit_our_balance = false;
        $instaforex_data = 0;
        $perfect_money = 0;
        if($method->type_method == 1){
            $instaforex_data = 1;
//            $method_setting = json_decode($method->setting,true);
//            $method_setting = $method_setting ? $method_setting : array();
//            if(isset($method_setting['umbrella_account']) && !empty($method_setting['umbrella_account']) && isset($method_setting['umbrella_password']) && !empty($method_setting['umbrella_password'])){
//                $instaforex = new \InstaForex($method_setting['umbrella_account'], $method_setting['umbrella_password']);
//                $balance = $instaforex->getBalance();
//                if($balance && $balance > $finalAmount){
//                    $instaforex_data = 1;
//                }
//                else{
//                    $limit_our_balance = true;
//                }
//            }
        }
        if($method->type_method == 2){
            $perfect_money = 1;
        }

        if($limit_our_balance){
            $notify[] = ['error', 'Your Request Amount is Larger Then Our Current Balance.'];
            return back()->withNotify($notify);
        }
        
        if($currency->currency_code == $method->method_currency){
            $wrate= 1.00;
            }else{
            $wrate = $request->currency_rate;
        }
//dd($finalAmount);

        $withdraw = new Withdrawal();
        $withdraw->method_id = $method->id;
        $withdraw->user_id = $user->id;
        $withdraw->user_type = userGuard()['type'];
        $withdraw->amount = $request->amount;
        $withdraw->rate = $wrate;
        $withdraw->currency_id = $currency->id;
        $withdraw->wallet_id = $wallet->id;
        $withdraw->instaforex = $instaforex_data;
        $withdraw->perfect_money = $perfect_money;
        $withdraw->currency = $currency->currency_code;
        $withdraw->method_currency = $method->method_currency;
        $withdraw->charge = $charge;
        $withdraw->final_amount = $finalAmount*$wrate;
        $withdraw->after_charge = $finalAmount*$wrate;
        $withdraw->withdraw_information = $userMethod->user_data;
        $withdraw->trx = getTrx();
        $withdraw->save();
        session()->put('wtrx',$withdraw->trx);
//        return redirect(route(strtolower(userGuard()['type']).'.withdraw.preview'));
        $userAction = new UserActionProcess();
        $userAction->user_id = userGuard()['user']->id;
        $userAction->user_type = userGuard()['type'];
        $userAction->act = 'withdraw_money';
        $userAction->details = [
            'done_route'=>route(strtolower(userGuard()['type']).'.withdraw.submit.done'),
        ];
        $userAction->type = $request->otp_type;
        $userAction->submit();

        return redirect($userAction->next_route);
    }



    public function withdrawSubmit(Request $request)
    {
        $request->validate([
            'otp_type'=>'nullable|in:email,sms,2fa'
          ]
        );
       $userAction = new UserActionProcess();
       $userAction->user_id = userGuard()['user']->id;
       $userAction->user_type = userGuard()['type'];
       $userAction->act = 'withdraw_money';
       $userAction->details = [
            'done_route'=>route(strtolower(userGuard()['type']).'.withdraw.submit.done'),
       ];
       $userAction->type = $request->otp_type;
       $userAction->submit();

       return redirect($userAction->next_route);
    }

    public function withdrawSubmitDone(){
        if(!session('wtrx')){
            $notify[]=['error','Sorry! something went  wrong'];
            return back()->withNotify($notify);
        }
        $withdraw = Withdrawal::with('method',strtolower(userGuard()['type']))->where('trx', session()->get('wtrx'))->where('status', 0)->orderBy('id','desc')->firstOrFail();

        $wallet = Wallet::checkWallet(['user'=>userGuard()['user'],'type'=>userGuard()['type']])->find($withdraw->wallet_id);
        if(!$wallet){
            $notify[]=['error','wallet not found'];
            return back()->withNotify($notify);
        }



        $withdraw->status = 2;
        $withdraw->save();

        $wallet->balance  -=  $withdraw->amount;
        $wallet->save();

        $transaction = new Transaction();
        $transaction->user_id = $withdraw->user_id;
        $transaction->user_type = $withdraw->user_type;
        $transaction->wallet_id = $wallet->id;
        $transaction->currency_id = $withdraw->currency_id;
        $transaction->before_charge = $withdraw->amount;
        $transaction->amount = $withdraw->amount;
        $transaction->post_balance = $wallet->balance;
        $transaction->charge = 0;
        $transaction->charge_type = '+';
        $transaction->trx_type = '-';
        $transaction->operation_type = 'withdraw_money';
        $transaction->details = 'Money withdrawal';
        $transaction->trx =  $withdraw->trx;
        $transaction->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = userGuard()['user']->id;
        $adminNotification->user_type = $withdraw->user_type;
        $adminNotification->title = 'New withdraw request from '.userGuard()['user']->username;
        $adminNotification->click_url = urlPath('admin.withdraw.details',$withdraw->id);
        $adminNotification->save();

        $general = GeneralSetting::first();

        notify(userGuard()['user'], 'WITHDRAW_REQUEST', [
            'method_name' => $withdraw->method->name,
            'method_currency' => $wallet->currency->currency_code,
            'method_amount' => showAmount($withdraw->final_amount,$general->currency),
            'amount' => showAmount($withdraw->amount,$general->currency),
            'charge' => showAmount($withdraw->charge,$general->currency),
            'currency' => $wallet->currency->currency_code,
            'trx' => $withdraw->trx,
            'post_balance' => showAmount($wallet->balance,$general->currency),
        ]);

        $notify[] = ['success', 'Withdraw request sent successfully'];
        return redirect()->route(strtolower(userGuard()['type']).'.withdraw.history')->withNotify($notify);
    }



}
