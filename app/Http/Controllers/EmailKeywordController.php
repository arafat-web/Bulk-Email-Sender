<?php

namespace App\Http\Controllers;

use App\Models\EmailKeyword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class EmailKeywordController extends Controller
{
    /**
     * Display a listing of keywords.
     */
    public function index()
    {
        $keywords = EmailKeyword::orderBy('key')->get();
        $stats = [
            'total' => EmailKeyword::count(),
            'active' => EmailKeyword::active()->count(),
        ];
        // Sample contacts for the live preview tester (current user's own contacts).
        $sampleContacts = \App\Models\EmailContact::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get(['id', 'email', 'first_name', 'last_name', 'company', 'phone']);

        return view('keywords.index', compact('keywords', 'stats', 'sampleContacts'));
    }

    /**
     * Show the form for creating a new keyword.
     */
    public function create()
    {
        $sources = EmailKeyword::SOURCES;

        return view('keywords.create', compact('sources'));
    }

    /**
     * Store a newly created keyword.
     */
    public function store(Request $request)
    {
        $request->merge(['key' => EmailKeyword::normalizeKey($request->input('key', ''))]);

        $request->validate([
            'key' => 'required|string|max:50|regex:/^[a-z0-9_]+$/|unique:email_keywords,key',
            'label' => 'nullable|string|max:255',
            'source_column' => ['nullable', 'string', Rule::in(array_merge(array_keys(EmailKeyword::SOURCES), ['name']))],
            'default_value' => 'nullable|string|max:1000',
            'description' => 'nullable|string|max:1000',
        ], [
            'key.required' => 'Please enter a keyword.',
            'key.regex' => 'Keyword may only contain letters, numbers and underscores.',
            'key.unique' => 'This keyword already exists.',
        ]);

        try {
            EmailKeyword::create([
                'key' => $request->key,
                'label' => $request->label,
                'source_column' => $request->source_column ?: null,
                'default_value' => $request->default_value,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('keywords.index')
                ->with('success', "Keyword [{$request->key}] created successfully!");
        } catch (\Exception $e) {
            Log::error('Failed to create email keyword', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()->withInput()->with('error', 'Failed to create keyword. Please try again.');
        }
    }

    /**
     * Show the form for editing the keyword.
     */
    public function edit(EmailKeyword $keyword)
    {
        $sources = EmailKeyword::SOURCES;

        return view('keywords.edit', compact('keyword', 'sources'));
    }

    /**
     * Update the keyword.
     */
    public function update(Request $request, EmailKeyword $keyword)
    {
        $request->merge(['key' => EmailKeyword::normalizeKey($request->input('key', ''))]);

        $request->validate([
            'key' => 'required|string|max:50|regex:/^[a-z0-9_]+$/|unique:email_keywords,key,'.$keyword->id,
            'label' => 'nullable|string|max:255',
            'source_column' => ['nullable', 'string', Rule::in(array_merge(array_keys(EmailKeyword::SOURCES), ['name']))],
            'default_value' => 'nullable|string|max:1000',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $keyword->update([
                'key' => $request->key,
                'label' => $request->label,
                'source_column' => $request->source_column ?: null,
                'default_value' => $request->default_value,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('keywords.index')
                ->with('success', "Keyword [{$keyword->key}] updated successfully!");
        } catch (\Exception $e) {
            Log::error('Failed to update email keyword', [
                'keyword_id' => $keyword->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()->withInput()->with('error', 'Failed to update keyword. Please try again.');
        }
    }

    /**
     * Remove the keyword.
     */
    public function destroy(EmailKeyword $keyword)
    {
        try {
            $placeholder = $keyword->placeholder;
            $keyword->delete();

            return redirect()->route('keywords.index')
                ->with('success', "Keyword '{$placeholder}' deleted successfully!");
        } catch (\Exception $e) {
            Log::error('Failed to delete email keyword', [
                'keyword_id' => $keyword->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()->with('error', 'Failed to delete keyword.');
        }
    }

    /**
     * Toggle keyword active status.
     */
    public function toggleActive(EmailKeyword $keyword)
    {
        try {
            $keyword->update(['is_active' => ! $keyword->is_active]);
            $status = $keyword->is_active ? 'activated' : 'deactivated';

            return back()->with('success', "Keyword [{$keyword->key}] {$status} successfully!");
        } catch (\Exception $e) {
            Log::error('Failed to toggle keyword status', [
                'keyword_id' => $keyword->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to update keyword status.');
        }
    }

    /**
     * JSON list of active keywords for editor pickers (AJAX).
     */
    public function list()
    {
        return response()->json(
            EmailKeyword::active()->orderBy('key')->get()
                ->map(fn ($k) => [
                    'key' => $k->key,
                    'placeholder' => $k->placeholder,
                    'label' => $k->label ?: $k->key,
                    'source_column' => $k->source_column,
                    'default_value' => $k->default_value,
                ])->values()
        );
    }

    /**
     * Live preview: resolve template text with sample data or a real contact.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:20000',
            'contact_id' => 'nullable|integer',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($request->filled('contact_id')) {
            $row = \App\Models\EmailContact::where('user_id', auth()->id())
                ->find($request->contact_id);
            if (! $row) {
                return response()->json(['message' => 'Contact not found.'], 404);
            }
        } else {
            $row = [
                'first_name' => $request->input('first_name', 'John'),
                'last_name' => $request->input('last_name', 'Doe'),
                'email' => $request->input('email', 'john@example.com'),
                'company' => $request->input('company', 'Acme Inc'),
                'phone' => $request->input('phone', '+1234567890'),
                'notes' => $request->input('notes', ''),
            ];
            // Derive full_name the same way contacts do.
            $row['full_name'] = trim(($row['first_name'] ?? '').' '.($row['last_name'] ?? '')) ?: ($row['email'] ?? '');
        }

        $output = EmailKeyword::replaceIn($request->input('text'), $row);
        $active = EmailKeyword::active()->orderBy('key')->get()
            ->map(fn ($k) => ['placeholder' => $k->placeholder, 'value' => $k->resolveFor($row)])
            ->values();

        return response()->json(['output' => $output, 'resolved' => $active]);
    }
}
