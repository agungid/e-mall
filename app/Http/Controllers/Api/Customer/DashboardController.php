<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //count invoice
        $pending = Invoice::where('status', 'pending')->where('customer_id', auth()->guard('api_customer')->user()->id)->count();
        $success = Invoice::where('status', 'success')->where('customer_id', auth()->guard('api_customer')->user()->id)->count();
        $expired = Invoice::where('status', 'expired')->where('customer_id', auth()->guard('api_customer')->user()->id)->count();
        $failed  = Invoice::where('status', 'failed')->where('customer_id', auth()->guard('api_customer')->user()->id)->count();

        $data = [
            'count' => [
                'pending'   => $pending,
                'success'   => $success,
                'expired'   => $expired,
                'failed'    => $failed
            ]
        ];
        
        return ResponseService::toJson(true, 'Statisitik data', 200, $data);
    }
}
