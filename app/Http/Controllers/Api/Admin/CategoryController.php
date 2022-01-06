<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::when(request()->q, function($categories) {
            $categories = $categories->where('name', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);
        
        return new CategoryResource(true, 'List Data Categories', $categories);
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
            'image'    => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'name'     => 'required|unique:categories',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/categories', $image->hashName());

        //create category
        $category = Category::create([
            'image'=> $image->hashName(),
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if($category) {
            //return success with Api Resource
            return new CategoryResource(true, 'Category create success', $category);
        }

        //return failed with Api Resource
        return new CategoryResource(false, 'Category create failed', null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::whereId($id)->first();
        
        if($category) {
            return new CategoryResource(true, 'Detail Data Category!', $category);
        }

        return new CategoryResource(false, 'Category was not found', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|unique:categories,name,'.$category->id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $curentCategory = [
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-')
        ];

        if ($request->file('image')) {

            Storage::disk('local')->delete('public/categories/'.basename($category->image));
        
            $image = $request->file('image');
            $image->storeAs('public/categories', $image->hashName());
            array_merge($curentCategory, ['image'=> $image->hashName()]);
        }

        $category->update($curentCategory);

        if($category) {
            return new CategoryResource(true, 'Update category success', $category);
        }

        //return failed with Api Resource
        return new CategoryResource(false, 'Update category failed', null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        Storage::disk('local')->delete('public/categories/'.basename($category->image));

        if($category->delete()) {
            return new CategoryResource(true, 'Category deleted success', null);
        }

        return new CategoryResource(false, 'Category delete failed', null);
    }
}
