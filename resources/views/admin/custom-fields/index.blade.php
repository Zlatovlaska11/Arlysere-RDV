<x-layouts.app title="Champs personnalisés — Admin">
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-900">Champs personnalisés</h1>
        <a href="{{ route('admin.options.index') }}" class="text-sm text-indigo-600 hover:underline">← Options</a>
    </div>

    {{-- Add field form --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h2 class="font-semibold text-gray-800 mb-4">Nouveau champ</h2>
        <form method="POST" action="{{ route('admin.custom-fields.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Libellé <span class="text-red-500">*</span></label>
                    <input type="text" name="label" value="{{ old('label') }}" required placeholder="ex: Orientation, Remarques…"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    @error('label') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">— choisir —</option>
                        <option value="list" {{ old('type') === 'list' ? 'selected' : '' }}>Liste (choix prédéfinis)</option>
                        <option value="open" {{ old('type') === 'open' ? 'selected' : '' }}>Texte libre</option>
                        <option value="yesno" {{ old('type') === 'yesno' ? 'selected' : '' }}>Oui / Non</option>
                    </select>
                    @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex items-center gap-2">
                <input type="hidden" name="is_optional" value="0">
                <input type="checkbox" name="is_optional" value="1" id="is_optional" {{ old('is_optional', '1') ? 'checked' : '' }}
                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                <label for="is_optional" class="text-sm text-gray-700">Champ optionnel</label>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition-colors">
                    Ajouter le champ
                </button>
            </div>
        </form>
    </div>

    {{-- Existing fields --}}
    @forelse($fields as $field)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-4">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <form method="POST" action="{{ route('admin.custom-fields.update', $field) }}" class="space-y-3">
                        @csrf @method('PUT')
                        <div class="flex flex-wrap items-center gap-3">
                            <input type="text" name="label" value="{{ $field->label }}" required
                                class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-400 flex-1 min-w-0">
                            <span class="text-xs font-medium px-2 py-1 rounded-full
                                {{ $field->type === 'list' ? 'bg-indigo-100 text-indigo-700' : ($field->type === 'yesno' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600') }}">
                                {{ $field->typeLabel() }}
                            </span>
                            @if(!$field->is_optional)
                                <span class="text-xs font-medium px-2 py-1 rounded-full bg-red-100 text-red-700">Obligatoire</span>
                            @endif
                            @if(!$field->is_active)
                                <span class="text-xs font-medium px-2 py-1 rounded-full bg-gray-100 text-gray-500">Inactif</span>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-4">
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="hidden" name="is_optional" value="0">
                                <input type="checkbox" name="is_optional" value="1" {{ $field->is_optional ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                Optionnel
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" {{ $field->is_active ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                Actif
                            </label>
                            <button type="submit" class="ml-auto text-xs px-3 py-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                                Sauvegarder
                            </button>
                        </div>
                    </form>
                </div>
                <form method="POST" action="{{ route('admin.custom-fields.destroy', $field) }}"
                    onsubmit="return confirm('Supprimer ce champ et toutes ses données ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-600 text-sm px-2 py-1 transition-colors">✕</button>
                </form>
            </div>

            {{-- Options (list type only) --}}
            @if($field->type === 'list')
                <div class="border-t border-gray-100 pt-4 space-y-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Options de la liste</p>
                    <div class="flex flex-wrap gap-2">
                        @forelse($field->options as $option)
                            <div class="flex items-center gap-1 px-2.5 py-1 rounded-full border border-gray-200 text-sm bg-gray-50">
                                <span>{{ $option->value }}</span>
                                <form method="POST" action="{{ route('admin.custom-fields.options.destroy', [$field, $option]) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500 ml-1 text-xs leading-none transition-colors">✕</button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 italic">Aucune option — ajoutez-en ci-dessous.</p>
                        @endforelse
                    </div>
                    <form method="POST" action="{{ route('admin.custom-fields.options.store', $field) }}" class="flex gap-2">
                        @csrf
                        <input type="text" name="value" placeholder="Nouvelle option…" required
                            class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm flex-1 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-medium hover:bg-indigo-700 transition-colors">
                            Ajouter
                        </button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 text-center text-gray-400 text-sm">
            Aucun champ personnalisé pour l'instant. Créez-en un ci-dessus.
        </div>
    @endforelse
</div>
</x-layouts.app>
