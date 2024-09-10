@extends('layouts.header')

@section('main')
@push('title')
<title> {{ trans('messages.menu_dress_lang',[],session('locale')) }}</title>
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
                        <h4 class="mb-sm-0 font-size-18">{{ trans('messages.menu_dress_lang',[],session('locale')) }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('messages.pages_lang',[],session('locale')) }}</a></li>
                                <li class="breadcrumb-item active">{{ trans('messages.menu_dress_lang',[],session('locale')) }}</li>
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
                            <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#add_dress_modal">
                                {{ trans('messages.add_data_lang',[],session('locale')) }}
                            </button>
                        </div>
                        <div class="card-body">

                            <table id="all_dress" class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ trans('messages.sku_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.dress_name_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.category_name_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.price_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.condition_lang',[],session('locale')) }}</th>
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
    <div class="modal fade" id="add_dress_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">{{ trans('messages.add_data_lang',[],session('locale')) }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('add_dress') }}" class="add_dress" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" class="dress_id" name="dress_id">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="category_name" class="form-label">{{ trans('messages.category_name_lang',[],session('locale')) }}</label>
                                            <select class="form-control category_name" name="category_name">
                                                <option value="">{{ trans('messages.choose_lang',[],session('locale')) }}</option>
                                                @foreach ($view_category as $cat) {
                                                    <option value="{{$cat->id}}">{{$cat->category_name}}</option>';
                                                }
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> 
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="brand_name" class="form-label">{{ trans('messages.brand_name_lang',[],session('locale')) }}</label>
                                            <select class="form-control brand_name" name="brand_name">
                                                <option value="">{{ trans('messages.choose_lang',[],session('locale')) }}</option>
                                                @foreach ($view_brand as $brand) {
                                                    <option value="{{$brand->id}}">{{$brand->brand_name}}</option>';
                                                }
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="color_name" class="form-label">{{ trans('messages.color_name_lang',[],session('locale')) }}</label>
                                            <select class="form-control color_name" name="color_name">
                                                <option value="">{{ trans('messages.choose_lang',[],session('locale')) }}</option>
                                                @foreach ($view_color as $color) {
                                                    <option value="{{$color->id}}">{{$color->color_name}}</option>';
                                                }
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="size_name" class="form-label">{{ trans('messages.size_name_lang',[],session('locale')) }}</label>
                                            <select class="form-control size_name" name="size_name">
                                                <option value="">{{ trans('messages.choose_lang',[],session('locale')) }}</option>
                                                @foreach ($view_size as $size) {
                                                    <option value="{{$size->id}}">{{$size->size_name}}</option>';
                                                }
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="dress_name" class="form-label">{{ trans('messages.dress_name_lang',[],session('locale')) }}</label>
                                            <input class="form-control dress_name" name="dress_name" type="text" id="dress_name">
                                        </div>
                                    </div> 
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="sku" class="form-label">{{ trans('messages.sku_lang',[],session('locale')) }}</label>
                                            <input class="form-control sku" name="sku" type="text" id="sku">
                                        </div>
                                    </div> 
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">{{ trans('messages.price_lang',[],session('locale')) }}</label>
                                            <input class="form-control price isnumber" name="price" type="text" id="price">
                                        </div>
                                    </div> 
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="condition" class="form-label">{{ trans('messages.condition_lang',[],session('locale')) }}</label>
                                            <select class="form-control condition" name="condition">
                                                <option value="1">{{ trans('messages.new_lang',[],session('locale')) }}</option>
                                                <option value="2">{{ trans('messages.used_lang',[],session('locale')) }}</option>
                                            </select>
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
                                <hr>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <h2>{{ trans('messages.atrributes_lang',[],session('locale')) }}</h2>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <button type="button" class="btn btn-primary" id="add_attribute">{{ trans('messages.add_attribute_lang',[],session('locale')) }}</button>
                                    </div>
                                </div>
                                <div  id="all_attribute">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-12 text-center cursor-pointer" id="ad_cover_container">
                                        <img src="{{asset('custom_images/dummy/cover-image-icon.png') }}" id="ad_cover_preview" class="img-fluid">
                                    </div>
                                    <input type="file" name="dress_image" id="ad_cover" class="d-none">
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <button type="button" class="btn btn-danger btn-block" id="btn-ad-images"><i class="fas fa-folder-open"></i> {{ trans('messages.more_images_lang',[],session('locale')) }}</button>    
                                        <input type="file" multiple name="ad_images" id="ad_images" class="d-none">
                                    </div>
                                    <div class="col-md-12">
                                        <div  class="row" id="attachment-holder"></div>
                                    </div>
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
        
         