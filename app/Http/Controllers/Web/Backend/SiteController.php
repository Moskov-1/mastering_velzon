<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index(){
        
     $data['orders']  = [450, 480, 520, 600, 750, 800, 870, 450, 480, 520, 600, 750];
     $data['earnings'] = [1200, 1350, 1500, 1600, 1700, 1900, 2000, 1200, 1350, 1500, 1600, 399];
     $data['refunds'] = [20, 35, 25, 40, 30, 28, 22, 20, 35, 25, 40, 11];
     $data['months'] = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul','Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
        return view("backend.index", $data);
    }
}
