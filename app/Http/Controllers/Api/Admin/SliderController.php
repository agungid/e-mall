<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use App\Models\Slider;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get sliders
        $sliders = Slider::latest()->paginate(5);
        
        //return with Api Resource
        return new SliderResource(true, 'List Sliders', $sliders);
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
            'link'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseService::toJson(false, 'Validation errors', 422, [], $validator->errors());
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/sliders', $image->hashName());

        //create slider
        $slider = Slider::create([
            'image'=> $image->hashName(),
            'link' => $request->link,
        ]);

        if($slider) {
            //return success with Api Resource
            return new SliderResource(true, 'Slider create success', $slider);
        }

        //return failed with Api Resource
        return new SliderResource(false, 'Slider create failed!', null);
    }

        /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Slider $slider)
    {
        //remove image
        Storage::disk('local')->delete('public/sliders/'.basename($slider->image));

        if($slider->delete()) {
            //return success with Api Resource
            return new SliderResource(true, 'Slider delete success', null);
        }

        //return failed with Api Resource
        return new SliderResource(false, 'Slider delete failed', null);
    }
}
