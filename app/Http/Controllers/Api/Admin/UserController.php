<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get users
        $users = User::when(request()->q, function($users) {
            $users = $users->where('name', 'like', '%'. request()->q . '%');
        })->latest()->paginate(5);
        
        //return with Api Resource
        return new UserResource(true, 'List Users', $users);
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
            'name'     => 'required',
            'email'    => 'required|unique:users',
            'password' => 'required|confirmed' 
        ]);

        if ($validator->fails()) {
            return ResponseService::toJson(false, 'Validation error', 422, [], $validator->errors());
        }

        //create user
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password)
        ]);

        if($user) {
            //return success with Api Resource
            return new UserResource(true, 'User create success', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'User create failed', null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::whereId($id)->first();
        
        if($user) {
            //return success with Api Resource
            return new UserResource(true, 'Detail user', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'User not found!', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|unique:users,email,'.$user->id,
            'password' => 'confirmed'
        ]);

        if ($validator->fails()) {
            return ResponseService::toJson(false, 'Validation error', 422, [], $validator->errors());
        }

        $currentUser = [
            'name'      => $request->name,
            'email'     => $request->email,
        ];

        if($request->has('password')) {
            $currentUser['password'] = bcrypt($request->password);
        }

        $user->update($currentUser);

        if($user) {
            //return success with Api Resource
            return new UserResource(true, 'User update success', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'User update failed', null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if($user->delete()) {
            //return success with Api Resource
            return new UserResource(true, 'User delete success', null);
        }

        //return failed with Api Resource
        return new UserResource(false, 'User delete failed', null);
    }
}
