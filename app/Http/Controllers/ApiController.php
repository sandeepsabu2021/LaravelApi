<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ApiTask;
use App\Http\Resources\TaskResource;
use App\Http\Controllers\JWTController;

class ApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taskdata = ApiTask::latest()->get();
        return response([
            'task' => TaskResource::collection($taskdata),
            'Msg' => 'Success'
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'name' => 'required|string|max:50',
            'body' => 'required|min:5|max:250'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        } else {
            $task = new ApiTask();
            $task->name = $req->name;
            $task->body = $req->body;
            if ($task->save()) {
                return response(['task' => new TaskResource($task), 'Msg' => 'Success'], 201); 
            } else {
                return response()->json(['Msg' => 'Failed']);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response(['task' => new TaskResource(ApiTask::find($id)),
        'Msg' => 'Success'], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $id)
    {
        $validator = Validator::make($req->all(), [
            'name' => 'required|string|max:50',
            'body' => 'required|min:5|max:250'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        } else {
            $task = ApiTask::find($id);
            $task->name = $req->name;
            $task->body = $req->body;
            if ($task->save()) {
                return response(['task' => new TaskResource($task), 'Msg' => 'Update Success'], 201); //using resource
            } else {
                return response()->json(['Msg' => 'Failed']);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = ApiTask::find($id);
        if ($task->delete()) {
            return response(['task' => new TaskResource($task), 'Msg' => 'Delete Success'], 201); //using resource
        } else {
            return response()->json(['Msg' => 'Failed']);
        }
    }
}
