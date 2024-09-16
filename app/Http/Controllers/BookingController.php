<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dress;

class BookingController extends Controller
{
    public function index(){
        $view_dress= Dress::all();
        return view ('booking.add_booking', compact('view_dress'));
    }
    public function get_dress_detail(Request $request)
	{
        $dress_id = $request->input('dress_id');
		// dress attributes
        $dress_att_div='<div class="col-md-6">
                                <div class="row accordion" id="accordionExample">';
        $dress_attribute = DressAttribute::where('dress_id', $dress_id)->get();
        if(!empty($dress_attribute))
        {
            foreach ($dress_attribute as $key => $value) {
                $dress_att_div.='<div class="col-md-12">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading'.$rows->id.'">
                                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$rows->id.'" aria-expanded="false" aria-controls="collapse'.$rows->id.'">
                                                '.rows->attribute.'
                                            </button>
                                        </h2>
                                        <div id="collapse'.$rows->id.'" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample" style="">
                                            <div class="accordion-body">
                                                <div class="text-muted">
                                                    <strong class="text-dark">'.rows->notes.'
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>';
            }
        }
        else
        {
            $dress_att_div.='<div class="col-md-12">
                                <h2>'.trans('messages.no_attribute_lang',[],session('locale')).'</h2>         
                            </div>';
        }
        $dress_att_div.='</div>
                            </div>';
        $images = DressImage::where('dress_id', $dress_id)->get();
        if(!empty($images))
        {
            foreach($images as $rows)
            {
                // Generate the URL for the file
                $url = asset('custom_images/dress_image/' . basename($rows->dress_image));
                $msg .= '<div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12"> 
                            <img class="img-thumbnail mb-1" src="'.$url.'" style="max-height:60px !important;min-height:60px !important;max-width:60px;min-width:60px;"> 
                            <p class="text-center">
                                <a href="#" class="card-link e-rmv-attachment" id="'.$rows->id.'">
                                <i class="fa fa-times"></i>
                                </a>
                            </p> 
                        </div>';
            }
        }
        return response()->json(['images' => $msg]);
	}
}
