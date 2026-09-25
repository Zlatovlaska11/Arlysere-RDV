<?php

namespace App\Http\Controllers;

use App\Exports\EntriesExport;
use App\Models\CustomField;
use App\Models\Entry;
use App\Models\EntryCustomValue;
use App\Models\EntryPerson;
use App\Models\Option;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class EntryController extends Controller
{
    public function index(Request $request): View
    {
        $query = auth()->user()->is_admin
            ? Entry::with(['user', 'theme', 'location', 'commune'])
            : auth()->user()->entries()->with(['theme', 'location', 'commune']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('theme_id')) {
            $query->where('theme_id', $request->theme_id);
        }

        if ($request->filled('commune_id')) {
            $query->where('commune_id', $request->commune_id);
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        $entries = $query->orderByDesc('date')->orderByDesc('created_at')->paginate(20)->withQueryString();

        $themes = Option::themes()->active()->get();
        $communes = Option::communes()->active()->get();

        return view('entries.index', compact('entries', 'themes', 'communes'));
    }

    public function create(): View
    {
        $options = $this->getFormOptions();

        return view('entries.create', $options);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateEntry($request);

        $entry = auth()->user()->entries()->create($validated);

        if ($entry->type === 'collective') {
            $this->savePersons($entry, $request->input('persons', []));
        }

        $this->saveCustomValues($entry, $request->input('custom', []));

        return redirect()->route('entries.index')->with('success', 'Saisie enregistrée avec succès.');
    }

    public function edit(int $id): View
    {
        $entry = $this->findOwnedEntry($id);
        $options = $this->getFormOptions();

        return view('entries.edit', array_merge($options, ['entry' => $entry]));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $entry = $this->findOwnedEntry($id);
        $validated = $this->validateEntry($request);

        $entry->update($validated);

        if ($entry->type === 'collective') {
            $entry->persons()->delete();
            $this->savePersons($entry, $request->input('persons', []));
        } else {
            $entry->persons()->delete();
        }

        $entry->customValues()->delete();
        $this->saveCustomValues($entry, $request->input('custom', []));

        return redirect()->route('entries.index')->with('success', 'Saisie mise à jour avec succès.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $entry = $this->findOwnedEntry($id);
        $entry->delete();

        return redirect()->route('entries.index')->with('success', 'Saisie supprimée.');
    }

    public function quickAddOption(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:location,commune,theme,difficulty,statut'],
            'value' => ['required', 'string', 'max:255'],
        ]);

        $option = Option::create([
            'type' => $validated['type'],
            'value' => $validated['value'],
            'is_active' => true,
            'sort_order' => 0,
        ]);

        return response()->json(['id' => $option->id, 'value' => $option->value]);
    }

    public function export(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filters = $request->only(['type', 'theme_id', 'commune_id', 'date_from', 'date_to']);

        return Excel::download(new EntriesExport($filters, auth()->user()), 'saisies-arlysere.xlsx');
    }

    private function findOwnedEntry(int $id): Entry
    {
        $entry = Entry::with(['persons', 'customValues'])->findOrFail($id);

        if (!auth()->user()->is_admin && $entry->user_id !== auth()->id()) {
            abort(403);
        }

        return $entry;
    }

    private function validateEntry(Request $request): array
    {
        $rules = [
            'date' => ['required', 'date'],
            'type' => ['required', 'in:rdv,collective,evenement'],
            'count' => ['required', 'integer', 'min:1'],
            'was_here_before' => ['boolean'],
            'duration' => ['nullable', 'integer', 'min:1'],
            'location_id' => ['nullable', 'exists:options,id'],
            'commune_id' => ['nullable', 'exists:options,id'],
            'material' => ['nullable', 'in:notebook,phone,tablet,mix'],
            'theme_id' => ['nullable', 'exists:options,id'],
            'difficulty_id' => ['nullable', 'exists:options,id'],
        ];

        if ($request->input('type') !== 'collective') {
            $rules['gender'] = ['nullable', 'in:homme,femme,autre'];
            $rules['age_id'] = ['nullable', 'exists:options,id'];
            $rules['statut_id'] = ['nullable', 'exists:options,id'];
        } else {
            $rules['persons'] = ['array'];
            $rules['persons.*.gender'] = ['required', 'in:homme,femme,autre'];
            $rules['persons.*.age_id'] = ['nullable', 'exists:options,id'];
            $rules['persons.*.statut_id'] = ['nullable', 'exists:options,id'];
        }

        $validated = $request->validate($rules);
        $validated['was_here_before'] = $request->boolean('was_here_before');

        if ($validated['type'] === 'collective') {
            unset($validated['gender'], $validated['age_id'], $validated['statut_id']);
        }

        unset($validated['persons']);

        return $validated;
    }

    /** @param array<int, array<string, mixed>> $persons */
    private function savePersons(Entry $entry, array $persons): void
    {
        foreach ($persons as $person) {
            EntryPerson::create([
                'entry_id' => $entry->id,
                'gender' => $person['gender'] ?? 'autre',
                'was_here_before' => ($person['was_here_before'] ?? '0') === '1',
                'age_id' => $person['age_id'] ?? null,
                'statut_id' => $person['statut_id'] ?? null,
            ]);
        }
    }

    /** @param array<string, string> $values */
    private function saveCustomValues(Entry $entry, array $values): void
    {
        foreach ($values as $fieldId => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            EntryCustomValue::create([
                'entry_id'        => $entry->id,
                'custom_field_id' => (int) $fieldId,
                'value'           => (string) $value,
            ]);
        }
    }

    /** @return array<string, mixed> */
    private function getFormOptions(): array
    {
        return [
            'locations'    => Option::locations()->active()->get(),
            'communes'     => Option::communes()->active()->get(),
            'themes'       => Option::themes()->active()->get(),
            'difficulties' => Option::difficulties()->active()->get(),
            'ages'         => Option::ages()->active()->get(),
            'statuts'      => Option::statuts()->active()->get(),
            'customFields' => CustomField::with('options')->active()->get(),
        ];
    }
}
