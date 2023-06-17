@extends($activeTemplate.'layouts.user_master')
@section('content')
<div class="col-xl-10">
    <div class="card style--two">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-center">
            <div class="bank-icon has--plus me-2">
                <i class="las la-university"></i>
            </div>
            <h4 class="fw-normal">@lang($pageTitle)</h4>
        </div>
        <div class="card-body p-4">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <form action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="d-widget mb-4">
                            <div class="d-widget__header">
                                <h6 class="">@lang('Enter Details')</h4>
                            </div>
                            <div class="d-widget__content">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label>@lang('Select Method')</label>
                                        <select class="select_method select" name="method_id" required>
                                            <option value="">@lang('Select')</option>
                                            @foreach ($withdrawMethod as $method)
                                               <option value="{{$method->id}}" data-userdata="{{json_encode($method->user_data)}}" data-currencies={{$method->curr()}}>@lang($method->name)</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <label>@lang('Select Currency')</label>
                                        <select class="select currency" name="currency_id" required>
                                            <option value="">@lang('Select Currency')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-widget">
                            <div class="d-widget__header">
                                <h6 class="">@lang('Enter Details')</h4>
                            </div>
                            <div class="d-widget__content">
                                <div class="form-group">
                                    <label >@lang('Provide a nick name')<span class="text-danger">*</span> </label>
                                    <input class="form--control" type="text" name="name"  placeholder="@lang('e.g. Paypal-USD')">
                                </div>
                               <div class="fields"></div>
                            </div>
                        </div>  
                        <div class="text-center">
                            <button type="submit" class="btn btn-md btn--base mt-4">@lang('Add withdraw method')</button>
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
            (function ($) {
              $('.select_method').on('change',function () { 
                  var userData =  $('.select_method option:selected').data('userdata')
                  var currencies =  $('.select_method option:selected').data('currencies')
                  var options =  `<option value="">@lang('Select Currency')</option>`
                  $('.currency').children().remove();
                  var fields = '';
                  $.each(userData, function (i, val) { 
                    var span='';
                    var required='';
                    if(val.validation == 'required'){
                        span = `<span class="text-danger">*</span>`
                        required = `required`
                    }
                    if(val.type == 'text') {
                        fields += `<div class="form-group">
                                        <label><strong>${val.field_level} ${span}</strong></label>
                                        <input type="text" name="${i}" class="form--control"  placeholder="${val.field_level}" ${required}>
                                    </div>`
                    }
                    if(val.type == 'textarea') {
                        fields += `<div class="form-group">
                                        <label><strong>${val.field_level} ${span}</strong></label>
                                        <input type="text" name="${i}" class="form--control"  placeholder="${val.field_level}" ${required}>
                                    </div>`
                    }
                    if(val.type == 'file') {
                        fields += `<div class="form-group">
                                        <label><strong>${val.field_level} ${span}</strong></label>
                                        <input type="file" name="${i}" class="form--control" ${required}>
                                    </div>`
                    }


                  });
                 $('.fields').html(fields);

                  $.each(currencies, function (i, val) { 
                       options += `<option value="${i}">${val}</option>`
                   });
                    $('.currency').append(options);
               })
            })(jQuery);
     </script>
@endpush