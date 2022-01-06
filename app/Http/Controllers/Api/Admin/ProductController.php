<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ResponseService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with('category')->when(request()->q, function($products) {
            $products = $products->where('title', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);
        
        return new ProductResource(true, 'List products', $products);
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
            'image'         => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'title'         => 'required|unique:products',
            'category_id'   => 'required',
            'description'   => 'required',
            'weight'        => 'required',
            'price'         => 'required',
            'stock'         => 'required',
            'discount'      => 'required'
        ]);

        if ($validator->fails()) {
            return ResponseService::toJson(false, 'Validation errors', 422, [], $validator->errors());
            // throw new HttpResponseException($response);
        }

        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        //create product
        $product = Product::create([
            'image'         => $image->hashName(),
            'title'         => $request->title,
            'slug'          => Str::slug($request->title, '-'),
            'category_id'   => $request->category_id,
            'user_id'       => auth()->guard('api_admin')->user()->id,
            'description'   => $request->description,
            'weight'        => $request->weight,
            'price'         => $request->price,
            'stock'         => $request->stock,
            'discount'      => $request->discount
        ]);

        if($product) {
            return new ProductResource(true, 'Product create success', $product);
        }

        //return failed with Api Resource
        return new ProductResource(false, 'Product create failed', null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::whereId($id)->first();
        
        if($product) {
            //return success with Api Resource
            return new ProductResource(true, 'Detail data product!', $product);
        }

        //return failed with Api Resource
        return new ProductResource(false, 'Detail product not found', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'title'         => 'required|unique:products,title,'.$product->id,
            'category_id'   => 'required',
            'description'   => 'required',
            'weight'        => 'required',
            'price'         => 'required',
            'stock'         => 'required',
            'discount'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $currentProduct = [
            'title'         => $request->title,
            'slug'          => Str::slug($request->title, '-'),
            'category_id'   => $request->category_id,
            'user_id'       => auth()->guard('api_admin')->user()->id,
            'description'   => $request->description,
            'weight'        => $request->weight,
            'price'         => $request->price,
            'stock'         => $request->stock,
            'discount'      => $request->discount
        ];

        //check image update
        if ($request->file('image')) {
            Storage::disk('local')->delete('public/products/'.basename($product->image));
        
            $image = $request->file('image');
            $image->storeAs('public/products', $image->hashName());
            array_merge($currentProduct, [ 'image' => $image->hashName()]);
        }

        $product->update($currentProduct);

        if($product) {
            return new ProductResource(true, 'Product update success', $product);
        }

        return new ProductResource(false, 'Product update failed', null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //remove image
        Storage::disk('local')->delete('public/products/'.basename($product->image));

        if($product->delete()) {
            return new ProductResource(true, 'Product delete success', null);
        }
        
        return new ProductResource(false, 'Product delete failed', null);
    }

}
