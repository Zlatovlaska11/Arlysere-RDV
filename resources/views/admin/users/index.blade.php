<x-layouts.app title="Administration — Utilisateurs">

<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestion des utilisateurs</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $users->count() }} utilisateur(s) au total</p>
        </div>
        <a href="{{ route('admin.options.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">← Retour aux options</a>
    </div>

    {{-- Users table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-left">
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Nom</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Rôle</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Créé le</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $user->name }}
                                @if($user->id === auth()->id())
                                    <span class="ml-1 text-xs text-gray-400">(vous)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                @if($user->is_admin)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Admin</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Utilisateur</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap space-x-2">
                                <button
                                    type="button"
                                    onclick="openEditUserModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', {{ $user->is_admin ? 'true' : 'false' }})"
                                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium"
                                >Modifier</button>

                                <form method="POST" action="{{ route('admin.users.reset-password', $user->id) }}" class="inline"
                                    onsubmit="return confirm('Générer un nouveau mot de passe pour {{ addslashes($user->name) }} ?')">
                                    @csrf
                                    <button type="submit" class="text-xs text-orange-600 hover:text-orange-800 font-medium">Réinitialiser MDP</button>
                                </form>

                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline"
                                        onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Supprimer</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create user form --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="font-semibold text-gray-800 mb-4">Créer un utilisateur</h2>

        @if($errors->create->any())
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-red-700 text-sm space-y-1">
                @foreach($errors->create->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe <span class="text-red-500">*</span></label>
                <input type="password" name="password" required minlength="8"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="flex items-end gap-3">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_admin" id="create-is-admin" value="1" class="rounded border-gray-300 text-indigo-600">
                    <label for="create-is-admin" class="text-sm text-gray-700">Rôle administrateur</label>
                </div>
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg px-5 py-2.5 transition-colors">
                    Créer l'utilisateur
                </button>
            </div>
        </form>
    </div>

</div>

{{-- Edit user modal --}}
<div id="edit-user-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4">
        <h3 class="font-semibold text-gray-900 text-lg">Modifier l'utilisateur</h3>
        <form id="edit-user-form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                <input type="text" name="name" id="edit-user-name" required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="edit-user-email" required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_admin" id="edit-is-admin" value="1" class="rounded border-gray-300 text-indigo-600">
                <label for="edit-is-admin" class="text-sm text-gray-700">Rôle administrateur</label>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="document.getElementById('edit-user-modal').classList.add('hidden')"
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
function openEditUserModal(id, name, email, isAdmin) {
    document.getElementById('edit-user-form').action = '/admin/users/' + id;
    document.getElementById('edit-user-name').value = name;
    document.getElementById('edit-user-email').value = email;
    document.getElementById('edit-is-admin').checked = isAdmin;
    document.getElementById('edit-user-modal').classList.remove('hidden');
}
</script>

</x-layouts.app>
