@extends($activeTemplate.'layouts.user_master')
@push('style')
    <style>
        .select_method{
            position: relative;
        }
        .list_user_method{
            position: absolute;
            display: none;
            border: 1px solid #dee2e6;
            border-bottom-left-radius: 5px;
            border-bottom-right-radius: 5px;
            width: 100%;
            background: #fff;
            z-index: 999;
        }
        .selected_method,.method-item{
            padding: 5px;
            border-top: 1px solid #dee2e6;
            cursor: pointer;
        }
        .selected_method .wallet-amount:after{
            display: block;
            content: "\f107";
            font-weight: 900;
            margin-left: 5px;
            font-family: 'Line Awesome Free';
        }
        .selected_method.active{
            border-bottom-color: transparent;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        .selected_method.active .wallet-amount:after{
            content: "\f106";
        }
        .method-item:hover{
            background: #ebebeb;
        }
        .selected_method{
            border: 1px solid #dee2e6;
        }
        .method-item:last-of-type{

        }
        .method-item:first-of-type{
            border-top: 0;
        }
        .selected_method img,.method-item img{
            height: 40px;
        }
        .selected_method .wallet-amount,.method-item .wallet-amount{
            font-size: 13px;
        }
        #amount{
            border: 0;
            text-align: center;
            font-size: 60px;
            color: #333;
        }
        #amount:focus,#amount:active{
            outline: none;
            box-shadow: none;
        }
        .min_value, .max_value{
            font-weight: bold;
        }
        .min_max_amount{
            background: #e7e7e7;
            padding: 2px 10px;
            border-radius: 5px;
            font-size: 13px;
        }
        .icon-withdraw{
            font-size: 100px;
        }
        .list-account{}
        .account-item{
            cursor: pointer;
            border-top: 1px solid #dee2e6;
        }
        .account-select{}
        .account-item.active .account-select{
            position: relative;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            background: #399369;
        }
        .account-item.active .account-select:after{
            position: absolute;
            transform: rotate(45deg);
            height: 25px;
            width: 12px;
            border-bottom: 7px solid white;
            border-right: 7px solid white;
            display: block;
            content: '';
            top: 5px;
            left: 14px;
        }
        .account-detail{}
        .account-title{}
        .account-meta{
            font-size: 13px;
        }
        .account-meta p{}
        .account-meta p strong{}
        .add-new-wallet{
            color: #f16304;
        }
        .add-new-wallet i{
            display: inline-block;
            background: #f16304;
            font-size: 20px;
            padding: 5px;
            border-radius: 50%;
            color: #fff;
        }
        .add-new-wallet-btn{
            border-top: 1px solid #dee2e6;
        }
    </style>
@endpush
@section('content')
    <div class="col-xl-10 mx-auto one-step">
        @forelse ($withdrawMethods as $withdraw_method)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2 justify-content-center align-items-center d-flex">

                            <img src="{{ getImage('assets/images/withdraw/' .$withdraw_method->image) }}" class="card-img w-50">

                            </div>
                            <div class="col-md-7">
                                <p class="text-center text-lg-start"><strong>{!! $withdraw_method->name !!}</strong></p>
                                <div>
                                    {!! $withdraw_method->description !!}
                                </div>
                            </div>
                            <div class="col-md-3 d-flex justify-content-center align-items-center">
                                <a data-type="{!! $withdraw_method->currency->currency_type !!}" data-code="{!! $withdraw_method->currency->currency_code !!}" data-rate="{!! $withdraw_method->currency->rate !!}" class="btn btn-primary btn-sm select-gateway" href="javascript:void(0)" data-id="{!! $withdraw_method->id !!}" >@lang('Withdraw Now')</a>
                            </div>
                        </div>
                    </div>
                </div>
        @empty
        @endforelse
    </div>
    @foreach ($withdrawMethods as $withdraw_method)
    <div class="col-10 mx-auto select-account select-account-{!! $withdraw_method->id !!}" style="display: none">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-body p-0">
                        <p class="p-3"><strong>@lang('Select Wallet')</strong></p>
                        <div class="list-account">
                            @foreach($userMethods as $user_account)
                            @if($user_account->method_id == $withdraw_method->id)
                                @include($activeTemplate.'user.withdraw.account')
                            @endif
                            @endforeach
                        </div>
                        <div class="add-new-wallet-btn p-3">
                            <a href="javascript:void(0)" class="add-new-wallet" data-id="{!! $withdraw_method->id !!}"><i class="las la-plus"></i> @lang('Add wallet')</a>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <button class="btn btn-secondary btn-sm px-4 btn-back-gateway" type="button">@lang('Back')</button>
                    <button class="btn btn--primary btn-sm px-4 btn-next-amount" type="button">@lang('Next')</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    <div class="col-md-6 mx-auto preview-step" style="display: none">
        <div class="first-step">
            <div class="card">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-10 mx-auto pb-5">
                            <p class="text-center mt-3"><strong>@lang('Enter amount')</strong></p>
                            <input data-max="" autocomplete="off" id="amount" type="text"  class="form--control h-auto" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" name="amount" placeholder="0.00" required>
                            <p class="text-center"><span class="min_max_amount">@lang('Minimum')* <span class="min_value"></span></span></p>
                            <p class="text-center mt-2"><span class="min_max_amount">@lang('Limit')* <span class="max_value"></span></span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <button class="btn btn-secondary btn-sm px-4 btn-back-account" type="button">@lang('Back')</button>
                <button type="button" class="btn btn--primary btn-md req_confirm btn-withdraw-confirm"><i class="las la-wallet font-size--18px"></i> @lang('Submit')</button>
            </div>
        </div>
        @php
            $exchangeCharge = \App\Models\TransactionCharge::where('slug','exchange_charge')->first();
        @endphp
        <input type="hidden" class="exchange_percent" value="{!! $exchangeCharge->percent_charge !!}">
        <input type="hidden" class="exchange_fixed" value="{!! $exchangeCharge->fixed_charge !!}">
        <div class="second-step" style="display: none">
            <form action="{{route('user.withdraw.store')}}" method="post">
                @csrf
                <div class="card">
                    <div class="card-body p-0 pb-5">
                        <input type="hidden" name="method_id" class="method" value="">
                        <input type="hidden" name="user_method_id" class="user_method" value="">
                        <input type="hidden" name="fixed_charge" class="fixed_charge" value="">
                        <input type="hidden" name="percent_charge" class="percent_charge" value="">
                        <input type="hidden" name="currency_rate" class="currency_rate" value="">
                        <input type="hidden" name="currency_code" class="currency_code" value="">
                        <input type="hidden" name="withdraw_fee" class="withdraw_fee_amount" value="0">
                        <input type="hidden" name="amount_total" class="amount_total" value="">
                        <input type="hidden" name="amount" class="final_amount" value="0" required>
                        <p class="text-center icon-withdraw"><i class="las la-money-bill-wave"></i></p>
                        <div class="d-flex justify-content-between px-4">
                            <div>@lang('Money arrives')</div>
                            <div><strong>@lang('Instant')</strong></div>
                        </div>
                        <hr class="mt-1">
                        <div class="d-flex justify-content-between px-4">
                            <div>@lang('Requested Amount')</div>
                            <div><strong class="final_text_amount"></strong></div>
                        </div>
                        <div class="d-flex justify-content-between px-4">
                            <div>@lang('You will get')</div>
                            <div><strong class="receive_amount"></strong></div>
                        </div>
                        <div class="d-flex justify-content-between px-4">
                            <div>@lang('Fee')</div>
                            <div><strong class="withdraw_fee"></strong></div>
                        </div>
                        <div class="d-flex justify-content-between px-4">
                            <div>@lang('Exchange Rate')</div>
                            <div><strong class="exchange_rate"></strong></div>
                        </div>
                        <div class="d-flex justify-content-between px-4">
                            <div>@lang('Your balance will be')</div>
                            <div><strong class="deduce_balance"></strong></div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <button class="btn btn-secondary btn-sm px-4 btn-back-withdraw" type="button">@lang('Back')</button>
                    <button class="btn btn--primary btn-sm px-4" type="submit">@lang('Confirm')</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade modal-add-method" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add Withdraw Method')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        jQuery(document).ready(function ($){
            var oldAmount = parseFloat($('.final_amount').val());
            var limit_balance = '@lang('You do not have sufficient balance for withdraw')';
            var follow_limit = '@lang('Please follow the limits')';
            var select_withdraw = '@lang('Select wallet account for withdrawal')';
            var modalOpening = false;
            var from_currency_code = '';
            var from_currency_rate = 0;
            var from_currency_type = 1;
            var to_currency_code = '';
            var to_currency_rate = 0;
            var to_currency_type = 1;
            $(document).on('submit','#formAddMethod', function (){
                var data = $(this).serialize();
                modalOpening = true;
                var method_id = $('#formAddMethod input[name=method_id]').val();
                $.ajax({
                    url: '{!! route('user.withdraw.savemethod') !!}',
                    data: data,
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function (){
                        $('#formAddMethod .btn--base').attr('disabled','disabled');
                    },
                    success: function(res){
                        modalOpening = false;
                        $('#formAddMethod .btn--base').removeAttr('disabled');
                        console.log(res.message);
                        if(typeof res.message != 'undefined'){
                            alert(res.message);
                        }
                        else{
                            $('.modal-add-method').modal('hide');
                            $('.select-account-'+method_id+' .list-account').empty().html(res.html);
                        }
                    }
                })
                return false;
            })
            $(document).on('click','.account-item',function(e){
                if(!$(e.target).hasClass('delete-account-method')) {
                    var el = $(e.currentTarget);
                    $('.account-item').removeClass('active');
                    el.addClass('active');
                    var symbol = el.attr('data-currency-symbol');
                    var code = el.attr('data-currency');
                    var rate = el.attr('data-rate');
                    var type = el.attr('data-type');
                    var amount = el.attr('data-amount');
                    var method_id = el.attr('data-method-id');
                    var id = el.attr('data-id');
                    var min_value = el.attr('data-min-limit');
                    var max_value = el.attr('data-max-limit');
                    var fixed_charge = el.attr('data-fixed-charge');
                    var percent_charge = el.attr('data-percent-charge');
                    var amountFix = amount.replaceAll(',', '');
                    var maxValueFix = max_value.replaceAll(',', '');
                    if (maxValueFix > amountFix) {
                        max_value = amount;
                        maxValueFix = amountFix;
                    }
                    to_currency_code = code;
                    to_currency_rate = rate;
                    to_currency_type = type;
                    $('.min_value').html(symbol + min_value);
                    $('.max_value').html(symbol + max_value);
                    $('.user_method').val(id);
                    $('.fixed_charge').val(fixed_charge);
                    $('.amount_total').val(amountFix);
                    $('.percent_charge').val(percent_charge);
                    $('.currency_rate').val(rate);
                    $('.currency_code').val(code);
                    $('.method').val(method_id);
                    $('#amount').val('');
                    $('#amount').attr('data-max', maxValueFix);
                }
            });
            $('.btn-next-amount').click(function (){
                var method_id = $('.method').val();
                if(method_id === ''){
                    alert(select_withdraw);
                }
                else{
                    $('.select-account').hide();
                    $('.preview-step').show();
                }
            });
            function formatToCurrencyFix(amount, number){
                return (amount).toFixed(number).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }
            function formatToCurrency(amount){
                return (amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }
            $('.selected_method').click(function (){
                $(this).toggleClass('active');
                $('.list_user_method').slideToggle('fast');
            });
            $(document).click(function(e){
                if($(e.target).closest('.selected_method').length === 0){
                    $('.selected_method').removeClass('active');
                    $('.list_user_method').slideUp('fast');
                }
            });
            $('.btn-back-account').click(function (){
                var method_id = $('.method').val();
                $('.preview-step').hide();
                $('.select-account-'+method_id).show();
            })
            $('.btn-withdraw-confirm').click(function(){
                var amount = parseFloat($('#amount').val());
                if(amount > 0){
                    $('.first-step').slideUp();
                    $('.second-step').slideDown();
                }
                else{
                    alert(follow_limit);
                }
            });
            $('.btn-back-gateway').click(function (){
                $('.one-step').show();
                $('.account-item').removeClass('active');
                $('.select-account').hide();
            });
            $('.btn-back-withdraw').click(function(){
                $('.first-step').slideDown();
                $('.second-step').slideUp();
            });
            $('.select-gateway').click(function(){
                var id = $(this).attr('data-id');
                from_currency_rate = $(this).attr('data-rate');
                from_currency_code = $(this).attr('data-code');
                from_currency_type = $(this).attr('data-type');
                $('.one-step').hide();
                $('.select-account').hide();
                $('.select-account-'+id).show();
                $('#method-item-'+id).click();
                // $('.preview-step').show();
            });
            $('.add-new-wallet').click(function(){
                var id = $(this).attr('data-id');
                var data = {_token: '{!! csrf_token() !!}', id: id};
                if(!modalOpening) {
                    modalOpening = true;
                    $.ajax({
                        url: '{!! route('user.withdraw.addmethod') !!}',
                        data: data,
                        type: 'POST',
                        success: function (res) {
                            modalOpening = false;
                            $('.modal-add-method .modal-body').html(res);
                            $('.modal-add-method').modal('show');
                        }
                    })
                }
            });
            $(document).on('click','.delete-account-method', function(e){
                var el = $(e.currentTarget);
                var conf = confirm('@lang('Are you sure?')');
                var id = el.attr('data-id');
                var exist_method = $('.method').val();
                if(conf) {
                    if(exist_method == id){
                        $('.method').val('');
                    }
                    var data = {_token: '{!! csrf_token() !!}', id: id}
                    el.parent().parent().remove();
                    $.ajax({
                        url: '{!! route('user.withdraw.deletemethod') !!}',
                        data: data,
                        type: 'POST',
                        success: function (res){

                        }
                    })
                }

            });
            $(document).on('change','.add_select_currency', function(e){
                var el = $(e.currentTarget);
                var name = el.attr('data-name');
                var currency = el.find('option:selected').text().trim();
                $('#formAddMethod input[name=name]').val(name+' - '+currency);
            });
            $('.method-item').click(function(){
                var symbol = $(this).attr('data-currency-symbol');
                var code = $(this).attr('data-currency');
                var rate = $(this).attr('data-rate');
                var amount = $(this).attr('data-amount');
                var method_id = $(this).attr('data-method-id');
                var id = $(this).attr('data-id');
                var min_value = $(this).attr('data-min-limit');
                var max_value = $(this).attr('data-max-limit');
                var fixed_charge = $(this).attr('data-fixed-charge');
                var percent_charge = $(this).attr('data-percent-charge');
                var amountFix = amount.replaceAll(',','');
                var maxValueFix = max_value.replaceAll(',','');
                if(maxValueFix > amountFix){
                    max_value = amount;
                    maxValueFix = amountFix;
                }
                $('.min_value').html(symbol+min_value);
                $('.max_value').html(symbol+max_value);
                $('.user_method').val(id);
                $('.fixed_charge').val(fixed_charge);
                $('.amount_total').val(amountFix);
                $('.percent_charge').val(percent_charge);
                $('.currency_rate').val(rate);
                $('.currency_code').val(code);
                $('.method').val(method_id);
                $('#amount').val('');
                $('#amount').attr('data-max',maxValueFix);
                $('.selected_method').html($(this).html());
            });
            $('#amount').on('keyup', function(e){
                var thisVal = parseFloat($(this).val());
                var fixed_charge = parseFloat($('.fixed_charge').val());
                var percent_charge = parseFloat($('.percent_charge').val());
                var currency_rate = parseFloat($('.currency_rate').val());
                var currency_code = $('.currency_code').val();
                var max_value = parseFloat($(this).attr('data-max'));
                var amount_total = $('.amount_total').val();
                var fee = fixed_charge/currency_rate + thisVal*percent_charge/100;
                if(from_currency_code !== to_currency_code){
                    var exchange_percent = parseFloat($('.exchange_percent').val());
                    var exchange_fixed = parseFloat($('.exchange_fixed').val());
                    if(exchange_percent > 0){
                        fee += thisVal*exchange_percent/100;
                    }
                    if(exchange_fixed > 0){
                        fee += exchange_fixed/currency_rate;
                    }
                }
                if(thisVal > max_value){
                    $(this).val(oldAmount);
                    alert(limit_balance);
                }
                else{
                    oldAmount = thisVal;
                    var precesion = 2;
                    if(from_currency_type === 2){
                        precesion = 8;
                    }
                    if(from_currency_code !== to_currency_code){
                        var rateAmount = to_currency_rate/from_currency_rate;
                        var receive_amount = (thisVal-fee)*rateAmount;
                        $('.currency_rate').val(rateAmount);
                        $('.receive_amount').html(formatToCurrencyFix(receive_amount,precesion)+' '+from_currency_code);
                        if(to_currency_code === 'USD'){
                            $('.exchange_rate').html('1'+to_currency_code+' = '+formatToCurrencyFix(1/from_currency_rate,precesion)+from_currency_code);
                        }
                        else{
                            $('.exchange_rate').html('1'+from_currency_code+' = '+formatToCurrencyFix(1/to_currency_rate,precesion)+to_currency_code);
                        }
                    }
                    else{
                        $('.exchange_rate').html(formatToCurrency(currency_rate)+' '+currency_code);
                        $('.receive_amount').html(formatToCurrency(thisVal-fee)+' '+currency_code);
                    }
                    $('.final_text_amount').html(formatToCurrency(thisVal)+' '+currency_code);
                    $('.withdraw_fee').html(formatToCurrency(fee)+' '+currency_code);
                    $('.withdraw_fee_amount').val(fee);
                    $('.deduce_balance').html(formatToCurrency(amount_total - thisVal)+' '+currency_code);
                    $('.final_amount').val(thisVal);
                }
            })
        })
    </script>
@endpush
