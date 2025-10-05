<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectController extends Controller
{

    public function index(){
        return view("backend.layout.projects.index");
    }
    public function create(){

        return view("backend.layout.projects.form");
    }
}
