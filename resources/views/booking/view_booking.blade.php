@extends('layouts.header')

@section('main')
@push('title')
<title> {{ trans('messages.menu_booking_lang',[],session('locale')) }}</title>
@endpush
<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">{{ trans('messages.menu_booking_lang',[],session('locale')) }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('messages.pages_lang',[],session('locale')) }}</a></li>
                                <li class="breadcrumb-item active">{{ trans('messages.menu_booking_lang',[],session('locale')) }}</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>

            <!-- end page title -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ url('booking') }}" class="btn btn-primary waves-effect waves-light">
                                {{ trans('messages.add_data_lang',[],session('locale')) }}
                            </a>
                        </div>
                        <div class="card-body">

                            <table id="all_booking" class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ trans('messages.booking_no_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.status_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.customer_name_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.dress_name_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.booking_date_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.rent_date_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.return_date_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.total_price_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.add_date_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.added_by_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.actions_lang',[],session('locale')) }}</th>
                                    </tr>
                                </thead>


                                <tbody>
                                    
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div> <!-- container-fluid -->
    </div>
    <!-- End Page-content -->

    <!-- Static Backdrop Modal -->
    <div class="modal fade" id="add_booking_info_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">{{ trans('messages.booking_detail_lang',[],session('locale')) }} : <span id="booking_no_id"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row" id="get_booking_detail">
                         
                    </div> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('messages.close_lang',[],session('locale')) }}</button>
                    <button type="submit" class="btn btn-primary submit_form">{{ trans('messages.submit_lang',[],session('locale')) }}</button>
                </div> 
            </div>
        </div>
    </div>
    
    

    @include('layouts.footer_content')
</div>
<!-- end main content-->

</div>
<!-- END layout-wrapper -->
@include('layouts.footer')
@endsection
        
         