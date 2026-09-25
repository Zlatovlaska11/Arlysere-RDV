<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\CustomFieldOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomFieldController extends Controller
{
    public function index(): View
    {
        $fields = CustomField::with('options')->orderBy('sort_order')->get();

        return view('admin.custom-fields.index', compact('fields'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'label'       => ['required', 'string', 'max:255'],
            'type'        => ['required', 'in:list,open,yesno'],
            'is_optional' => ['boolean'],
        ]);

        $maxOrder = CustomField::max('sort_order') ?? 0;

        CustomField::create([
            'label'       => $validated['label'],
            'type'        => $validated['type'],
            'is_optional' => $request->boolean('is_optional', true),
            'sort_order'  => $maxOrder + 1,
        ]);

        return redirect()->route('admin.custom-fields.index')->with('success', 'Champ créé.');
    }

    public function update(Request $request, CustomField $customField): RedirectResponse
    {
        $validated = $request->validate([
            'label'       => ['required', 'string', 'max:255'],
            'is_optional' => ['boolean'],
            'is_active'   => ['boolean'],
        ]);

        $customField->update([
            'label'       => $validated['label'],
            'is_optional' => $request->boolean('is_optional', true),
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.custom-fields.index')->with('success', 'Champ mis à jour.');
    }

    public function destroy(CustomField $customField): RedirectResponse
    {
        $customField->delete();

        return redirect()->route('admin.custom-fields.index')->with('success', 'Champ supprimé.');
    }

    public function storeOption(Request $request, CustomField $customField): RedirectResponse
    {
        $validated = $request->validate(['value' => ['required', 'string', 'max:255']]);

        $maxOrder = $customField->options()->max('sort_order') ?? 0;

        $customField->options()->create([
            'value'      => $validated['value'],
            'sort_order' => $maxOrder + 1,
        ]);

        return redirect()->route('admin.custom-fields.index')->with('success', 'Option ajoutée.');
    }

    public function destroyOption(CustomField $customField, CustomFieldOption $option): RedirectResponse
    {
        $option->delete();

        return redirect()->route('admin.custom-fields.index')->with('success', 'Option supprimée.');
    }
}
