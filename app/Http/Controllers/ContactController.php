<?php

namespace App\Http\Controllers;

use App\Imports\ContactsImport;
use App\Models\ContactTag;
use App\Models\EmailContact;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ContactController extends Controller
{
    /**
     * Display a listing of contacts.
     */
    public function index(Request $request)
    {
        $query = EmailContact::where('user_id', Auth::id())
            ->with('tags')
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Apply tag filter
        if ($request->filled('tag')) {
            $query->withTag($request->tag);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $contacts = $query->paginate(20);
        $filteredCount = (clone $query)->count();
        $tags = ContactTag::where('user_id', Auth::id())->get();

        return view('contacts.index', compact('contacts', 'tags', 'filteredCount'));
    }

    /**
     * Show the form for creating a new contact.
     */
    public function create()
    {
        $tags = ContactTag::where('user_id', Auth::id())->get();

        return view('contacts.create', compact('tags'));
    }

    /**
     * Store a newly created contact in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:email_contacts,email',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:contact_tags,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $contact = EmailContact::create([
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'company' => $request->company,
            'notes' => $request->notes,
            'user_id' => Auth::id(),
        ]);

        if ($request->filled('tags')) {
            $contact->tags()->sync($request->tags);
        }

        return redirect()->route('contacts.index')->with('success', 'Contact created successfully.');
    }

    /**
     * Show the specified contact.
     */
    public function show(EmailContact $contact)
    {
        $this->authorize('view', $contact);

        return view('contacts.show', compact('contact'));
    }

    /**
     * Show the form for editing the specified contact.
     */
    public function edit(EmailContact $contact)
    {
        $this->authorize('update', $contact);
        $tags = ContactTag::where('user_id', Auth::id())->get();

        return view('contacts.edit', compact('contact', 'tags'));
    }

    /**
     * Update the specified contact in storage.
     */
    public function update(Request $request, EmailContact $contact)
    {
        $this->authorize('update', $contact);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:email_contacts,email,'.$contact->id,
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,bounced,unsubscribed',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:contact_tags,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $contact->update($request->only([
            'email', 'first_name', 'last_name', 'phone', 'company', 'notes', 'status',
        ]));

        $contact->tags()->sync($request->tags ?? []);

        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
    }

    /**
     * Remove the specified contact from storage.
     */
    public function destroy(EmailContact $contact)
    {
        $this->authorize('delete', $contact);
        $contact->delete();

        return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
    }

    /**
     * Show the import form.
     */
    public function importForm()
    {
        $tags = ContactTag::where('user_id', Auth::id())->get();

        return view('contacts.import', compact('tags'));
    }

    /**
     * Import contacts from Excel/CSV file.
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
            'tags' => 'nullable|array',
            'tags.*' => 'exists:contact_tags,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $import = new ContactsImport(Auth::id(), $request->tags ?? []);
            Excel::import($import, $request->file('file'));

            $imported = $import->getImportedCount();
            $skipped = $import->getSkippedCount();

            $message = "Import completed! {$imported} contacts imported";
            if ($skipped > 0) {
                $message .= ", {$skipped} skipped (duplicates or invalid)";
            }

            return redirect()->route('contacts.index')->with('success', $message);
        } catch (Exception $e) {
            return back()->with('error', 'Import failed: '.$e->getMessage());
        }
    }

    /**
     * Return filtered contact IDs as JSON (for select-all across pages). Capped to 10k.
     */
    public function ids(Request $request)
    {
        $query = EmailContact::where('user_id', Auth::id());
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('tag')) {
            $query->withTag($request->tag);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $total = (clone $query)->count();
        $ids = $query->limit(10000)->pluck('id');

        return response()->json(['ids' => $ids, 'count' => $ids->count(), 'total' => $total, 'capped' => $total > 10000]);
    }

    /**
     * Bulk actions for contacts. Supports select-all across pages via bulk_all_filtered flag.
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:delete,add_tag,remove_tag,change_status',
            'contacts' => 'nullable|array',
            'contacts.*' => 'exists:email_contacts,id',
            'bulk_all_filtered' => 'nullable|boolean',
            'bulk_search' => 'nullable|string|max:255',
            'bulk_tag' => 'nullable|exists:contact_tags,id',
            'bulk_status' => 'nullable|in:active,inactive,bounced,unsubscribed',
            'tag_id' => 'nullable|exists:contact_tags,id',
            'status' => 'nullable|in:active,inactive,bounced,unsubscribed',
        ]);

        // Custom required checks (cannot use required_if with scoped exists easily)
        if ($request->boolean('bulk_all_filtered')) {
            // when bulk_all_filtered, contacts may be empty; we resolve server-side
        } else {
            if (empty($request->contacts)) {
                return back()->withErrors(['contacts' => 'Please select at least one contact.']);
            }
        }
        if (in_array($request->action, ['add_tag', 'remove_tag']) && empty($request->tag_id)) {
            return back()->withErrors(['tag_id' => 'Please select a tag.']);
        }
        if ($request->action === 'change_status' && empty($request->status)) {
            return back()->withErrors(['status' => 'Please select a status.']);
        }

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Resolve target IDs: either posted contacts[] or all filtered (server-side) to avoid max_input_vars
        $targetIds = null;
        if ($request->boolean('bulk_all_filtered')) {
            $q = EmailContact::where('user_id', Auth::id());
            if ($request->filled('bulk_search')) {
                $q->search($request->bulk_search);
            }
            if ($request->filled('bulk_tag')) {
                $q->withTag($request->bulk_tag);
            }
            if ($request->filled('bulk_status')) {
                $q->where('status', $request->bulk_status);
            }
            $targetIds = $q->limit(10000)->pluck('id');
            if ($targetIds->isEmpty()) {
                return back()->with('error', 'No contacts match the current filters.');
            }
        } else {
            $targetIds = collect($request->contacts ?? []);
        }

        // Ensure tag ownership if tagging
        if (in_array($request->action, ['add_tag', 'remove_tag']) && $request->filled('tag_id')) {
            $ownsTag = ContactTag::where('id', $request->tag_id)->where('user_id', Auth::id())->exists();
            if (! $ownsTag) {
                return back()->withErrors(['tag_id' => 'Invalid tag selected.']);
            }
        }

        $contacts = EmailContact::whereIn('id', $targetIds)
            ->where('user_id', Auth::id())
            ->get();

        if ($contacts->isEmpty()) {
            return back()->with('error', 'No contacts found for this selection.');
        }

        switch ($request->action) {
            case 'delete':
                $count = $contacts->count();
                EmailContact::whereIn('id', $contacts->pluck('id'))
                    ->where('user_id', Auth::id())
                    ->delete();

                return back()->with('success', "{$count} contacts deleted successfully.");

            case 'add_tag':
                foreach ($contacts as $contact) {
                    $contact->tags()->syncWithoutDetaching([$request->tag_id]);
                }

                return back()->with('success', 'Tag added to selected contacts.');

            case 'remove_tag':
                foreach ($contacts as $contact) {
                    $contact->tags()->detach($request->tag_id);
                }

                return back()->with('success', 'Tag removed from selected contacts.');

            case 'change_status':
                EmailContact::whereIn('id', $contacts->pluck('id'))
                    ->where('user_id', Auth::id())
                    ->update(['status' => $request->status]);

                return back()->with('success', 'Status updated for selected contacts.');
        }
    }
}
