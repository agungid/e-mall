<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use App\Services\ResponseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api_customer');
    } 

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $carts = Cart::with('product')
                ->where('customer_id', auth()->guard('api_customer')->user()->id)
                ->latest()
                ->get();
        
        //return with Api Resource
        return new CartResource(true, 'Lists Carts : '.auth()->guard('api_customer')->user()->name.'', $carts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:products,id'
        ]);

        if($validator->fails()) {
            return ResponseService::toJson(false, 'Validator errors', 422, [], $validator->errors());
        }

        $item = Cart::with('product')->where('product_id', $request->product_id)
                        ->where('customer_id', auth()->guard('api_customer')->user()->id);
        //check if product already in cart and increment qty
        if ($item->count()) {
            //increment / update quantity
            $item->increment('qty');
            $item = $item->first();
            //sum price * quantity
            $price = $item->product->price * $item->qty;
            //sum weight
            $weight = $item->product->weight * $item->qty;
            $item->update([
                'price'     => $price,
                'weight'    => $weight
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'qty'   => 'required|integer'
            ]);
    
            if($validator->fails()) {
                return ResponseService::toJson(false, 'Validator errors', 422, [], $validator->errors());
            }
            //insert new item cart

            $product = Product::where('id', $request->product_id)->first();
            $item = Cart::create([
                'product_id'    => $request->product_id,
                'customer_id'   => auth()->guard('api_customer')->user()->id,
                'qty'           => $request->qty,
                'price'         => $product->price * $request->qty,
                'weight'        => $product->weight * $request->qty
            ]);

        }
     
        //return with Api Resource
        return new CartResource(true, 'Success Add To Cart', $item);
        
    }
    
    /**
     * getCartPrice
     *
     * @return void
     */
    public function getCartPrice()
    {
        $totalPrice = Cart::with('product')
             ->where('customer_id', auth()->guard('api_customer')->user()->id)
             ->sum('price');
        
        //return with Api Resource
        return new CartResource(true, 'Total Cart Price', $totalPrice);
    }
    
    /**
     * getCartWeight
     *
     * @return void
     */
    public function getCartWeight()
    {
        $totalWeight = Cart::with('product')
        ->where('customer_id', auth()->guard('api_customer')->user()->id)
        ->sum('weight');

        //return with Api Resource
        return new CartResource(true, 'Total Cart Weight', $totalWeight);
    }
    
    /**
     * removeCart
     *
     * @param  mixed $request
     * @return void
     */
    public function removeCart($id)
    {
        try {
            $cart = Cart::find($id);
            
            if($cart) {
                $cart->delete();
                return new CartResource(true, 'Success Remove Item Cart', null);
            }

            throw new ModelNotFoundException('Cart not found!', 404);

        } catch (\Throwable $th) {
            return ResponseService::toJson(false, $th->getMessage(), $th->getCode());
        }
        
    }
}
