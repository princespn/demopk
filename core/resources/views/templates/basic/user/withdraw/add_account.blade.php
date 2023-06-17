<form action="" method="POST" enctype="multipart/form-data" id="formAddMethod">
    @csrf
    <input type="hidden" name="method_id" value="{!! $method->id !!}">
    <label>@lang('Select Currency')</label>
    <select class="select currency add_select_currency" name="currency_id" required data-name="{!! $method->name !!}">
        <option value="">@lang('Select Currency')</option>
        @foreach($method->curr() as $key=> $curr)
            <option value="{!! $key !!}">{!! $curr !!}</option>
        @endforeach
    </select>
    <h6 class="mt-3">@lang('Enter Details')</h6>
    <div class="form-group">
        <label >@lang('Provide a nick name')<span class="text-danger">*</span> </label>
        <input class="form--control" type="text" name="name"  placeholder="@lang('e.g. Paypal-USD')" value="{!! $method->name !!}">
    </div>
    <div class="fields">
        @foreach($method->user_data as $k=>$v)
            @if($v->type == 'text')
                <div class="form-group">
                    <label><strong>{{$v->field_level}}{!! $v->validation  == 'required' ? ' <span class="text-danger">*</span>':'' !!}</strong></label>
                    <input type="text" name="{{$k}}" class="form--control"  placeholder="{{$v->field_level}}" {!! $v->validation !!}>
                </div>
            @elseif($v->type == 'textarea')
                <div class="form-group">
                    <label><strong>{{$v->field_level}}{!! $v->validation  == 'required' ? ' <span class="text-danger">*</span>':'' !!}</strong></label>
                    <input type="text" name="{{$k}}" class="form--control"  placeholder="{{$v->field_level}}" {!! $v->validation !!}>
                </div>
            @elseif($v->type == 'file')
                <div class="form-group">
                    <label><strong>{{$v->field_level}}{!! $v->validation  == 'required' ? ' <span class="text-danger">*</span>':'' !!}</strong></label>
                    <input type="file" name="{{$k}}" class="form--control"  placeholder="{{$v->field_level}}" {!! $v->validation !!}>
                </div>
            @endif
        @endforeach
    </div>

    <div class="text-center">
        <button type="submit" class="btn btn-md btn--base mt-4">@lang('Add withdraw method')</button>
    </div>
</form>
