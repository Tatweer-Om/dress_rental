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
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
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
                 </div> 
            </div>
        </div>
    </div>


    {{-- payment modal --}} 
    <div class="modal fade" id="add_payment_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">{{ trans('messages.add_data_lang',[],session('locale')) }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('add_payment') }}" class="add_payment" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" class="bill_id" name="bill_id">
                        <input type="hidden" class="bill_booking_id" name="booking_id">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="bill_total_amount" class="form-label">{{ trans('messages.total_price_lang',[],session('locale')) }}</label>
                                    <input class="form-control bill_total_amount" name="bill_total_amount" type="text" id="bill_total_amount">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="bill_remaining_amount" class="form-label">{{ trans('messages.remaining_lang',[],session('locale')) }}</label>
                                    <input class="form-control bill_remaining_amount" name="bill_remaining_amount" type="text" id="bill_remaining_amount">
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="bill_paid_amount" class="form-label">{{ trans('messages.paid_amount_lang',[],session('locale')) }}</label>
                                    <input class="form-control bill_paid_amount" name="bill_paid_amount" type="text" id="bill_paid_amount">
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="bill_payment_date" class="form-label">{{ trans('messages.payment_date_lang',[],session('locale')) }}</label>
                                    <input class="form-control bill_payment_date datepick" readonly value="<?php echo date('Y-m-d'); ?>" name="bill_payment_date" type="text" id="bill_payment_date">
                                </div>
                            </div> 
                        </div> 
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="bill_payment_method" class="form-label">{{ trans('messages.total_price_lang',[],session('locale')) }}</label>
                                    <select class="form-control bill_payment_method" name="bill_payment_method">
                                        @foreach($view_account as $account)
                                            <option value="{{ $account->id }}">{{ $account->account_name}}</option>
                                        @endforeach
                                    </select>
                                 </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bill_notes" class="form-label">{{ trans('messages.notes_lang',[],session('locale')) }}</label>
                                    <textarea class="form-control bill_notes" name="bill_notes" id="bill_notes" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('messages.close_lang',[],session('locale')) }}</button>
                    <button type="submit" class="btn btn-primary submit_form">{{ trans('messages.submit_lang',[],session('locale')) }}</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    {{-- payment modal --}} 
    <div class="modal fade" id="extend_booking_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">{{ trans('messages.add_data_lang',[],session('locale')) }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('add_extend_booking') }}" class="add_extend_booking" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" class="extend_booking_id" name="booking_id">
                        <input type="hidden" class="extend_dress_id" name="dress_id">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="old_rent_date" class="form-label">{{ trans('messages.old_rent_date_lang',[],session('locale')) }}</label>
                                    <input class="form-control old_rent_date" readonly name="old_rent_date" type="text" id="old_rent_date">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="return_date" class="form-label">{{ trans('messages.return_date_lang',[],session('locale')) }} -> {{ trans('messages.new_rent_date_lang',[],session('locale')) }}</label>
                                    <input class="form-control return_date" readonly name="return_date" type="text" id="return_date">
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="new_return_date" class="form-label">{{ trans('messages.new_return_date_lang',[],session('locale')) }}</label>
                                    <input class="form-control new_return_date" readonly name="new_return_date" type="text" id="new_return_date">
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="duration" class="form-label">{{ trans('messages.duration_lang',[],session('locale')) }} </label>
                                    <input class="form-control duration" readonly  name="duration" type="text" id="duration">
                                </div>
                            </div> 
                        </div>  
                        <div class="row">
                           
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="price" class="form-label">{{ trans('messages.price_lang',[],session('locale')) }}</label>
                                    <input class="form-control price isnumber" readonly  name="price" type="text" id="price">
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="discount" class="form-label">{{ trans('messages.discount_lang',[],session('locale')) }}</label>
                                    <input class="form-control discount isnumber" readonly  name="discount" type="text" id="discount">
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="total_price" class="form-label">{{ trans('messages.total_price_lang',[],session('locale')) }}</label>
                                    <input class="form-control total_price isnumber" readonly  name="total_price" type="text" id="total_price">
                                </div>
                            </div> 
                        </div>  
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="extend_notes" class="form-label">{{ trans('messages.notes_lang',[],session('locale')) }}</label>
                                    <textarea class="form-control extend_notes" name="extend_notes" id="extend_notes" rows="3"></textarea>
                                </div>
                            </div>
                            
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('messages.close_lang',[],session('locale')) }}</button>
                    <button type="submit" class="btn btn-primary submit_form">{{ trans('messages.submit_lang',[],session('locale')) }}</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- finish Modal -->
    <div class="modal fade" id="finish_booking_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">{{ trans('messages.booking_detail_lang',[],session('locale')) }} : <span id="finish_booking_no"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('add_finish_booking') }}" class="add_finish_booking" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" class="finish_booking_id" name="booking_id">
                        <div class="row" id="get_finish_detail_detail" style="padding:30px">
                            
                        </div> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('messages.close_lang',[],session('locale')) }}</button>
                    <button type="submit" class="btn btn-primary submit_form">{{ trans('messages.submit_lang',[],session('locale')) }}</button>
                </div> 
                </form>
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
        
         