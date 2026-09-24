<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Arlysere' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 font-sans">

<nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <div class="flex justify-between items-center h-14">
            <div class="flex items-center gap-6">
                <a href="{{ route('entries.index') }}" class="text-lg font-semibold text-indigo-700 tracking-tight">
                    Arlysere
                </a>
                <div class="hidden sm:flex items-center gap-4 text-sm font-medium">
                    <a href="{{ route('entries.index') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Saisies</a>
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.options.index') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Admin</a>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="hidden sm:block text-sm text-gray-500">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-red-600 transition-colors">
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
        {{-- Mobile nav --}}
        <div class="sm:hidden pb-2 flex gap-4 text-sm font-medium">
            <a href="{{ route('entries.index') }}" class="text-gray-600 hover:text-indigo-600">Saisies</a>
            @if(auth()->user()->is_admin)
                <a href="{{ route('admin.options.index') }}" class="text-gray-600 hover:text-indigo-600">Admin</a>
                <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-indigo-600">Utilisateurs</a>
            @endif
        </div>
    </div>
</nav>

<main class="max-w-5xl mx-auto px-4 sm:px-6 py-6">

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-red-800 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if(session('reset_password'))
        @php $reset = session('reset_password'); @endphp
        <div class="mb-4 rounded-lg bg-yellow-50 border border-yellow-300 px-4 py-3 text-yellow-900 text-sm">
            <strong>Nouveau mot de passe pour {{ $reset['user'] }} :</strong>
            <code class="ml-2 font-mono bg-yellow-100 px-2 py-0.5 rounded">{{ $reset['password'] }}</code>
            <span class="ml-2 text-yellow-700">(à transmettre à l'utilisateur — visible une seule fois)</span>
        </div>
    @endif

    {{ $slot }}

</main>
</body>
</html>
