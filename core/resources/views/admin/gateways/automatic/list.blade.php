@extends('admin.layouts.app')

@section('panel')
    <div class="row">

        <div class="col-lg-12">
            <div class="card b-radius--10 ">

                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                            <tr>
                                <th>@lang('Gateway')</th>
                                <th>@lang('Supported Currency')</th>
                                <th>@lang('Enabled Currency')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                          
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
            </div><!-- card end -->
        </div>
    </div>

    <x-confirmation-modal />
@endsection
@push('breadcrumb-plugins')
<div class="d-inline">
    <div class="input-group justify-content-end">
        <input type="text" name="search_table" class="form-control bg--white" placeholder="@lang('Search')...">
        <button class="btn btn--primary input-group-text"><i class="fa fa-search"></i></button>
    </div>
</div>
@endpush
