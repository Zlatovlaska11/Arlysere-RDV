<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OptionController extends Controller
{
    /** @var array<string, string> */
    public const TYPES = [
        'location' => 'Lieux',
        'commune' => 'Communes',
        'theme' => 'Thèmes',
        'difficulty' => 'Difficultés',
        'age' => 'Tranches d\'âge',
        'statut' => 'Statuts',
    ];

    public function index(): View
    {
        $optionsByType = [];

        foreach (self::TYPES as $type => $label) {
            $optionsByType[$type] = [
                'label' => $label,
                'options' => Option::ofType($type)->get(),
            ];
        }

        return view('admin.options.index', compact('optionsByType'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:' . implode(',', array_keys(self::TYPES))],
            'value' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        Option::create($validated + ['is_active' => true]);

        return back()->with('success', 'Option ajoutée avec succès.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $option = Option::findOrFail($id);

        $validated = $request->validate([
            'value' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $option->update($validated);

        return back()->with('success', 'Option mise à jour.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $option = Option::findOrFail($id);
        $option->update(['is_active' => false]);

        return back()->with('success', 'Option désactivée.');
    }
}
