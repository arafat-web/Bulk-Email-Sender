<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates = EmailTemplate::recent()->get();
        $stats = [
            'total' => EmailTemplate::count(),
            'active' => EmailTemplate::active()->count(),
            'total_usage' => EmailTemplate::sum('usage_count'),
        ];

        return view('email-templates.index', compact('templates', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('email-templates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:email_templates,name',
            'subject' => 'required|string|max:500',
            'body' => 'required|string',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Please enter a template name.',
            'name.unique' => 'A template with this name already exists.',
            'subject.required' => 'Please enter an email subject.',
            'body.required' => 'Please enter the email content.',
        ]);

        try {
            EmailTemplate::create([
                'name' => $request->name,
                'subject' => $request->subject,
                'body' => $request->body,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('email-templates.index')
                ->with('success', 'Email template created successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to create email template', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return back()->withInput()
                ->with('error', 'Failed to create template. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailTemplate $emailTemplate)
    {
        return view('email-templates.show', compact('emailTemplate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        return view('email-templates.edit', compact('emailTemplate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:email_templates,name,' . $emailTemplate->id,
            'subject' => 'required|string|max:500',
            'body' => 'required|string',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $emailTemplate->update([
                'name' => $request->name,
                'subject' => $request->subject,
                'body' => $request->body,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('email-templates.index')
                ->with('success', 'Email template updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update email template', [
                'template_id' => $emailTemplate->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return back()->withInput()
                ->with('error', 'Failed to update template. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailTemplate $emailTemplate)
    {
        try {
            $templateName = $emailTemplate->name;
            $emailTemplate->delete();

            return redirect()->route('email-templates.index')
                ->with('success', "Template '{$templateName}' deleted successfully!");

        } catch (\Exception $e) {
            Log::error('Failed to delete email template', [
                'template_id' => $emailTemplate->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return back()->with('error', 'Failed to delete template. Please try again.');
        }
    }

    /**
     * Toggle template active status
     */
    public function toggleActive(EmailTemplate $emailTemplate)
    {
        try {
            $emailTemplate->update(['is_active' => !$emailTemplate->is_active]);

            $status = $emailTemplate->is_active ? 'activated' : 'deactivated';
            return back()->with('success', "Template '{$emailTemplate->name}' {$status} successfully!");

        } catch (\Exception $e) {
            Log::error('Failed to toggle template status', [
                'template_id' => $emailTemplate->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return back()->with('error', 'Failed to update template status.');
        }
    }

    /**
     * Duplicate a template
     */
    public function duplicate(EmailTemplate $emailTemplate)
    {
        try {
            $newTemplate = $emailTemplate->replicate();
            $newTemplate->name = $emailTemplate->name . ' (Copy)';
            $newTemplate->usage_count = 0;
            $newTemplate->last_used_at = null;
            $newTemplate->save();

            return redirect()->route('email-templates.edit', $newTemplate)
                ->with('success', "Template duplicated successfully! You can now edit the copy.");

        } catch (\Exception $e) {
            Log::error('Failed to duplicate email template', [
                'template_id' => $emailTemplate->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return back()->with('error', 'Failed to duplicate template.');
        }
    }

    /**
     * Get template data for AJAX requests
     */
    public function getTemplate(EmailTemplate $emailTemplate)
    {
        if (!$emailTemplate->is_active) {
            return response()->json(['error' => 'Template is not active'], 400);
        }

        return response()->json([
            'id' => $emailTemplate->id,
            'name' => $emailTemplate->name,
            'subject' => $emailTemplate->subject,
            'body' => $emailTemplate->body,
        ]);
    }
}
