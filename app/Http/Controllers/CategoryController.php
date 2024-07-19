<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('todo')->where('user_id', auth()->user()->id)->get();
        return view('category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255'
        ]);
        Category::create([
            'user_id' => auth()->user()->id,
            'title' => $request->title
        ]);

        return redirect()->route('category.index')->with('success', 'Category created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        if (auth()->user()->id == $category->user_id) {
            return view('category.edit', compact('category'));
        } else {
            return redirect()->route('category.index')->with('danger', 'You are not authorized to edit this category!');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'title' => 'required|max:255',
        ]);

        // Practical
        // $todo->title = $request->title;
        // $todo->save();

        // Eloquent Way - Readable
        $category->update([
            'title' => ucfirst($request->title),
        ]);
        return redirect()->route('category.index')->with('success', 'Todo category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if (auth()->user()->id == $category->user_id) {
            $category->delete();
            return redirect()->route('category.index')->with('success', 'Category deleted successfully!');
        } else {
            return redirect()->route('category.index')->with('danger', 'You are not authorized to delete this category!');
        }
    }
}
