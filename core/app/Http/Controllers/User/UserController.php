<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Models\Form;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Gateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller{ 

    public function home(){
        
        $pageTitle = 'Dashboard';
        $user = auth()->user();

        $wallets = $user->topTransactedWallets()->with('currency');

        $totalBalance[] = 0;
        foreach ($wallets->get() as $wallet) {
            $totalBalance[] = $wallet->balance * $wallet->currency->rate;
        }

        $wallets = $wallets->take(3)->get();
        $totalMoneyInOut = $user->moneyInOut();

        $kyc = $user->kycStyle();
        $histories = Transaction::where('user_id', $user->id)->where('user_type', 'USER')
            ->with('currency', 'receiverUser', 'receiverAgent', 'receiverMerchant')->orderBy('id', 'desc')->take(10)
        ->get();

        return view($this->activeTemplate . 'user.dashboard', compact('pageTitle', 'wallets', 'totalBalance',  'totalMoneyInOut', 'kyc', 'user', 'histories'));
    } 

    public function depositHistory(){ 
        $pageTitle = 'Add Money History';
        $deposits = auth()->user()->deposits()->searchable(['trx'])->with(['gateway'])->orderBy('id','desc')->with('currency')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.deposit_history', compact('pageTitle', 'deposits'));
    }


    public function PaymentGateways()
    {
        $pageTitle = 'List Payment Gateways';
        $gateways = Gateway::with('currencies')->get();
       // dd($gateways);
        return view($this->activeTemplate . 'user.payments', compact('pageTitle', 'gateways'));

        //return view('admin.gateways.automatic.list', compact('pageTitle', 'gateways'));
    }

    

    public function PaymentsGateway(Request $request)
    {
        $pageTitle = 'Payment Gateways';

        $userMethods = UserDipositMethod::myWithdrawMethod()->with('withdrawMethod', 'currency')->get();
        return view($this->activeTemplate . 'user.diposits', compact('pageTitle', 'userMethods'));
        
       

        //return view('admin.gateways.automatic.list', compact('pageTitle', 'gateways'));
    }


    public function depositInsert(Request $request){

        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'method_code' => 'required',
            'currency' => 'required',
        ]);

        $user = userGuard()['user'];
        $currency = Currency::enable()->find($request->currency_id);
        
        if(!$currency) {
            $notify[] = ['error', 'Invalid currency'];
            return back()->withNotify($notify);
        }

        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->where('method_code', $request->method_code)->where('currency', $request->currency)->first();

        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }
        
        if ($gate->min_amount / $currency->rate > $request->amount || $gate->max_amount / $currency->rate < $request->amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $charge = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        $payable = $request->amount + $charge;
        $final_amo = $payable;
        $data = new Deposit();
        $data->user_id = $user->id;

        $data->user_type = userGuard()['type'];
        $data->wallet_id = $request->wallet_id;
        $data->currency_id = $request->currency_id;

        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $request->amount;
        $data->charge = $charge;
        $data->rate = 1;
        $data->final_amo = $final_amo;
        $data->btc_amo = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->save();

        session()->put('Track', $data->trx);
        return to_route(strtolower(userGuard()['type']).'.deposit.confirm');
    } 

   
    public function depositConfirm()
    {   
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status',0)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return to_route(strtolower(userGuard()['type']).'.deposit.manual.confirm');
        }


        $dirName = $deposit->gateway->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);


        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if(@$data->session){
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view($this->activeTemplate . $data->view, compact('data', 'pageTitle', 'deposit'));
    }




    public function show2faForm(){
        $general = gs();
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $general->site_name, $secret);
        $pageTitle = '2FA Setting';
        return view($this->activeTemplate . 'user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl', 'user'));
    }

    public function create2fa(Request $request){

        $user = auth()->user();

        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);
        
        $response = verifyG2fa($user, $request->code, $request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts = 1;
            $user->save();
            $notify[] = ['success', 'Google authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request){
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user = auth()->user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts = 0;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function transactions(Request $request){
        $request->search ? $pageTitle = "Search Result of #$request->search" : $pageTitle = "Transaction History";
        $user = auth()->user();
        $transactions = $user->trxLog($request);
        return view($this->activeTemplate . 'user.transactions', compact('pageTitle', 'transactions'));
    }

    public function kycForm(){
        if (auth()->user()->kv == 2) {
            $notify[] = ['error', 'Your KYC is under review'];
            return to_route('user.home')->withNotify($notify);
        }
        if (auth()->user()->kv == 1) {
            $notify[] = ['error', 'You are already KYC verified'];
            return to_route('user.home')->withNotify($notify);
        }

        $pageTitle = 'KYC Form';
        $form = Form::where('act', 'user_kyc')->first();
        return view($this->activeTemplate . 'user.kyc.form', compact('pageTitle', 'form'));
    }

    public function kycData(){
        $user = auth()->user();
        $pageTitle = 'KYC Data';
        return view($this->activeTemplate . 'user.kyc.info', compact('pageTitle', 'user'));
    }

    public function kycSubmit(Request $request){
        $form = Form::where('act', 'user_kyc')->first();
        $formData = $form->form_data;
        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);
        $user = auth()->user();
        $user->kyc_data = $userData;
        $user->kv = 2;
        $user->save();

        $notify[] = ['success', 'KYC data submitted successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function attachmentDownload($fileHash){
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $general = gs();
        $title = slug($general->site_name) . '- attachments.' . $extension;
        $mimetype = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function userData(){
        $user = auth()->user();
        if ($user->profile_complete == 1) {
            return to_route('user.home');
        }
        $pageTitle = 'User Data';
        return view($this->activeTemplate . 'user.user_data', compact('pageTitle', 'user'));
    }

    public function userDataSubmit(Request $request){
        $user = auth()->user();
        if ($user->profile_complete == 1) {
            return to_route('user.home');
        }
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
        ]);
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->address = [
            'country' => @$user->address->country,
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'city' => $request->city,
        ];
        $user->profile_complete = 1;
        $user->save();

        $notify[] = ['success', 'Registration process completed successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function qrCode(){
        $pageTitle = 'QR Code';
        $user = userGuard()['user'];
        $qrCode = $user->createQrCode();
        $uniqueCode = $qrCode->unique_code;
        $qrCode = cryptoQR($uniqueCode);
        return view($this->activeTemplate . 'user.qr_code', compact('pageTitle', 'qrCode', 'uniqueCode'));
    }

    public function downLoadQrCodeJpg(){
        $user = userGuard()['user'];
        $qrCode = $user->downLoadQrCode();
        return $qrCode;
    }

    public function checkInsight(Request $request){
        if ($request->day) {
            $totalMoneyInOut = auth()->user()->moneyInOut($request->day);
            return response()->json($totalMoneyInOut);
        }
        return response()->json(['error' => 'Sorry can\'t process your request right now']);
    }

    public function wallets(){
        $pageTitle = "All Wallets";
        $wallets = Wallet::hasCurrency()->where('user_id', auth()->user()->id)->where('user_type', 'USER')
            ->with('currency')->orderBy('balance', 'DESC')
        ->get();
        return view($this->activeTemplate . 'user.wallets', compact('pageTitle', 'wallets'));
    }

    public function logoutOtherDevicesForm(){
        $pageTitle = 'Logout From Other Devices';
        return view($this->activeTemplate . 'user.logout_form', compact('pageTitle'));
    }

    public function logoutOtherDevices(Request $request){
        
        $request->validate([
            'password'=>'required'
        ]);

        $password = $request->password;

        if (!Hash::check($password, auth()->user()->password)) {
            $notify[] = ['error', 'The password doesn\'t match!'];
            return back()->withNotify($notify);
        }

        Auth::logoutOtherDevices($password);

        $notify[] = ['success', 'Successfully logged out from other devices'];
        return back()->withNotify($notify);
    }

}
