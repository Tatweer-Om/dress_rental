
    @extends('layouts.header')

    @section('main')
    @push('title')
    <title> {{ trans('messages.menu_expense_lang',[],session('locale')) }}</title>
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
                            <h4 class="mb-sm-0 font-size-18">{{ trans('messages.menu_expense_lang',[],session('locale')) }}</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('messages.pages_lang',[],session('locale')) }}</a></li>
                                    <li class="breadcrumb-item active">{{ trans('messages.menu_expense_lang',[],session('locale')) }}</li>
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
                                <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#add_expense_modal">
                                    {{ trans('messages.add_data_lang',[],session('locale')) }}
                                </button>
                            </div>
                            <div class="card-body">

                                <table id="all_expense" class="table table-bordered dt-responsive  nowrap w-100">
                                    <thead>
                                        <tr>
                                        <th>#</th>
                                        <th>{{ trans('messages.expense_category_name_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.expense_name_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.amount_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.payment_method_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.expense_date_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.created_by_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.created_at_lang',[],session('locale')) }}</th>
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
        <div class="modal fade" id="add_expense_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">{{ trans('messages.add_data_lang',[],session('locale')) }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="#" class="add_expense" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" class="expense_id" name="expense_id">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="category_name" class="form-label">{{ trans('messages.category_name_lang',[],session('locale')) }}</label>
                                                <select class="form-control category_name" name="category_name">
                                                    <option value="">{{ trans('messages.choose_lang',[],session('locale')) }}</option>
                                                    @foreach ($view_expense as $cat) {
                                                        <option value="{{$cat->id}}">{{$cat->expense_category_name}}</option>';
                                                    }
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="payment_method" class="form-label">{{ trans('messages.payment_method_lang',[],session('locale')) }}</label>
                                                <select class="form-control payment_method" name="payment_method">
                                                    <option value="">{{ trans('messages.choose_lang',[],session('locale')) }}</option>
                                                    @foreach ($view_account as $acc) {
                                                        <option value="{{$acc->id}}">{{$acc->account_name}}</option>';
                                                    }
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="expense_name" class="form-label">{{ trans('messages.expense_name_lang',[],session('locale')) }}</label>
                                                <input class="form-control expense_name" name="expense_name" type="text" id="expense_name">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="amount" class="form-label">{{ trans('messages.amount_lang',[],session('locale')) }}</label>
                                                <input class="form-control amount isnumber" name="amount" type="text" id="amount">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="expense_date" class="form-label">{{ trans('messages.expense_date_lang',[],session('locale')) }}</label>
                                                <input class="form-control expense_date datepick" name="expense_date" type="text" id="expense_date">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="notes" class="form-label">{{ trans('messages.notes_lang',[],session('locale')) }}</label>
                                                <textarea class="form-control notes" name="notes" rows="5"></textarea>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <div class="col-md-4">
                                    <div class="fallback">
                                        <input name="expense_image" type="file" multiple="multiple">
                                    </div>
                                    <div class="dz-message needsclick">
                                        <div class="mb-3">
                                            <i class="display-4 text-muted bx bx-cloud-upload"></i>
                                        </div> 
                                        <div id="formErrors"></div>
                                    </div>


                                </div>

                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('messages.close_lang',[],session('locale')) }}</button>
                        <button type="submit" class="btn btn-primary ">{{ trans('messages.submit_lang',[],session('locale')) }}</button>
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

