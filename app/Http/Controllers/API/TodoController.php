<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search');
        if ($search) {
            $todos = Todo::with('category')
                ->where('user_id', auth()->user()->id)
                ->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%');
                })
                ->latest()
                ->get();
            return response()->json([
                'status' => 'success',
                'data' => [
                    'todos' => $todos,
                ]
            ], 200);
        }

        $todos = Todo::with('category')
            ->where('user_id', auth()->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'todos' => $todos,
            ]
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|max:255',
                'category_id' => [
                    'nullable',
                    Rule::exists('categories', 'id')->where(function ($query) {
                        $query->where('user_id', auth()->user()->id);
                    })
                ]
            ]);

            $todo = Todo::create([
                'title' => ucfirst($request->title),
                'user_id' => auth()->user()->id,
                'category_id' => $request->category_id
            ]);

            $todo = Todo::with('category')
                ->where('id', $todo->id)
                ->first();
            return response()->json([
                'status' => 'success',
                'message' => 'Todo created',
                'data' => [
                    'todo' => $todo,
                ]
            ], 201);
        } catch (ValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $exception->errors(),
            ], 422);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        $todo = Todo::with('category')
            ->where('id', $todo->id)
            ->first();
        if ($todo->user_id != auth()->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'todo' => $todo,
            ]
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Todo $todo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo)
    {
        try {
            $request->validate([
                'title' => 'required|max:255',
                'category_id' => [
                    'nullable',
                    Rule::exists('categories', 'id')->where(function ($query) {
                        $query->where('user_id', auth()->user()->id);
                    })
                ]
            ]);
            if (auth()->user()->id !== $todo->user_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden'
                ], 403);
            }
            $todo->update([
                'title' => ucfirst($request->title),
                'category_id' => $request->category_id
            ]);
            $todo = Todo::with('category')
                ->where('id', $todo->id)
                ->first();
            return response()->json([
                'status' => 'success',
                'message' => 'Todo updated',
                'data' => [
                    'todo' => $todo,
                ]
            ], 200);
        } catch (ValidationException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $exception->errors(),
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        if (auth()->user()->id !== $todo->user_id) {

            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden'
            ], 403);
        }
        $todo->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Todo deleted'
        ], 200);
    }

    public function complete(Todo $todo)
    {
        if (auth()->user()->id !== $todo->user_id) {
            # code...
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden'
            ], 403);
        }
        $todo->update([
            'is_complete' => true
        ]);
        $todo = Todo::with('category')
            ->where('id', $todo->id)
            ->first();
        return response()->json([
            'status' => 'success',
            'message' => 'Todo completed',
            'data' => [
                'todo' => $todo,
            ]
        ], 200);
    }

    public function uncomplete(Todo $todo)
    {
        if (auth()->user()->id !== $todo->user_id) {

            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden'
            ], 403);
        }
        $todo->update([
            'is_complete' => false
        ]);
        $todo = Todo::with('category')
            ->where('id', $todo->id)
            ->first();
        return response()->json([
            'status' => 'success',
            'message' => 'Todo uncompleted',
            'data' => [
                'todo' => $todo,
            ]
        ], 200);
    }

    public function deleteAllCompleted()
    {
        $todos = Todo::where('user_id', auth()->user()->id)
            ->where('is_complete', true)
            ->get();
        if ($todos->count() == 0) {
            # code...
            return response()->json([
                'status' => 'error',
                'message' => 'No completed todos found',
            ], 404);
        }

        foreach ($todos as $todo) {
            $todo->delete();
        }
        return response()->json([
            'status' => 'success',
            'message' => '' . $todos->count() . ' completed todos deleted',
        ], 200);
    }
}
