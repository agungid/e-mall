<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\RajaOngkirResource;
use App\Models\City;
use App\Models\Province;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class RajaOngkirController extends Controller
{
    /**
     * getProvinces
     *
     * @return void
     */
    public function getProvinces()
    {
        //get all provinces
        $provinces = Province::all();

        //return with Api Resource
        return new RajaOngkirResource(true, 'Data Provinces', $provinces);
    }
    
    /**
     * getCities
     *
     * @param  mixed $request
     * @return void
     */
    public function getCities(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'province_id'   => 'required|integer|exists:provinces,province_id'
        ]);

        if ($validator->fails()) {
            return ResponseService::toJson(false, 'Validation errors', 422, [], $validator->errors());
        }
        //get province name
        $province = Province::where('province_id', $request->province_id)->first();

        //get cities by province
        $cities = City::where('province_id', $request->province_id)->get();

        //return with Api Resource
        return new RajaOngkirResource(true, 'Data Cities By Province : '.$province->name.'', $cities);
    }
    
    /**
     * checkOngkir
     *
     * @param  mixed $request
     * @return void
     */
    public function checkShippingCost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_destination'  => 'required|integer|exists:cities,city_id',
            'to_destination'    => 'required|integer|exists:cities,city_id',
            'weight'            => 'required|between:0,99.99',
            'courier'           => 'required|string'
        ]);

        if ($validator->fails()) {
            return ResponseService::toJson(false, 'Validation errors', 422, [], $validator->errors());
        }
        
        //Fetch Rest API
        $response = Http::withHeaders([
            //api key rajaongkir
            'key'          => config('services.rajaongkir.key')
        ])->post('https://api.rajaongkir.com/starter/cost', [

            //send data
            'origin'      => $request->from_destination,
            'destination' => $request->to_destination,
            'weight'      => $request->weight,
            'courier'     => $request->courier
        ]);

        //return with Api Resource
        return new RajaOngkirResource(true, 'Postage cost lists : '.$request->courier.'', $response['rajaongkir']['results'][0]['costs']);
    }
}
