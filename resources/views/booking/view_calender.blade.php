@extends('layouts.header')

@section('main')
@push('title')
<title> {{ trans('messages.menu_calender_lang',[],session('locale')) }}</title>
@endpush
<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<style>
    .disabled-date {
        background-color: #f0f0f0 !important; /* Gray out disabled dates */
        pointer-events: none; /* Disable click events */
        color: #999999; /* Change text color */
        opacity: 0.5; /* Make the text look lighter */
    }
</style>
<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">{{ trans('messages.menu_calender_lang',[],session('locale')) }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('messages.pages_lang',[],session('locale')) }}</a></li>
                                <li class="breadcrumb-item active">{{ trans('messages.menu_calender_lang',[],session('locale')) }}</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="hidden" value="{{ $day_before }}" id="day_before">
                            <label>{{ trans('messages.dress_name_lang',[],session('locale')) }}</label>
                            <select class="form-control dress_id" name="dress_id" onchange="get_calender_bookings()" id="dress_id">
                                <option value="">{{ trans('messages.choose_lang',[],session('locale')) }}</option>
                                @foreach ($view_dress as $dress)
                                    <option value="{{ $dress->id }}">{{ $dress->dress_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xl-3 col-lg-4" style="display:none">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-grid">
                                        <button class="btn font-16 btn-primary" id="btn-new-event"><i class="mdi mdi-plus-circle-outline"></i> Create
                                            New Event
                                        </button>
                                    </div>
                                    
                                    <div id="external-events" class="mt-2">
                                        <br>
                                        <p class="text-muted">Drag and drop your event or click in the calendar</p>
                                        <div class="external-event fc-event text-success bg-success-subtle" data-class="bg-success">
                                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>New Event Planning
                                        </div>
                                        <div class="external-event fc-event text-info bg-info-subtle" data-class="bg-info">
                                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>Meeting
                                        </div>
                                        <div class="external-event fc-event text-warning bg-warning-subtle" data-class="bg-warning">
                                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>Generating Reports
                                        </div>
                                        <div class="external-event fc-event text-danger bg-danger-subtle" data-class="bg-danger">
                                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>Create New theme
                                        </div>
                                        <div class="external-event fc-event text-dark bg-dark-subtle" data-class="bg-dark">
                                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>Team Meeting
                                        </div>
                                    </div>
            
                                    <div class="row justify-content-center mt-5">
                                        <div class="col-lg-12 col-sm-6">
                                            <img src="{{ asset('images/undraw-calendar.svg') }}" alt="" class="img-fluid d-block">
                                        </div>
                                    </div>
                                
                                </div>
                            </div>
                        </div> <!-- end col-->
            
                        <div class="col-xl-12 col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div id="dress_calendar"></div>
                                </div>
                            </div>
                        </div> <!-- end col -->
            
                    </div>
                    <!-- end row -->
            
                    <div style='clear:both'></div>
            
                    <!-- Add New Event MODAL -->
                    <div class="modal fade" id="event-modal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header py-3 px-4 border-bottom-0">
                                    <h5 class="modal-title" id="modal-title">Event</h5>
            
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            
                                </div>
                                <div class="modal-body p-4">
                                    <form class="needs-validation" name="event-form" id="form-event" novalidate>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Event Name</label>
                                                    <input class="form-control" placeholder="Insert Event Name"
                                                        type="text" name="title" id="event-title" required value="" />
                                                    <div class="invalid-feedback">Please provide a valid event name</div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Category</label>
                                                    <select class="form-control form-select" name="category" id="event-category">
                                                        <option  selected> --Select-- </option>
                                                        <option value="bg-danger">Danger</option>
                                                        <option value="bg-success">Success</option>
                                                        <option value="bg-primary">Primary</option>
                                                        <option value="bg-info">Info</option>
                                                        <option value="bg-dark">Dark</option>
                                                        <option value="bg-warning">Warning</option>
                                                    </select>
                                                    <div class="invalid-feedback">Please select a valid event category</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-6">
                                                <button type="button" class="btn btn-danger" id="btn-delete-event">Delete</button>
                                            </div>
                                            <div class="col-6 text-end">
                                                <button type="button" class="btn btn-light me-1" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-success" id="btn-save-event">Save</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div> <!-- end modal-content-->
                        </div> <!-- end modal dialog-->
                    </div>
                    <!-- end modal-->
            
                </div>
            </div>
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
        
         
