@extends($activeTemplate.'layouts.user_master')

@section('content')
<div class="col-xl-8">
    <div class="card style--two">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-center">
            <div class="bank-icon  me-2">
                <i class="las la-shopping-bag"></i>
            </div>
            <h4 class="fw-normal">@lang('Deposit')</h4>

        </div>
        <div class="row justify-content-center">
        <center><h5>Select a {{$gateways->getway_name}}</h5></center>
        <div>&nbsp;&nbsp;</div>

            <form action="{{ route('user.diposite.method.add.page') }}" method="POST" enctype="multipart/form-data">

                            @csrf

        <div class="col-xl-10">

            <strong>&nbsp;&nbsp;&nbsp;&nbsp;{{$gateways->getway_name}}</strong>
        
        <input type="hidden" name="getway_id" id="getway_id" value="{{ $gateways->getway_id}}">
        <input type="hidden" name="name" id="name" value="{{ $gateways->name}}">

                  &nbsp;&nbsp;  {{$gateways->name}}



                
                    <div class="fw-normal float-start" style="float:right !important;">

                    <input type="radio" name="name" id="name" value="{{ $gateways->name}}">        
                    </div>

                    </div>


    



                <h3 class="fw-normal float-end">
                   <!-- <a href="{{ route('user.diposite.method.add.page') }}" class="btn btn-outline--primary btn-sm" style="margin-left: 303px;"> <i class="fas fa-plus"></i> @lang('Add New') {{$gateways->getway_name}} @lang('Account')</a>-->
                <button type="submit" name="getway" id="getway" class="btn btn-outline--primary btn-sm" style="margin-left: 303px;"><i class="fas fa-plus"></i> @lang('Add New') {{$gateways->getway_name}} @lang('Account')</button>

                </h3>

                
                                    
    <button type="submit" name="Next" id="Next" class="btn btn-outline--primary btn-sm">Next</button>
</form>

            
        </div>
    </div>
</div>

@endsection

@push('script')
<script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

@endpush
