
@extends('layouts.header')

@section('main')
@push('title')
<title> {{ trans('messages.menu_account_lang',[],session('locale')) }}</title>
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
                        <h4 class="mb-sm-0 font-size-18">{{ trans('messages.menu_account_lang',[],session('locale')) }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('messages.pages_lang',[],session('locale')) }}</a></li>
                                <li class="breadcrumb-item active">{{ trans('messages.menu_account_lang',[],session('locale')) }}</li>
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
                            <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#add_account_modal">
                                {{ trans('messages.add_data_lang',[],session('locale')) }}
                            </button>
                        </div>
                        <div class="card-body">

                            <table id="accounts" class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>{{ trans('messages.account_detail_lang',[],session('locale')) }}</th>
                                        {{-- <th>{{ trans('messages.account_branch_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.account_no_lang',[],session('locale')) }}</th> --}}
                                        <th>{{ trans('messages.opening_balance_lang',[],session('locale')) }}</th>
                                        {{-- <th>{{ trans('messages.commission_lang',[],session('locale')) }}</th> --}}
                                        {{-- <th>{{ trans('messages.account_type_lang',[],session('locale')) }}</th> --}}
                                        <th>{{ trans('messages.notes_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.created_by_lang',[],session('locale')) }}</th>
                                        {{-- <th>{{ trans('messages.created_at_lang',[],session('locale')) }}</th> --}}
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
    <div class="modal fade" id="add_account_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">{{ trans('messages.add_data_lang',[],session('locale')) }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" class="add_account" method="POST" >
                        @csrf
                        <input type="hidden" class="account_id" name="account_id">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="example-text-input" class="form-label">{{ trans('messages.account_name_lang',[],session('locale')) }}</label>
                                    <input class="form-control account_name" name="account_name" type="text" id="example-text-input">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="example-text-input" class="form-label">{{ trans('messages.account_branch_lang',[],session('locale')) }}</label>
                                    <input class="form-control account_branch" name="account_branch" type="text" id="example-text-input">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="example-text-input" class="form-label">{{ trans('messages.account_no_lang',[],session('locale')) }}</label>
                                    <input class="form-control account_no" name="account_no" type="text" id="example-text-input">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="example-text-input" class="form-label">{{ trans('messages.opening_balance_lang',[],session('locale')) }}</label>
                                    <input class="form-control opening_balance" name="opening_balance" type="text" id="example-text-input">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="example-text-input" class="form-label">{{ trans('messages.commission_lang',[],session('locale')) }}</label>
                                    <input class="form-control commission isnumber" name="commission" type="text" id="example-text-input">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3 form-group">
                                    <label>{{ trans('messages.account_type_lang', [], session('locale')) }}</label>
                                    <select class="form-control account_type" name="account_type">
                                        <option value="1">{{ trans('messages.normal_account_lang', [], session('locale')) }}</option>
                                        <option value="2">{{ trans('messages.saving_account_lang', [], session('locale')) }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3 form ">
                                    <label class="checkboxs">{{ trans('messages.cash_lang', [], session('locale')) }}
                                        <input type="checkbox"   name="account_status" value="1" id="account_status" class="account_status">
                                        <span class="checkmarks" for="account_status"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3 form-group">
                                    <label>{{ trans('messages.notes_lang', [], session('locale')) }}</label>
                                    <textarea  class="form-control notes" rows="3" name="notes"></textarea>
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


    @include('layouts.footer_content')
</div>
<!-- end main content-->

</div>
<!-- END layout-wrapper -->
@include('layouts.footer')
@endsection

