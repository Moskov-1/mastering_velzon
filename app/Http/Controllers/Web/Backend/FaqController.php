<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\Faq;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
// use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class FaqController extends Controller
{   
     public function index_old(Request $request) {
        if ($request->ajax()) {
            $data = Faq::latest()
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('question', function ($data) {
                    $name = $data->name;
                    return $name;
                })
                ->addColumn('answer', function ($data) {
                    $name = $data->name;
                    return $name;
                })
                
                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                <a href="#" type="button" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger fs-14 text-white delete-icn" title="Delete">
                                    <i class="fe fe-trash"></i>
                                </a>
                            </div>';
                })
                ->rawColumns(['question', 'answer','action'])
                ->make();
        }
        return view('backend.layout.faqs.index-v2');
    }
    public function index(Request $request){
        if($request->ajax()){
            $faq = Faq::where("status", Faq::STATUS['ACTIVE'])->latest('priority')->get();
            return DataTables::of($faq)
            ->addIndexColumn()
            ->addColumn('id', function($faq){
                return ''.$faq->id.'';
            })
            ->addColumn('question', function($faq){
                return ''.$faq->question.'';
            })
             ->addColumn('answer', function($faq){
                return ''.$faq->answer.'';
            })
            ->addColumn('priority', function($faq){
                return ''.$faq->priority.'';
            })
            ->addColumn('status', function ($data) {
                return '<div class="form-check form-switch mb-2">
                            <input class="form-check-input" onclick="statusFaq(' . $data->id . ')" type="checkbox" ' . ($data->status == Faq::STATUS['ACTIVE'] ? 'checked' : '') . '>
                        </div>';
            })
            ->addColumn('action', function ($data) {
                return '
                    <button onclick="editFaq(' . $data->id . ')" type="button" class="btn btn-info btn-sm">
                        <i class="mdi mdi-pencil"></i>
                    </button>
                    <button type="button" onclick="deleteData(\'' . route('backend.faq.destroy', $data->id) . '\')" class="btn btn-danger btn-sm del">
                        <i class="mdi mdi-delete"></i>
                    </button>
                ';
            })
            ->setRowAttr([
                'data-id' => function ($data) {
                    return $data->id;
                }
            ])
            ->rawColumns(['question','status','action'])
            ->make(true);
            ;
        }
        return view("backend.layout.faqs.index");
    }
    public function create(){
        $data['status'] = Faq::STATUS;
        return view("backend.layout.faqs.form", $data);
    }

    public function store(Request $request){
        $validated = $request->validate([
            "question"  => "required",
            'answer' => 'required',
            'priority'=> 'required|min:1',
            'status'=> 'required',
        ]);
        // dd($request->all());
        Faq::create($validated);

        return redirect()
        ->route('backend.faq.index')
        ->with('success','new faq successfully created');
    }
}
