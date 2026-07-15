<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Core\Analytics\FunnelAnalytics;
use Illuminate\Http\Request;

class FunnelController extends Controller
{
    public function index()
    {
        return view('admin.funnel.index');
    }

    public function data(Request $request, FunnelAnalytics $analytics)
    {
        $period = $request->get('period', 'month');
        $data = $analytics->compute($period);

        return response()->json($data);
    }
}
