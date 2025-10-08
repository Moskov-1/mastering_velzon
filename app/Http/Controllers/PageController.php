<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Page::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('page_title', function ($data) {
                    $page_title = $data->page_title;
                    return $page_title;
                })
                ->addColumn('page_content', function ($data) {
                    $page_content = $data->page_content;
                    return $page_content;
                })

                ->addColumn('status', function ($data) {
                    $backgroundColor  = $data->status == Faq::STATUS['ACTIVE'] ? '#4CAF50' : '#ccc';
                    $sliderTranslateX = $data->status == Faq::STATUS['ACTIVE'] ? '26px' : '2px';
                    $sliderStyles     = "position: absolute; top: 2px; left: 2px; width: 20px; height: 20px; background-color: white; border-radius: 50%; transition: transform 0.3s ease; transform: translateX($sliderTranslateX);";

                    $status = '<div class="form-check form-switch" style="margin-left:40px; position: relative; width: 50px; height: 24px; background-color: ' . $backgroundColor . '; border-radius: 12px; transition: background-color 0.3s ease; cursor: pointer;">';
                    $status .= '<input onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" class="form-check-input" id="customSwitch' . $data->id . '" getAreaid="' . $data->id . '" name="status" style="position: absolute; width: 100%; height: 100%; opacity: 0; z-index: 2; cursor: pointer;">';
                    $status .= '<span style="' . $sliderStyles . '"></span>';
                    $status .= '<label for="customSwitch' . $data->id . '" class="form-check-label" style="margin-left: 10px;"></label>';
                    $status .= '</div>';

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

        return view('backend.layout.pages.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.layout.pages.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
               'page_title' => 'required|string|max:255',
                'page_content' => 'required|string|max:2000',
            ]);
            // dd($validator->errors());

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = new Page();
            $data->page_title = $request->page_title;
            $data->page_content = $request->page_content;
            $data->save();

            return redirect()->route('backend.page.index')->with('success', 'Created Successfully !!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong! ' . $e->getMessage());
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page)
    {
        return view('backend.layout.pages.form', ['page'=> $page]);
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Page $page)
    {
        try {
            $validator = Validator::make($request->all(), [
                'page_title' => 'required|string|max:255',
                'page_content' => 'required|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $page->page_title = $request->page_title;
            $page->page_content = $request->page_content;
            $page->save();

            return redirect()->route('backend.page.index')->with('success', 'Updated Successfully.');
        } catch (\Exception $e) {
            // dd($e);
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        try {
            
            $page->delete();

            return response()->json([
                'success' => true,
                'message' => 'Deleted successfully.',
            ]);
        } catch (\Exception) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete the Data.',
            ]);
        }
    }

    public function status(int $id): JsonResponse
    {
        $data = Page::findOrFail($id);
       
        $data->status = !$data->status ;
        $data->save();

        return response()->json([
            'success' => true,
            'message' => 'Status Chaned Successfully.',
            'data'    => $data,
        ]);
    
    }
}
