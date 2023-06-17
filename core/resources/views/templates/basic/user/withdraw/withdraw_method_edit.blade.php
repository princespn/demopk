@extends($activeTemplate.'layouts.user_master')
@section('content')
<div class="col-xl-10">
    <div class="card style--two">
        <div class="card-header d-flex justify-content-between">
            <div class="d-flex flex-wrap align-items-center">
                <div class="bank-icon has--plus me-2">
                    <i class="las la-university"></i>
                </div>
                <h4 class="fw-normal">@lang($pageTitle)</h4>
            </div>
            <div class="form-group">
                <a href="{{route('user.withdraw.methods')}}" class="btn btn-outline--primary btn-sm me-2 "><i class="las la-backward"></i> @lang('Back') </a>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <form action="{{route('user.withdraw.update')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{$userMethod->id}}">
                        <input type="hidden" name="method_id" value="{{$userMethod->withdrawMethod->id}}">
                        <div class="d-widget">
                            <div class="d-widget__header">
                                <h6 class="">@lang('Edit Details -') {{$userMethod->withdrawMethod->name}}</h4>
                            </div>
                            <div class="d-widget__content">
                                <div class="form-group">
                                    <label >@lang('Provide a nick name')<span class="text-danger">*</span> </label>
                                    <input class="form--control" type="text" name="name" value="{{$userMethod->name}}"  placeholder="@lang('e.g. Paypal-USD')" required>
                                </div>
                                @if($userMethod->user_data)
                                 @foreach($userMethod->user_data as $k => $v)
                                    @if($v->type == "text")
                                        <div class="form-group">
                                            <label><strong>{{__(ucwords(str_replace('_',' ',$k)))}} @if($v->validation == 'required') <span class="text-danger">*</span>  @endif</strong></label>
                                            <input type="text" name="{{$k}}" class="form--control" value="{{$v->field_name}}" placeholder="{{__(ucwords(str_replace('_',' ',$k)))}}" @if($v->validation == "required") required @endif>
                                           
                                        </div>
                                    @elseif($v->type == "textarea")
                                        <div class="form-group">
                                            <label><strong>{{__(ucwords(str_replace('_',' ',$k)))}} @if($v->validation == 'required') <span class="text-danger">*</span>  @endif</strong></label>
                                            <textarea name="{{$k}}"  class="form--control"  placeholder="{{__(ucwords(str_replace('_',' ',$k)))}}" rows="3" @if($v->validation == "required") required @endif>{{$v->field_name}}</textarea>
                                           
                                        </div>
                                    @elseif($v->type == "file")
                                        <div class="form-group">
                                            <label><strong>{{__(ucwords(str_replace('_',' ',$k)))}} @if($v->validation == 'required') <span class="text-danger">*</span>  @endif</strong></label>
                                            <input type="file" name="{{$k}}" class="form--control" id="">
                                            
                                        </div>
                                    @endif
                                 @endforeach
                              @endif

                             <div class="form-group">
                              
                                 <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>@lang('Status')</strong>
                                    <div class="form-group mb-0">
                                        <label class="switch">
                                            <input type="checkbox" class="update" name="status"  id="checkbox" {{$userMethod->status == 1 ? 'checked':''}}>
                                            <div class="slider round"></div>
                                          </label>
                                    </div>
                                </li> 
                             </div>
                            </div>
                        </div>  
                        <div class="text-center">
                            <button type="submit" class="btn btn-md btn--base mt-4">@lang('Update withdraw method')</button>
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
                  var options =  `<option>@lang('Select Currency')</option>`
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