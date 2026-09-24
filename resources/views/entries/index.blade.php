<x-layouts.app title="Saisies">

<div class="space-y-6">

    {{-- Header actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Saisies terrain</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $entries->total() }} entrée(s) au total</p>
        </div>
        <div class="flex gap-2">
            <a
                href="{{ route('entries.export', request()->query()) }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Exporter Excel
            </a>
            <a
                href="{{ route('entries.create') }}"
                class="inline-flex items-center gap-1.5 rounded-lg bg-green-600 hover:bg-green-700 text-white px-5 py-2 text-sm font-semibold transition-colors shadow-sm"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouvelle saisie
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <details class="bg-white rounded-xl border border-gray-200 shadow-sm" {{ request()->hasAny(['type','theme_id','commune_id','date_from','date_to']) ? 'open' : '' }}>
        <summary class="px-5 py-4 cursor-pointer text-sm font-medium text-gray-700 flex items-center gap-2 select-none">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
            Filtrer les résultats
        </summary>
        <form method="GET" action="{{ route('entries.index') }}" class="px-5 pb-5 pt-2 grid grid-cols-2 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                <select name="type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">Tous</option>
                    <option value="rdv" {{ request('type') === 'rdv' ? 'selected' : '' }}>RDV</option>
                    <option value="collective" {{ request('type') === 'collective' ? 'selected' : '' }}>Collectif</option>
                    <option value="evenement" {{ request('type') === 'evenement' ? 'selected' : '' }}>Événement</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Thème</label>
                <select name="theme_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">Tous</option>
                    @foreach($themes as $theme)
                        <option value="{{ $theme->id }}" {{ request('theme_id') == $theme->id ? 'selected' : '' }}>{{ $theme->value }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Commune</label>
                <select name="commune_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <option value="">Toutes</option>
                    @foreach($communes as $commune)
                        <option value="{{ $commune->id }}" {{ request('commune_id') == $commune->id ? 'selected' : '' }}>{{ $commune->value }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Du</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Au</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg px-4 py-2 transition-colors">Filtrer</button>
                <a href="{{ route('entries.index') }}" class="flex-1 text-center border border-gray-300 text-sm font-medium rounded-lg px-4 py-2 text-gray-600 hover:bg-gray-50 transition-colors">Réinitialiser</a>
            </div>
        </form>
    </details>

    {{-- Entries list --}}
    @if($entries->isEmpty())
        <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
            <svg class="mx-auto w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="text-gray-500 font-medium">Aucune saisie pour l'instant</p>
            <a href="{{ route('entries.create') }}" class="mt-4 inline-block text-indigo-600 text-sm hover:underline">Créer la première saisie</a>
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-left">
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Personnes</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Thème</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Lieu</th>
                            <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Durée</th>
                            @if(auth()->user()->is_admin)
                                <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Par</th>
                            @endif
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($entries as $entry)
                            @php
                                $typeColors = ['rdv' => 'bg-blue-100 text-blue-800', 'collective' => 'bg-purple-100 text-purple-800', 'evenement' => 'bg-orange-100 text-orange-800'];
                                $typeLabels = ['rdv' => 'RDV', 'collective' => 'Collectif', 'evenement' => 'Événement'];
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">{{ $entry->date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$entry->type] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $typeLabels[$entry->type] ?? $entry->type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $entry->count }}</td>
                                <td class="px-4 py-3">
                                    @if($entry->theme)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium text-white" style="background-color: {{ $entry->theme->color ?? '#6b7280' }}">
                                            {{ $entry->theme->value }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $entry->location?->value ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $entry->duration ? $entry->duration . ' min' : '—' }}</td>
                                @if(auth()->user()->is_admin)
                                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $entry->user?->name }}</td>
                                @endif
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('entries.edit', $entry) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium mr-3">Modifier</a>
                                    <form method="POST" action="{{ route('entries.destroy', $entry) }}" class="inline" onsubmit="return confirm('Supprimer cette saisie ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($entries->hasPages())
            <div class="flex justify-center">
                {{ $entries->links() }}
            </div>
        @endif
    @endif

</div>

</x-layouts.app>
