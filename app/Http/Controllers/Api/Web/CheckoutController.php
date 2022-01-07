<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\CheckoutResource;
use App\Models\Cart;
use App\Models\Invoice;
use App\Services\MidtransService;
use App\Services\ResponseService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    protected $midtransServicenew;
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        //set middleware
        $this->middleware('auth:api_customer');
        $this->midtransServicenew = new MidtransService();
    } 
    
    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'courier'         => 'required',
            'courier_service' => 'required',
            'courier_cost'    => 'required',
            'weight'          => 'required|integer',
            'name'            => 'required|string|max:150',
            'phone'           => 'required|string|max:12',
            'city_id'         => 'required|integer|exists:cities,city_id',
            'province_id'     => 'required|integer|exists:provinces,province_id',
            'address'         => 'required|string',
            'grand_total'     => 'required'
        ]);

        if($validator->fails()) {
            return ResponseService::toJson(false, 'Validator errors', 422, [], $validator->errors());
        }

        try {
            DB::transaction(function() use ($request) {
                //store invoice
                $invoice = Invoice::create([
                    'invoice'           => Invoice::generateInvoiceNumber(),
                    'customer_id'       => auth()->guard('api_customer')->user()->id,
                    'courier'           => $request->courier,
                    'courier_service'   => $request->courier_service,
                    'courier_cost'      => $request->courier_cost,
                    'weight'            => $request->weight,
                    'name'              => $request->name,
                    'phone'             => $request->phone,
                    'city_id'           => $request->city_id,
                    'province_id'       => $request->province_id,
                    'address'           => $request->address,
                    'grand_total'       => $request->grand_total,
                    'status'            => 'pending',
                ]);
    
                $carts = Cart::where('customer_id', auth()->guard('api_customer')->user()->id);
    
                if(!$carts->exists()) {
                    throw new ModelNotFoundException("Cart not found. Please check before", 404);
                }
    
                //store orders by invoice
                foreach ($carts->get() as $cart) {
    
                    //insert product ke table order
                    $invoice->orders()->create([
                        'invoice_id'    => $invoice->id,   
                        'product_id'    => $cart->product_id,
                        'qty'           => $cart->qty,
                        'price'         => $cart->price,
                    ]);   
    
                }
                
                //remove cart by customer
                Cart::with('product')
                    ->where('customer_id', auth()->guard('api_customer')->user()->id)
                    ->delete();
    
                // Buat transaksi ke midtrans kemudian save snap tokennya.
                $payload = [
                    'transaction_details' => [
                        'order_id'      => $invoice->invoice,
                        'gross_amount'  => $invoice->grand_total,
                    ],
                    'customer_details' => [
                        'first_name'       => $invoice->name,
                        'email'            => auth()->guard('api_customer')->user()->email,
                        'phone'            => $invoice->phone,
                        'shipping_address' => $invoice->address  
                    ]
                ];
    
                //create snap token
                $snapToken = Snap::getSnapToken($payload);
    
                //update snap_token
                $invoice->snap_token = $snapToken;
                $invoice->save();
    
                //make response "snap_token"
                $this->response['snap_token'] = $snapToken;
    
            });
    
            //return with Api Resource
            return New CheckoutResource(true, 'Checkout Successfully', $this->response);
        } catch (\Throwable $th) {
            return ResponseService::toJson(false, $th->getMessage(), $th->getCode());
        }

    }
}
