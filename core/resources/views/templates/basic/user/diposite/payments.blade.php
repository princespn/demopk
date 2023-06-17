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
        <center><h5>Select from your popular payment options below.</h5></center>
        <div>&nbsp;&nbsp;</div>



        @forelse($gateways->sortBy('alias') as $k=>$gateway)

        @if($gateway->status == 1)
            <form action="{{ route('user.diposite.method.add') }}" method="POST" enctype="multipart/form-data">

                            @csrf

        <div class="col-xl-10">
        
        <img src="{{ getImage('assets/images/gateway/' .$gateway->logo) }}" alt="" width="100px;" height="100px;">
        <input type="hidden" name="getway_id" id="getway_id" value="{{ $gateway->id}}">
        <input type="hidden" name="getway_name" id="getway_name" value="{{ $gateway->name}}">
        <input type="hidden" name="name" id="name" value="{{ $gateway->name}}">

        
        {{ $gateway->name }}


                
                    <div class="fw-normal float-start" style="float:right !important;">
        
                    <button type="submit" class="btn btn-outline--primary btn-sm">Deposit Now</button>
                    </div>

                    </div>


    


</form>
<div>&nbsp;&nbsp;</div>
                                    
        @endif

                            
        @empty
        @endforelse

            
        </div>
    </div>
</div>

@endsection

@push('script')
<script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

@endpush
