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
            // ->addColumn('image', function ($data) {
            //     $avatar = $data->avatar ? asset($data->avatar) : asset('assests/images/users/no-image.jpg');
            //     return '<img src="' . $avatar . '" width="60" alt="Article Image"/>';
            // })
            ->addColumn('name', function($data){
                return $data->name;
            })
            ->addColumn('type', function($data){
                return $data->type;
            })
            ->addColumn('parent', function($data){
                return $data->parent->name ?? 'N/A';
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
            ->rawColumns(['parent', 'status', 'action'])
            ->make(true);
        }
        return view("backend.layout.categories.index");
    }

    public function create(){

        $data['parents'] = Category::all();
        $data['statuses'] = Category::_status();
        $data['types'] = Category::_types();

        return view("backend.layout.categories.form", $data);
    }

    public function store(Request $request){
        $request ->validate([
            "name"=> "required",
            "type"=> "required",    
            "parent_id"=> "nullable|exists:categories,id",
        ]);

        $category = Category::create($request->only(["name","type","parent_id",'status']));

        return redirect()->route('backend.feature.category.index')->with('success','category created successfully');
    }

    public function edit(Category $category){
         $data['parents'] = Category::all()->except($category->id);
        $data['statuses'] = Category::_status();
        $data['types'] = Category::_types();
        $data['category'] = $category;
        return view("backend.layout.categories.form", $data);
    }

    public function update(Request $request, Category $category){
        $request ->validate([
            "name"=> "required",
            "type"=> "required",    
            "parent_id"=> "nullable|not_in:{$category->id}|exists:categories,id",
        ]);

        $category->update($request->only(["name","type","parent_id","status"]));

        return redirect()->route('backend.feature.category.index')->with('success','category updated successfully');
    }

    public function destroy(Category $category){
        $category->delete();
        return redirect()->route('backend.feature.category.index')->with('success','category deleted successfully');
    }

    public function status($id){
        $category = Category::find($id);
        $category->status = !$category->status;
        $category->save();

        return response()->json([
            'success'=> true,
            'message'=> 'status updated',
            ]);

    }
}
