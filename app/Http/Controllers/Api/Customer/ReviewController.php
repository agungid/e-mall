<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        //check review already
        $check_review = Review::where('order_id', $request->order_id)->where('product_id', $request->product_id)->first();

        if($check_review) {
            return ResponseService::toJson(false, 'Review found', 409, [], $check_review);
        }

        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer|exists:customers,id',
            'order_id'    => 'required|integer|exists:orders,id',
            'product_id'  => 'required|integer|exists:products,id',
            'rating'      => 'required|integer',
            'review'      => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseService::toJson(false, 'Validation errors', 422, [], $validator->errors());
        }

        $review = Review::create([
            'rating'        => $request->rating,
            'review'        => $request->review,
            'product_id'    => $request->product_id,
            'order_id'      => $request->order_id,
            'customer_id'   => auth()->guard('api_customer')->user()->id
        ]);

        if($review) {
            return new ReviewResource(true, 'Review create success', $review);
        }

        //return failed with Api Resource
        return new ReviewResource(false, 'Review failed success', null);
    }
}
