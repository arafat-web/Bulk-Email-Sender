<?php

namespace App\Http\Controllers;

use App\Models\EmailContact;
use App\Models\ContactTag;
use App\Imports\ContactsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

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
        $tags = ContactTag::where('user_id', Auth::id())->get();

        return view('contacts.index', compact('contacts', 'tags'));
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
            'tags.*' => 'exists:contact_tags,id'
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
            'user_id' => Auth::id()
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
            'email' => 'required|email|unique:email_contacts,email,' . $contact->id,
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,bounced,unsubscribed',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:contact_tags,id'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $contact->update($request->only([
            'email', 'first_name', 'last_name', 'phone', 'company', 'notes', 'status'
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
            'tags.*' => 'exists:contact_tags,id'
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
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Bulk actions for contacts.
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:delete,add_tag,remove_tag,change_status',
            'contacts' => 'required|array',
            'contacts.*' => 'exists:email_contacts,id',
            'tag_id' => 'required_if:action,add_tag,remove_tag|exists:contact_tags,id',
            'status' => 'required_if:action,change_status|in:active,inactive,bounced,unsubscribed'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $contacts = EmailContact::whereIn('id', $request->contacts)
            ->where('user_id', Auth::id())
            ->get();

        switch ($request->action) {
            case 'delete':
                $count = $contacts->count();
                EmailContact::whereIn('id', $request->contacts)->delete();
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
                EmailContact::whereIn('id', $request->contacts)
                    ->update(['status' => $request->status]);
                return back()->with('success', 'Status updated for selected contacts.');
        }
    }
}
