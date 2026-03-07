<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()    {
        $data['total_earnings'] = 0;
        $data['confirmed_bookings'] = 0;
        $data['my_balance'] = 0;
        $data['customers'] = 0;
        $data['count_vendors'] = 0;

        $data['months'] = [];
        $data['orders'] = [];
        $data['earnings'] = [];
        $data['refunds'] = [];

        $data['chart_data'] = [
            'months' => [],
            'orders' => [],
            'earnings' => [],
            'refunds' => []
        ];

        $vendors = collect([]);

        $data['vendors'] = $vendors;

        return view("backend.index", $data);
    }
}
