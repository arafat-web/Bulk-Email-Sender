<?php

namespace App\Http\Controllers;

use App\Models\ContactTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContactTagController extends Controller
{
    /**
     * Display a listing of tags.
     */
    public function index()
    {
        $tags = ContactTag::where('user_id', Auth::id())
            ->withCount('contacts')
            ->orderBy('name')
            ->get();

        return view('tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new tag.
     */
    public function create()
    {
        return view('tags.create');
    }

    /**
     * Store a newly created tag in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:contact_tags,name,NULL,id,user_id,' . Auth::id(),
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        ContactTag::create([
            'name' => $request->name,
            'color' => $request->color,
            'description' => $request->description,
            'user_id' => Auth::id()
        ]);

        return redirect()->route('tags.index')->with('success', 'Tag created successfully.');
    }

    /**
     * Show the form for editing the specified tag.
     */
    public function edit(ContactTag $tag)
    {
        $this->authorize('update', $tag);
        return view('tags.edit', compact('tag'));
    }

    /**
     * Update the specified tag in storage.
     */
    public function update(Request $request, ContactTag $tag)
    {
        $this->authorize('update', $tag);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:contact_tags,name,' . $tag->id . ',id,user_id,' . Auth::id(),
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $tag->update($request->only(['name', 'color', 'description']));

        return redirect()->route('tags.index')->with('success', 'Tag updated successfully.');
    }

    /**
     * Remove the specified tag from storage.
     */
    public function destroy(ContactTag $tag)
    {
        $this->authorize('delete', $tag);
        $tag->delete();
        return redirect()->route('tags.index')->with('success', 'Tag deleted successfully.');
    }
}
