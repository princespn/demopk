@extends($activeTemplate . 'layouts.user_master')
@section('content')
    <div class="col-xl-10">
        <div class="card style--two">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-center">
            <div class="bank-icon  me-2">
                <i class="las la-shopping-bag"></i>
                </div>
            <h4 class="fw-normal">@lang('Deposit')</h4>
            </div>
            <div class="card-body p-4">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <form action="{{ route('user.diposite.method.add.info') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="d-widget mb-4">
                                <div class="d-widget__header">
                                    <h6 class="">@lang('Enter Details')</h4>
                                </div>
                                <div class="d-widget__content">
                                    <div class="row">
                                    <div class="form-group">

        <input type="hidden" name="getway_id" id="getway_id" value="{{ $gateways->getway_id}}">

        <input class="form--control" type="text" name="getway_name" id="getway_name" value="{{ $gateways->getway_name}}">


                                         
                                          </div> 
                                                                           
                                        </div>
                                </div>
                            </div>

                            <div class="d-widget">
                                <div class="d-widget__header">
                                    <h6 class="">@lang('Enter Details')</h4>
                                </div>
                                <div class="d-widget__content">
                                    <!--<div class="description mb-2"></div>-->
                                    <div class="form-group">
                                        <label>@lang('Provide a nick name')<span class="text-danger">*</span> </label>
                                        <input 
                                            class="form--control" 
                                            type="text" name="nick_name" id="nick_name"
                                        >
                                    </div>
                                    <!-- <div class="fields"></div>-->

                                    @php
                                    if($gateways->getway_name =='Bank'){
                                    @endphp
                                    <div class="form-group">
                                        <label>@lang(' Bank Name')<span class="text-danger">*</span> </label>
                                        <input 
                                            class="form--control" 
                                            type="text" name="bank_name" id="bank_name"
                                        >
                                    </div>

                                     @php
                                     }
                                    @endphp
                                   

                                    <div class="form-group">
                                        <label>{{ $gateways->getway_name}} @lang ('Account Holder Name')<span class="text-danger">*</span> </label>
                                        <input 
                                            class="form--control" 
                                            type="text" name="account_holder" id="account_holder"
                                        >
                                    </div>

                                     @php
                                    if($gateways->getway_name =='Bank'){
                                    @endphp
                                   
                                    <div class="form-group">
                                        <label>@lang(' Account number with branch code ')<span class="text-danger">*</span> </label>
                                        <input 
                                            class="form--control" 
                                            type="text" name="branch_code" name="branch_code"
                                        >
                                    </div>
                                     @php
                                     }
                                    @endphp

                                    <div class="form-group">
                                        <label> {{ $gateways->getway_name}} @lang ('Account Number')<span class="text-danger">*</span> </label>
                                        <input 
                                            class="form--control" 
                                            type="text" name="account_number" id="account_number"
                                        >
                                    </div>

                                   

                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-md btn--base mt-4 w-100">@lang('Add diposit method')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        'use strict';
        (function($) {
            $('.select_method').on('change', function() {
                var userData = $('.select_method option:selected').data('userdata')
                var description = $('.select_method option:selected').data('description')
                var currencies = $('.select_method option:selected').data('currencies')
                var options = `<option value="">@lang('Select Currency')</option>`
                $('.currency').children().remove();

                $('.fields').html(userData ?? null);
                $('.description').html(description ?? null);

                $.each(currencies, function(i, val) {
                    options += `<option value="${i}">${val}</option>`
                });
                $('.currency').append(options);
            })
        })(jQuery);
    </script>
@endpush
