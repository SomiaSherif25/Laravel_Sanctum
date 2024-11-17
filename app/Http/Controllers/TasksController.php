<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Resources\TasksResource;
use App\Models\Task;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TasksResource::collection(
            //if task is related to the authenticated user return its id
            Task::where('user_id',Auth::user()->id)->get()
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $request->validated($request->all());

        $task = Task::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'priority' => $request->priority
        ]);

        return new TasksResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        // this is the classic way ti return elemnt by id in laravel
        // $task = Task::where('id',$id)->get();
        // return $task;

        return $this->isNotAuthorized(task: $task) ? $this->isNotAuthorized($task) : new TasksResource($task);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //is an edit method if we have a form in frontend
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        if(Auth::user()->id !== $task->user_id){
            return $this->error('','You are not authorized to update this resource',403);
        }

        $task->update($request->all());
        return new TasksResource($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        // $task->delete();
        // return response(null,204); 
        return $this->isNotAuthorized(task: $task) ? $this->isNotAuthorized($task) : $task->delete();


    }

    private function isNotAuthorized($task){
        if(Auth::user()->id !== $task->user_id){
            return $this->error('','You are not authorized to do this action',403);
        }
    }
}
