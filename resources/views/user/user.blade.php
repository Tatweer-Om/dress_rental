@extends('layouts.header')

@section('main')
    @push('title')
        <title> {{ trans('messages.menu_user_lang', [], session('locale')) }}</title>
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
                            <h4 class="mb-sm-0 font-size-18">{{ trans('messages.menu_user_lang', [], session('locale')) }}</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a
                                            href="javascript: void(0);">{{ trans('messages.pages_lang', [], session('locale')) }}</a>
                                    </li>
                                    <li class="breadcrumb-item active">
                                        {{ trans('messages.menu_user_lang', [], session('locale')) }}</li>
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
                                <button type="button" class="btn btn-primary waves-effect waves-light"
                                    data-bs-toggle="modal" data-bs-target="#add_user_modal">
                                    {{ trans('messages.add_data_lang', [], session('locale')) }}
                                </button>
                            </div>
                            <div class="card-body">

                                <table id="all_user" class="table table-bordered dt-responsive  nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ trans('messages.user_name_lang', [], session('locale')) }}</th>
                                            <th>{{ trans('messages.password_lang', [], session('locale')) }}</th>
                                            <th>{{ trans('messages.user_contact_lang', [], session('locale')) }}</th>
                                            <th>{{ trans('messages.notes_lang', [], session('locale')) }}</th>
                                            <th>{{ trans('messages.created_by_lang', [], session('locale')) }}</th>
                                            <th>{{ trans('messages.action_lang', [], session('locale')) }}</th>
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
        <div class="modal fade" id="add_user_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">
                            {{ trans('messages.add_data_lang', [], session('locale')) }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="#" class="add_user" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" class="user_id" name="user_id">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="row">
                                        <!-- User Name Field -->
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="user_name" class="form-label">{{ trans('messages.user_name_lang', [], session('locale')) }}</label>
                                                <input class="form-control user_name" name="user_name" type="text" id="user_name">
                                            </div>
                                        </div>

                                        <!-- Password Field -->
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="password" class="form-label">{{ trans('messages.password_lang', [], session('locale')) }}</label>
                                                <input class="form-control password" name="password" type="text" id="password">
                                            </div>
                                        </div>

                                        <!-- User Phone Field -->
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="user_phone" class="form-label">{{ trans('messages.user_phone_lang', [], session('locale')) }}</label>
                                                <input class="form-control user_phone" name="user_phone" type="text" id="user_phone">
                                            </div>
                                        </div>

                                        <!-- User Email Field -->
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="user_email" class="form-label">{{ trans('messages.user_email_lang', [], session('locale')) }}</label>
                                                <input class="form-control user_email" name="user_email" type="text" id="user_email">
                                            </div>
                                        </div>

                                        <!-- Checkboxes Section -->
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">{{ trans('messages.permissions_lang', [], session('locale')) }}</label>
                                                <div class="row" id="checked_html">
                                                    <div class="col-md-1 checkbox-container me-4">
                                                        <div class="form-check">
                                                            <label class="form-check-label" for="checkbox6">{{ trans('messages.all') }}</label>
                                                            <input class="form-check-input permit_array" type="checkbox" value="1" id="checkboxAll">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 checkbox-container me-4">
                                                        <div class="form-check">
                                                            <label class="form-check-label" for="checkbox1">{{ trans('messages.checkbox_dress') }}</label>
                                                            <input class="form-check-input permit_array" type="checkbox" value="2" id="checkbox_dress" name="permit_array[]">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 checkbox-container me-4">
                                                        <div class="form-check">
                                                            <label class="form-check-label" for="checkbox2">{{ trans('messages.checkbox_booking') }}</label>
                                                            <input class="form-check-input permit_array" type="checkbox" value="3" id="checkbox_booking" name="permit_array[]">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 checkbox-container me-4 ">
                                                        <div class="form-check">
                                                            <label class="form-check-label" for="checkbox3">{{ trans('messages.checkbox_reports') }}</label>
                                                            <input class="form-check-input permit_array" type="checkbox" value="4" id="checkbox_reports" name="permit_array[]">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 checkbox-container me-4">
                                                        <div class="form-check">
                                                            <label class="form-check-label" for="checkbox4">{{ trans('messages.checkbox_expense') }}</label>
                                                            <input class="form-check-input permit_array" type="checkbox" value="5" id="checkbox_expense" name="permit_array[]">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 checkbox-container me-4">
                                                        <div class="form-check">
                                                            <label class="form-check-label" for="checkbox5">{{ trans('messages.checkbox_user') }}</label>
                                                            <input class="form-check-input permit_array" type="checkbox" value="6" id="checkbox_user" name="permit_array[]">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 checkbox-container me-4">
                                                        <div class="form-check">
                                                            <label class="form-check-label" for="checkbox5">{{ trans('messages.maint_lang') }}</label>
                                                            <input class="form-check-input permit_array" type="checkbox" value="7" id="checkbox_maint" name="permit_array[]">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 checkbox-container me-4">
                                                        <div class="form-check">
                                                            <label class="form-check-label" for="checkbox5">{{ trans('messages.setting_lang') }}</label>
                                                            <input class="form-check-input permit_array" type="checkbox" value="8" id="checkbox_setting" name="permit_array[]">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 checkbox-container me-4">
                                                        <div class="form-check">
                                                            <label class="form-check-label" for="checkbox5">{{ trans('messages.sms_lang') }}</label>
                                                            <input class="form-check-input permit_array" type="checkbox" value="9" id="checkbox_sms" name="permit_array[]">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 checkbox-container me-4">
                                                        <div class="form-check">
                                                            <label class="form-check-label" for="checkbox5">{{ trans('messages.customer_lang') }}</label>
                                                            <input class="form-check-input permit_array" type="checkbox" value="10" id="checkbox_customer" name="permit_array[]">
                                                        </div>
                                                    </div>



                                                </div>
                                            </div>
                                        </div>

                                        <!-- Notes Field -->
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="notes" class="form-label">{{ trans('messages.notes_lang', [], session('locale')) }}</label>
                                                <textarea class="form-control notes" name="notes" rows="5"></textarea>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('messages.close_lang', [], session('locale')) }}</button>
                                <button type="submit" class="btn btn-primary">{{ trans('messages.submit_lang', [], session('locale')) }}</button>
                            </div>
                        </form>


                </div>
            </div>
        </div>


    </div>
    <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->
    @include('layouts.footer')
@endsection
