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
    public function index(Request $request){
        if($request->ajax()){
            $faq = Faq::latest('priority')->get();
            return DataTables::of($faq)
            ->addIndexColumn()
            
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
                    <button type="button" onclick="deleteData(\'' . route('backend.feature.faq.destroy', $data->id) . '\')" class="btn btn-danger btn-sm del">
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
        ->route('backend.feature.faq.index')
        ->with('success','new faq successfully created');
    }

    public function edit(Faq $faq){

        return view('backend.layout.faqs.form', ['faq'=> $faq, 'status' => Faq::STATUS]);
    }
    public function update(Request $request, Faq $faq){
        $validated = $request->validate([
            "question"  => "required",
            'answer' => 'required',
            'priority'=> 'required|min:1',
            'status'=> 'required',
        ]);
        // dd($request->all());
        $faq->update($validated);

        return redirect()->route('backend.feature.faq.index')->with('success','Faq Updaed');
    }

    public function destroy(Faq $faq){
        // dd('here');
        try {
            $faq->delete();
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

    public function status($id){
        $faq = Faq::findOrFail($id);
        $faq->status = !$faq->status;
        $faq->save();
        return response()->json([
            'success'=> true,
            'message'=> 'status updated',
            ]);
    }
}
