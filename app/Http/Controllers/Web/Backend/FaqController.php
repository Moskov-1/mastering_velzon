<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;

class FaqController extends Controller
{
    public function index(Request $request){
        
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
