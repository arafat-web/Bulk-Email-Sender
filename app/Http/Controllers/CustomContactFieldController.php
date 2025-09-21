<?php

namespace App\Http\Controllers;

use App\Models\CustomContactField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomContactFieldController extends Controller
{
    /**
     * Display a listing of custom fields.
     */
    public function index()
    {
        $fields = CustomContactField::where('user_id', Auth::id())
            ->ordered()
            ->paginate(20);

        return view('custom-fields.index', compact('fields'));
    }

    /**
     * Show the form for creating a new custom field.
     */
    public function create()
    {
        return view('custom-fields.create');
    }

    /**
     * Store a newly created custom field.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,number,email,url,textarea,select,date',
            'description' => 'nullable|string|max:1000',
            'options' => 'required_if:type,select|array',
            'options.*' => 'required_with:options|string|max:255',
            'default_value' => 'nullable|string|max:255',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Generate field name from label
        $name = Str::slug($request->label, '_');
        
        // Ensure unique field name for this user
        $originalName = $name;
        $counter = 1;
        while (CustomContactField::where('user_id', Auth::id())->where('name', $name)->exists()) {
            $name = $originalName . '_' . $counter++;
        }

        // Process options for select type
        $options = null;
        if ($request->type === 'select' && $request->options) {
            $options = [];
            foreach ($request->options as $option) {
                if (!empty($option)) {
                    $optionKey = Str::slug($option, '_');
                    $options[$optionKey] = $option;
                }
            }
        }

        // Get the next sort order
        $sortOrder = CustomContactField::where('user_id', Auth::id())->max('sort_order') + 1;

        CustomContactField::create([
            'name' => $name,
            'label' => $request->label,
            'type' => $request->type,
            'description' => $request->description,
            'options' => $options,
            'default_value' => $request->default_value,
            'is_required' => $request->boolean('is_required'),
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $sortOrder,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('custom-fields.index')
            ->with('success', 'Custom field created successfully.');
    }

    /**
     * Show the form for editing a custom field.
     */
    public function edit(CustomContactField $customField)
    {
        $this->authorize('update', $customField);
        return view('custom-fields.edit', compact('customField'));
    }

    /**
     * Update the specified custom field.
     */
    public function update(Request $request, CustomContactField $customField)
    {
        $this->authorize('update', $customField);

        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,number,email,url,textarea,select,date',
            'description' => 'nullable|string|max:1000',
            'options' => 'required_if:type,select|array',
            'options.*' => 'required_with:options|string|max:255',
            'default_value' => 'nullable|string|max:255',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Process options for select type
        $options = null;
        if ($request->type === 'select' && $request->options) {
            $options = [];
            foreach ($request->options as $option) {
                if (!empty($option)) {
                    $optionKey = Str::slug($option, '_');
                    $options[$optionKey] = $option;
                }
            }
        }

        $customField->update([
            'label' => $request->label,
            'type' => $request->type,
            'description' => $request->description,
            'options' => $options,
            'default_value' => $request->default_value,
            'is_required' => $request->boolean('is_required'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('custom-fields.index')
            ->with('success', 'Custom field updated successfully.');
    }

    /**
     * Remove the specified custom field.
     */
    public function destroy(CustomContactField $customField)
    {
        $this->authorize('delete', $customField);
        
        $customField->delete();

        return redirect()->route('custom-fields.index')
            ->with('success', 'Custom field deleted successfully.');
    }

    /**
     * Update field sort order.
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fields' => 'required|array',
            'fields.*.id' => 'required|exists:custom_contact_fields,id',
            'fields.*.sort_order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        foreach ($request->fields as $fieldData) {
            CustomContactField::where('id', $fieldData['id'])
                ->where('user_id', Auth::id())
                ->update(['sort_order' => $fieldData['sort_order']]);
        }

        return response()->json(['success' => true]);
    }
}
