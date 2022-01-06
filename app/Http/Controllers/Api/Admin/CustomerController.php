<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::when(request()->q, function($customers) {
   			$customers = $customers->where('name', 'like', '%'. request()->q . '%');
		})->latest()->paginate(5);

        //return with Api Resource
        return new CustomerResource(true, 'List Customers', $customers);
    }
}
