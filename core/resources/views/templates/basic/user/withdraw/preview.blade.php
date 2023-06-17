@extends($activeTemplate.'layouts.user_master')

@section('content')
    <div class="col-xl-6 col-lg-6 col-md-8">
       <form action="" method="POST">
           @csrf <style>
                        .exchange_input.select {
                            padding: 0.625rem 0.5rem;
                            width: 100%;
                            border: none;                            
                        }
                        .txtcenter{
                            text-align: center;
                        }
                        .txtcenter input{
                            text-align: center;
                            border: none;
                        }
                        .maxtext{
                            font-size: 14px;
                            background: #f3f5f7;
                            border-radius: 30px;
                            padding: 2px 10px;
                            width: fit-content;
                            margin: auto;
                        }
                        .exchange_formbox{
                            background: #fff;
                            padding: 2rem;
                        }
                        .form_innerbox{border: 2px solid #f3f5f7;
                            border-radius: 15px;
                            padding: 2em;
                        }
                        .formbox-head{
                            font-size: 21px;
                        } 
                        </style>
            <style>
                            .list_same span{
                                width:50%;
                            }
                            .list_same span.list_value{
                                text-align:right;
                                color: #000; 
                            }
                            .list_same{
                                display: flex;
                            }
                            .list_same small{
                                display: block;
                                margin-top: -8px;
                                color: #ccc;
                            }
                            .in_icon i{
                                margin: 0;
                                line-height: 20px;
                                font-size: 2rem;
                                color: #464646;
                                display: block;
                            }
                            .exchange_dtl{
                                margin-bottom: 2em;
                            }
                            .btn_box{
                                display: flex;
                                justify-content: space-between;
                            }
                            .in_icon{
                                background: #ddd;
                                width: 50px;
                                height: 50px;
                                display: flex;
                                margin: auto;
                                border-radius: 50%;
                                align-items: center;
                                flex-direction: column;
                                justify-content: center;
                            }
                        </style>
                        <div class="d-widget__content px-5 exchange_formbox" id="confirm_exchange_formbox"> 
                            <div class="text-center formbox-head">
                                   {{__($withdraw->method->name)}}
                                </div>
                            <div class="form_innerbox">
                                <div class="Confirm_box">
                                    <div class="exchange_dtl text-center">
                                    <img src="{{getImage(imagePath()['withdraw']['method']['path'].'/'.$withdraw->method->image,'800x800')}}" alt="image" class="rounded-2" width="150"><p id="Exchange_from_To_title"></p>
                                        <h3 id="from_value_show_confirm"></h3>
                                    </div> 
                                    <div class="list_tab">
                                        <div class="list_same">
                                            <span class="list_lable">Money arrives</span>
                                            <span class="list_value">Instantly</span>
                                        </div>
                                        <hr>
                                        <div class="list_same">
                                            <span class="list_lable"> @lang('Requested Amount '):</span>
                                            <span class="list_value amount_to_exchange">{{showAmount($withdraw->amount,$withdraw->curr)}}  {{$withdraw->curr->currency_code}}</span>
                                        </div> 
                                        <div class="list_same">
                                            <span class="list_lable"> @lang('Withdraw Charge '):</span>
                                            <span class="list_value amount_to_exchange">{{showAmount($withdraw->charge,$withdraw->curr)}} {{$withdraw->curr->currency_code}}</span>
                                        </div> 
                                        <div class="list_same">
                                            <span class="list_lable"> @lang('You will get '):</span>
                                            <span class="list_value amount_to_exchange">{{showAmount($withdraw->final_amount,$withdraw->curr)}} {{$withdraw->curr->currency_code}}</span>
                                        </div> 
                                        <div class="list_same">
                                            <span class="list_lable"> @lang('Your balance will be '):</span>
                                            <span class="list_value amount_to_exchange">{{showAmount($withdraw->wallet->balance-$withdraw->final_amount,$withdraw->curr)}} {{$withdraw->curr->currency_code}}</span>
                                        </div> 
                                    </div> 
                                </div>
                            </div> 
                            <div class="btn_box">
                                <a href="{{ url()->previous() }}" class="btn btn-md btn--base mt-4 exchange_back"  >@lang('Back')</a>
                                <button type="submit" class="btn btn-md btn--base mt-4 exchange req_confirm"  >@lang('Confirm')</button>
                            </div>
                        </div>  
       </form>
    </div>
@endsection

