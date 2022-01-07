<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get categories
        $categories = Category::latest()->get();
        
        //return with Api Resource
        return new CategoryResource(true, 'List Categories', $categories);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        // $category = Category::with('products')
        //     //get count review and average review
        //     ->with('products', function ($query) {
        //         $query->withCount('reviews');
        //         $query->withAvg('reviews', 'rating');
        //     })
        //     ->where('slug', $slug)->first();
        // $category = Category::whereHas('products', function(Builder $query) {
        //     $query->where('slug', 'like', '%pamflet%');
        // }, '>', 1)->with('products')->get();
        // $category->with('products', function($query){
        //     $query->withCount('reviews');
        // });

        // $category = Category::with(['products' => function($query) {
        //     $query->withCount('reviews');
        //     $query->withAvg('reviews', 'rating');
        // }], 'category')->where('slug', $slug)->first();
        // dd($category);

        $category = Category::with(['products.user', 'products' => function ($query) {
            $query->withCount('reviews');
            $query->withAvg('reviews', 'rating');
        }])->where('slug', $slug)->first();

        if($category) {
            //return success with Api Resource
            return new CategoryResource(true, 'Product by category : '.$category->name.'', $category);
        }

        //return failed with Api Resource
        return new CategoryResource(false, 'Detail category not found!', null);
    }
}
