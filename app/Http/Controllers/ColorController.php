<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ColorController extends Controller
{
    public function index(){
        return view ('dress.color');
    }
    public function show_color()
    {
        $sno=0;

        $view_color= color::all();
        if(count($view_color)>0)
        {
            foreach($view_color as $value)
            {

                $modal='<a class="btn btn-outline-secondary btn-sm edit" data-bs-toggle="modal" data-bs-target="#add_color_modal" onclick=edit("'.$value->id.'") title="Edit">
                            <i class="fas fa-pencil-alt" title="Edit"></i>
                        </a>
                        <a class="btn btn-outline-secondary btn-sm edit" onclick=del("'.$value->id.'") title="Delete">
                            <i class="fas fa-trash" title="Edit"></i>
                        </a>';
                $add_data=get_date_only($value->created_at);

                $sno++;
                $json[]= array(
                            $sno,
                            $value->color_name,
                            $add_data,
                            $value->added_by,
                            $modal
                        );
            }
            $response = array();
            $response['success'] = true;
            $response['aaData'] = $json;
            echo json_encode($response);
        }
        else
        {
            $response = array();
            $response['sEcho'] = 0;
            $response['iTotalRecords'] = 0;
            $response['iTotalDisplayRecords'] = 0;
            $response['aaData'] = [];
            echo json_encode($response);
        }
    }

    public function add_color(Request $request){

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;

        $color = new color();


        $color->color_name = $request['color_name'];
        $color->added_by = $user;
        $color->user_id = $user_id;
        $color->save();
        return response()->json(['color_id' => $color->id]);

    }

    public function edit_color(Request $request){
        $color = new color();
        $color_id = $request->input('id');

        // Use the Eloquent where method to retrieve the color by column name
        $color_data = color::where('id', $color_id)->first();
        // Add more attributes as needed
        $data = [
            'color_id' => $color_data->id,
            'color_name' => $color_data->color_name,
            // Add more attributes as needed
        ];

        return response()->json($data);
    }

    public function update_color(Request $request){

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;
        $color_id = $request->input('color_id');
        $color = color::where('id', $color_id)->first();
        $color->color_name = $request->input('color_name');
        $color->updated_by = $user;
        $color->save();

    }

    public function delete_color(Request $request){
        $color_id = $request->input('id');
        $color = color::where('id', $color_id)->first();
        $color->delete();
    }
}
