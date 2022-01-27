<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\TaskResource;
use App\Models\ApiTask;

class JWTController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        } else {
            $user = new User();
            $user->name = $req->name;
            $user->email = $req->email;
            $user->password = Hash::make($req->password);
            if ($user->save()) {
                // return response()->json(['task'=>$tasks, 'msg' => 'Success']);
                return response(['user' => new TaskResource($user), 'Msg' => 'Success'], 201); //using resource
            } else {
                return response()->json(['msg' => 'Failed']);
            }
        }
    }

    public function login(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        } else {
            if (!$token = auth()->attempt($validator->validated())) {
                return response()->json(['error' => 'Unauthorized User'], 401);
            }
            return $this->respondWithToken($token);
        }
    }

    public function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function profile(){
        return response()->json(auth()->user());
    }

    public function refresh(){
        return $this->respondWithToken(auth()->refresh());
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['msg' => 'Logout']);
    }
}
