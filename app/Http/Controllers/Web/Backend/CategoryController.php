<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index(Request $request){
        if($request->ajax()){
            $data = Category::latest()->get();
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('image', function ($data) {
                $avatar = $data->avatar ? asset($data->avatar) : asset('assests/images/users/no-image.jpg');
                return '<img src="' . $avatar . '" width="60" alt="Article Image"/>';
            })
            ->addColumn('name', function($data){
                return $data->name;
            })
            ->addColumn('type', function($data){
                return $data->type;
            })
            ->addColumn('parent', function($data){
                return $data->parent->name;
            })
            
            ->addColumn('status', function ($data) {
                $backgroundColor  = $data->status ? '#4CAF50' : '#ccc';
                $sliderTranslateX = $data->status ? '26px' : '2px';
                $status = getStatusHTML($data, $backgroundColor, $sliderTranslateX);

                return $status;
            })

            ->addColumn('action', function ($data) {
                return '
                <button onclick="edit(' . $data->id . ')" type="button" class="btn btn-info btn-sm">
                    <i class="mdi mdi-pencil"></i>
                </button>
                <button type="button" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger btn-sm del">
                    <i class="mdi mdi-delete"></i>
                </button>
            ';
            })
            ->rawColumns(['page_title', 'page_content', 'status', 'action'])
            ->make();
        }
        return view("backend.layout.categories.index");
    }

    public function create(){
        return view("backend.layout.categories.form");
    }
}
