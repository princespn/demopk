<?php
$wallet = App\Models\Wallet::hasCurrency()->where('user_type',userGuard()['type'])->where('user_id',userGuard()['user']->id)->where('currency_id',$user_account->currency->id)->first();
$max_limit = $user_account->withdrawMethod->max_limit/$user_account->currency->rate;
if($max_limit > $wallet->balance){
    $max_limit = $wallet->balance;
}
?>
<div class="account-item d-flex justify-content-between p-3 align-items-center"
     data-method-id="{!! $user_account->method_id !!}"
     data-id="{!! $user_account->id !!}"
     data-rate="{{$user_account->currency->rate}}"
     data-type="{{$user_account->currency->currency_type}}"
     data-amount="{{showAmount($wallet->balance)}}"
     data-percent-charge="{{$user_account->withdrawMethod->percent_charge}}"
     data-fixed-charge="{{$user_account->withdrawMethod->fixed_charge}}"
     data-currency-symbol="{{$user_account->currency->currency_symbol}}"
     data-currency="{{$user_account->currency->currency_code}}"
     data-min-limit="{{showAmount($user_account->withdrawMethod->min_limit/$user_account->currency->rate,$user_account->currency)}}"
     data-max-limit="{{showAmount($max_limit,$user_account->currency)}}"
>
    <div class="account-detail">
        <p class="account-title"><strong>{!! $user_account->name !!}</strong></p>
        <div class="account-meta">
            @foreach((array)$user_account->user_data as $k => $v)
                <p><strong>{{__(ucwords(str_replace('_',' ',$k)))}}: </strong>{!! $v->field_name !!}</p>
            @endforeach
        </div>
        <a href="javascript:void(0)" class="text-danger delete-account-method font-size--14px" data-id="{!! $user_account->id !!}"><i class="las la-trash"></i> @lang('Delete')</a>
    </div>
    <div class="account-select"></div>
</div>
