<x-layouts.app title="Administration — Options">

<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestion des options</h1>
            <p class="text-sm text-gray-500 mt-0.5">Gérez les listes de choix disponibles dans les formulaires</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Gérer les utilisateurs →</a>
    </div>

    @foreach($optionsByType as $type => $group)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800">{{ $group['label'] }}</h2>
                <span class="text-xs text-gray-500">{{ $group['options']->count() }} option(s)</span>
            </div>

            <div class="divide-y divide-gray-50">
                @forelse($group['options'] as $option)
                    <div class="flex items-center gap-3 px-5 py-3">
                        @if($option->color)
                            <div class="w-4 h-4 rounded-full flex-shrink-0" style="background-color: {{ $option->color }}"></div>
                        @else
                            <div class="w-4 h-4 rounded-full bg-gray-200 flex-shrink-0"></div>
                        @endif

                        <span class="flex-1 text-sm font-medium {{ $option->is_active ? 'text-gray-900' : 'text-gray-400 line-through' }}">
                            {{ $option->value }}
                        </span>

                        <span class="text-xs text-gray-400">ordre: {{ $option->sort_order }}</span>

                        <button
                            type="button"
                            onclick="openEditModal({{ $option->id }}, '{{ addslashes($option->value) }}', '{{ $option->color ?? '' }}', {{ $option->sort_order }})"
                            class="text-xs text-indigo-600 hover:text-indigo-800 font-medium"
                        >Modifier</button>

                        @if($option->is_active)
                            <form method="POST" action="{{ route('admin.options.destroy', $option) }}" onsubmit="return confirm('Désactiver cette option ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Désactiver</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.options.update', $option) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="value" value="{{ $option->value }}">
                                <input type="hidden" name="color" value="{{ $option->color }}">
                                <input type="hidden" name="sort_order" value="{{ $option->sort_order }}">
                                <input type="hidden" name="is_active" value="1">
                                <button type="submit" class="text-xs text-green-600 hover:text-green-800 font-medium">Réactiver</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="px-5 py-4 text-sm text-gray-400 italic">Aucune option pour cette catégorie.</p>
                @endforelse
            </div>

            {{-- Add new option --}}
            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">
                <form method="POST" action="{{ route('admin.options.store') }}" class="flex flex-wrap gap-2 items-end">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Valeur</label>
                        <input type="text" name="value" required placeholder="Nouvelle option"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm w-48 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Couleur (hex)</label>
                        <div class="flex gap-1 items-center">
                            <input type="color" name="color_picker" class="h-9 w-10 rounded border border-gray-300 cursor-pointer p-0.5"
                                onchange="this.nextElementSibling.value = this.value">
                            <input type="text" name="color" placeholder="#3b82f6" pattern="^#[0-9a-fA-F]{6}$"
                                class="rounded-lg border border-gray-300 px-3 py-2 text-sm w-28 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Ordre</label>
                        <input type="number" name="sort_order" value="0" min="0"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm w-20 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>

                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg px-4 py-2 transition-colors">
                        + Ajouter
                    </button>
                </form>
            </div>
        </div>
    @endforeach

</div>

{{-- Edit modal --}}
<div id="edit-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4">
        <h3 class="font-semibold text-gray-900 text-lg">Modifier l'option</h3>
        <form id="edit-form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Valeur</label>
                <input type="text" name="value" id="edit-value" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Couleur</label>
                <div class="flex gap-2">
                    <input type="color" id="edit-color-picker" class="h-10 w-12 rounded border border-gray-300 cursor-pointer p-0.5"
                        oninput="document.getElementById('edit-color').value = this.value">
                    <input type="text" name="color" id="edit-color" placeholder="#3b82f6" pattern="^#[0-9a-fA-F]{6}$"
                        class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ordre d'affichage</label>
                <input type="number" name="sort_order" id="edit-sort" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="document.getElementById('edit-modal').classList.add('hidden')"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, value, color, sortOrder) {
    document.getElementById('edit-form').action = '/admin/options/' + id;
    document.getElementById('edit-value').value = value;
    document.getElementById('edit-color').value = color;
    document.getElementById('edit-color-picker').value = color || '#000000';
    document.getElementById('edit-sort').value = sortOrder;
    document.getElementById('edit-modal').classList.remove('hidden');
}
</script>

</x-layouts.app>
