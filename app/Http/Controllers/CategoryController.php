<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    public function index(){


        if (!Auth::check()) {

            return redirect()->route('login_page')->with('error', 'Please LogIn first()');
        }

        $user = Auth::user();

        if (in_array(2, explode(',', $user->permit_type))) {

            return view ('dress.category');
        } else {

            return redirect()->route('home')->with( 'error', 'You dont have Permission');
        }
    }
    public function show_category()
    {
        $sno=0;

        $view_category= Category::all();
        if(count($view_category)>0)
        {
            foreach($view_category as $value)
            {

                $modal='<a class="btn btn-outline-secondary btn-sm edit" data-bs-toggle="modal" data-bs-target="#add_category_modal" onclick=edit("'.$value->id.'") title="Edit">
                            <i class="fas fa-pencil-alt" title="Edit"></i>
                        </a>
                        <a class="btn btn-outline-secondary btn-sm edit" onclick=del("'.$value->id.'") title="Delete">
                            <i class="fas fa-trash" title="Edit"></i>
                        </a>';
                $add_data=get_date_only($value->created_at);

                $sno++;
                $json[]= array(
                            $sno,
                            $value->category_name,
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

    public function add_category(Request $request){

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;
        $category = new Category();


        $category->category_name = $request['category_name'];
        $category->added_by = $user;
        $category->user_id = $user_id;
        $category->save();
        return response()->json(['category_id' => $category->id]);

    }

    public function edit_category(Request $request){
        $category = new Category();
        $category_id = $request->input('id');

        // Use the Eloquent where method to retrieve the category by column name
        $category_data = Category::where('id', $category_id)->first();
        // Add more attributes as needed
        $data = [
            'category_id' => $category_data->id,
            'category_name' => $category_data->category_name,
            // Add more attributes as needed
        ];

        return response()->json($data);
    }

    public function update_category(Request $request){

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;
        $category_id = $request->input('category_id');
        $category = Category::where('id', $category_id)->first();
        $category->category_name = $request->input('category_name');
        $category->updated_by = $user;
        $category->save();

    }

    public function delete_category(Request $request){
        $category_id = $request->input('id');
        $category = Category::where('id', $category_id)->first();
        $category->delete();
    }
}
