<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Support\Facades\App;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $todos=\App\Models\Todo::orderBy('created_at','desc')->get();
        return view('todos.index',compact('todos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('todos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated=$request->validate([
            'title'=>'required|max:255',
            'description'=>'nullable|max:1000',
        ],
        [
            'title.required'=>'le titre est obligatoire',
            'title.max'=>'le titre ne doit pas depasser 255 caracteres',
            'description.max'=>'la description ne doit pas depasser 1000 caracteres',

        ]);
        Todo::create($validated);
        return redirect()->route('todos.index')->with('success','Tache creee avec succes');


    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Todo $todo)
    {
        return view('todos.edit',compact('todo'));
    }

        //


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo)
    {
        $validated=$request->validate([
            'title'=>'required|max:255',
            'description'=>'nullable|max:1000',
        ],
        [
            'title.required'=>'le titre est obligatoire',
            'title.max'=>'le titre ne doit pas depasser 255 caracteres',
            'description.max'=>'la description ne doit pas depasser 1000 caracteres',

        ]);
        $todo->update($validated);
        return redirect()->route('todos.index')->with('success','Tache mise a jour avec succes');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        $todo->delete();
        return redirect()->route('todos.index')->with('success','Tache supprimee avec succes');
    }

    public function toggle(Todo $todo)
    {
        $todo->update(['completed'=>!$todo->completed]);
        return redirect()->route('todos.index')->with('success','Tache mise a jour avec succes');
    }
}
