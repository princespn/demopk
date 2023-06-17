<?php

use Illuminate\Support\Facades\Route;

Route::namespace('User\Auth')->name('user.')->group(function () {

    Route::controller('LoginController')->group(function(){
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->name('logout');
    });

    Route::controller('RegisterController')->group(function(){
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register')->middleware('registration.status');
        Route::post('check-mail', 'checkUser')->name('checkUser');
    });

    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function(){
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });

    Route::controller('ResetPasswordController')->group(function(){
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });
}); 

Route::middleware('auth')->name('user.')->group(function () {
    //authorization
    Route::namespace('User')->controller('AuthorizationController')->group(function(){
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('go2fa.verify');
    });
  
    Route::middleware(['check.status'])->group(function () {

        Route::get('user-data', 'User\UserController@userData')->name('data');
        Route::post('user-data-submit', 'User\UserController@userDataSubmit')->name('data.submit');

        Route::middleware('registration.complete')->namespace('User')->group(function () {

            Route::controller('UserController')->group(function(){
                Route::get('dashboard', 'home')->name('home');
                Route::get('check/insight', 'checkInsight')->name('check.insight');

                //QR code
                Route::get('/qr-code', 'qrCode')->name('qr.code');
                Route::get('/download/qr-code/as/jpg', 'downLoadQrCodeJpg')->name('qr.code.jpg');

                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');

                //KYC
                Route::get('kyc-form','kycForm')->name('kyc.form');
                Route::get('kyc-data','kycData')->name('kyc.data');
                Route::post('kyc-submit','kycSubmit')->name('kyc.submit');

                //Report
                Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                Route::get('transactions','transactions')->name('transactions');                
                Route::get('attachment-download/{filHash}','attachmentDownload')->name('attachment.download');

                Route::get('logout-other-devices', 'logoutOtherDevicesForm')->name('logout.other.devices.form');
                Route::post('logout-other-devices', 'logoutOtherDevices')->name('logout.other.devices');
            });

            //Profile setting
            Route::controller('ProfileController')->group(function(){
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::post('profile-setting', 'submitProfile');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
            });

           
            
           

            //Money out
            Route::controller('MoneyOutController')->middleware(['module:money_out', 'kyc'])->group(function(){
                Route::post('/agent/exist', 'checkUser')->name('agent.check.exist');
                Route::get('/money-out', 'moneyOut')->name('money.out');
                Route::post('/money-out', 'moneyOutConfirm');
                Route::get('/money-out-done', 'moneyOutDone')->name('money.out.done');
            });

            //Make payment
            Route::controller('MakePaymentController')->middleware(['module:make_payment', 'kyc'])->group(function(){
                Route::post('/merchant/exist', 'checkUser')->name('merchant.check.exist');
                Route::get('/make-payment', 'paymentFrom')->name('payment');
                Route::post('/make-payment', 'paymentConfirm');
                Route::get('/make-payment-done', 'paymentDone')->name('payment.done');
            });

            //Transfer money
            Route::controller('UserOperationController')->middleware(['module:transfer_money', 'kyc'])->group(function(){
                Route::get('/transfer/money', 'transfer')->name('transfer');
                Route::post('/transfer/money', 'transferMoney');
                Route::get('/transfer/money-done', 'transferMoneyDone')->name('transfer.done');
                Route::post('/user/exist', 'checkUser')->name('check.exist');
            });

            //More Wallets
            Route::get('/wallets', 'UserController@wallets')->name('wallets');
  
            //Request Money
            Route::controller('UserOperationController')->middleware('module:request_money')->group(function(){
                Route::get('/requests', 'allRequests')->name('requests');
                Route::get('/my/requested/history', 'requestedHistory')->name('request.money.history');
                Route::get('/request/money', 'requestMoney')->name('request.money');
                Route::post('/request/money', 'confirmRequest')->middleware('kyc');
                Route::post('/accept/request', 'requestAccept')->name('request.accept');
                Route::get('/accept/done', 'requestAcceptDone')->name('request.accept.done');
                Route::post('/accept/reject', 'requestReject')->name('request.reject');
            });

            //Invoice
            Route::controller('InvoiceController')->middleware('module:create_invoice')->prefix('invoice')->name('invoice')->group(function(){
                Route::get('/all', 'invoices')->name('.all');
                Route::get('/create', 'createInvoice')->name('.create');
                Route::post('/create', 'createInvoiceConfirm');
                Route::get('/edit/{invoiceNum}', 'editInvoice')->name('.edit');
                Route::post('/update/', 'updateInvoice')->name('.update');
                Route::get('/send-to-mail/{invoiceNum}', 'sendInvoiceToMail')->name('.send.mail');
                Route::get('/publish/{invoiceNum}', 'publishInvoice')->name('.publish');
                Route::post('/discard/{invoiceNum}', 'discardInvoice')->name('.discard');
                Route::get('/payment/confirm/done', 'invoicePaymentConfirmDone')->name('.payment.confirm.done');
                Route::get('/payment/confirm/{invoiceNum}', 'invoicePaymentConfirm')->name('.payment.confirm');
            });

            //Voucher
            Route::controller('VoucherController')->middleware('module:create_voucher')->group(function(){
                Route::get('/voucher/list', 'userVoucherList')->name('voucher.list');
                Route::get('/create/voucher', 'userVoucher')->name('voucher.create')->middleware('kyc');
                Route::post('/create/voucher', 'userVoucherCreate')->middleware('kyc');
                Route::get('/create/voucher-done', 'userVoucherCreateDone')->name('voucher.create.done')->middleware('kyc');
                Route::get('/voucher/redeem', 'userVoucherRedeem')->name('voucher.redeem');
                Route::post('/voucher/redeem', 'userVoucherRedeemConfirm');
                Route::get('/voucher/redeem/log', 'userVoucherRedeemLog')->name('voucher.redeem.log');
            });

            //Exchange money
            Route::controller('MoneyExchangeController')->middleware('module:money_exchange')->prefix('exchange')->name('exchange')->group(function(){
                Route::get('/money', 'exchangeForm')->name('.money');
                Route::post('/money', 'exchangeConfirm');
            });

        });

        // Payment
        Route::middleware('registration.complete')->prefix('deposit')->controller('Gateway\PaymentController')->group(function(){
            Route::any('/', 'deposit')->name('deposit');
            Route::post('insert', 'depositInsert')->name('deposit.insert');
            Route::get('confirm', 'depositConfirm')->name('deposit.confirm');
            Route::get('manual', 'manualDepositConfirm')->name('deposit.manual.confirm');
            Route::post('manual', 'manualDepositUpdate')->name('deposit.manual.update');
        });

    });
});
