<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\WithdrawProcess;
use App\Models\UserDipositMethod;
use App\Models\Withdrawal;
use App\Models\WithdrawMethod;
use App\Models\UserDipositInfo;
use App\Models\Gateway;
use App\Models\Currency;

use Illuminate\Http\Request;

class UserDipositeController extends Controller{
    
    use WithdrawProcess;


    public function PaymentGateways()
    {

        $pageTitle = 'List Payment Gateways';
        $gateways = Gateway::with('currencies')->get();

        $guard = userGuard()['guard'];
       // $gateways = Gateway::whereJsonContains('user_guards', "$guard")->where('status', 1)->get();
       // $currencies = Currency::pluck('id', 'currency_code'); 
        return view($this->activeTemplate . strtolower(userGuard()['type']) . '.diposite.payments', compact('pageTitle', 'gateways'));
 

    }
public function addDipositeMethod(Request $request){        //dd($request->name);
    
      
        $pageTitle = 'List Payment Gateways';


           if(!empty($request->input('getway_name'))){
        $userMethod = new UserDipositMethod();
       // $userMethod->name = $request->name;
        $userMethod->name = userGuard()['user']->email;

        $userMethod->user_id = userGuard()['user']->id;
        $userMethod->user_type = userGuard()['type'];
        $userMethod->getway_id = $request->getway_id;
        $userMethod->getway_name = $request->getway_name;
       // $userMethod->user_data = $storeHelper['user_data'];
$Results = UserDipositMethod::where(['getway_id'=> $request->getway_id, 'name'=> userGuard()['user']->email])->count();



        if(empty($Results)){

               $userMethod->save();


        }

        $gateways = UserDipositMethod::where(['getway_id'=> $request->getway_id])->get();


    return view($this->activeTemplate . strtolower(userGuard()['type']) . '.diposite.payment_next', compact('pageTitle', 'gateways'));
   } else{

    $notify[] = ['error', 'gateways not found'];
            return back()->withNotify($notify);
       


    }

    }

    
    public function addNewDipositeMethod(Request $request){       
    
      
    $pageTitle = "Add Withdraw Method";
    $guard = userGuard()['guard'];

           if(!empty($request->input('getway'))){

$gateways = UserDipositMethod::where(['getway_id'=> $request->getway_id, 'name'=> userGuard()['user']->email])->first();
//dd($gateways);
    return view($this->activeTemplate . strtolower(userGuard()['type']) . '.diposite.add_method_page', compact('pageTitle', 'gateways'));

    } else if(!empty($request->input('Next'))){

$gateways = UserDipositMethod::where(['getway_id'=> $request->getway_id, 'name'=> userGuard()['user']->email])->first();
//dd($gateways);
    return view($this->activeTemplate . strtolower(userGuard()['type']) . '.diposite.add_method_page', compact('pageTitle', 'gateways'));



     else{

    $notify[] = ['error', 'gateways not found'];
            return back()->withNotify($notify);
       


        }


       
       }



public function addDipositeInfoMethod(Request $request){        //dd($request->name);
    
      
        $pageTitle = 'List Payment Gateways';


           if(!empty($request->input('getway_name'))){
        $userMethod = new UserDipositInfo();
        $userMethod->user_email = userGuard()['user']->email;
        $userMethod->user_id = userGuard()['user']->id;
        $userMethod->user_type = userGuard()['type'];
        $userMethod->getway_id = $request->getway_id;
        $userMethod->getway_name = $request->getway_name;
        $userMethod->nick_name = $request->nick_name;
        $userMethod->bank_name = $request->bank_name;

        $userMethod->account_holder = $request->account_holder;
        $userMethod->branch_code = $request->branch_code;
        $userMethod->account_number = $request->account_number;




       // $userMethod->user_data = $storeHelper['user_data'];
        $Results = UserDipositInfo::where(['getway_id'=> $request->getway_id, 'user_email'=> userGuard()['user']->email])->count();



        if(empty($Results)){

               $userMethod->save();


        }

        $gateways = UserDipositMethod::where(['getway_id'=> $request->getway_id])->first();


    return view($this->activeTemplate . strtolower(userGuard()['type']) . '.diposite.payment_next', compact('pageTitle', 'gateways'));
   } else{

    $notify[] = ['error', 'gateways not found'];
            return back()->withNotify($notify);
       


    }

    }

        

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
        $userMethods = UserWithdrawMethod::myWithdrawMethod()->whereHas('withdrawMethod')->with('withdrawMethod', 'currency')->paginate(getPaginate());
        $pageTitle = 'Withdraw Methods';
        return view($this->activeTemplate . 'user.withdraw.methods', compact('pageTitle', 'userMethods'));
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
}
