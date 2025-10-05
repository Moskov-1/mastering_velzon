<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(){

    }
    public function create(){
        return view("backend.layout.faqs.form");
    }

    public function store(Request $request){

    }
}
