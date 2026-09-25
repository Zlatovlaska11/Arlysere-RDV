<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Arlysere' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/favicon.png?v=2">
    <link rel="apple-touch-icon" href="/favicon.png?v=2">
    <meta name="theme-color" content="#4f46e5">
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
                        <a href="{{ route('admin.options.index') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Options</a>
                        <a href="{{ route('admin.custom-fields.index') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Champs</a>
                        <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">Utilisateurs</a>
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

<script>
(function() {
    window.bomboclatActive = false;

    window.activateBomboclat = function activateBomboclat() {
        if (window.bomboclatActive) return;
        window.bomboclatActive = true;

        // Background
        document.body.style.backgroundImage = "url('/bomboclat-bg.jpg')";
        document.body.style.backgroundSize = 'cover';
        document.body.style.backgroundAttachment = 'fixed';
        document.body.style.backgroundPosition = 'center';


        // Nav: Jamaican stripe
        var nav = document.querySelector('nav');
        nav.style.background = 'linear-gradient(90deg, #000 0%, #1a1a1a 100%)';
        nav.style.borderBottom = '3px solid #ffd700';

        // Brand name → Arlysere but gold
        var brand = nav.querySelector('a');
        if (brand) {
            brand.style.color = '#ffd700';
            brand.style.textShadow = '0 0 8px #ff6600';
            brand.style.letterSpacing = '0.05em';
        }

        // All cards get semi-transparent dark with gold border (exclude buttons so chip selection still works)
        document.querySelectorAll('.bg-white:not(button)').forEach(function(el) {
            el.style.background = 'rgba(10,10,10,0.82)';
            el.style.border = '1.5px solid #ffd700';
            el.style.color = '#f5e642';
        });
        document.querySelectorAll('.text-gray-800:not(button),.text-gray-700:not(button),.text-gray-600:not(button),.text-gray-900:not(button)').forEach(function(el) {
            el.style.color = '#ffd700';
        });
        document.querySelectorAll('input,select,textarea').forEach(function(el) {
            el.style.background = '#111';
            el.style.color = '#ffd700';
            el.style.borderColor = '#ffd700';
        });

        // Toast
        var toast = document.createElement('div');
        toast.innerText = '🇯🇲 BOMBOCLAT MODE ACTIVATED MON 🌴';
        toast.style.cssText = 'position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;background:#ffd700;color:#000;font-weight:900;font-size:1.1rem;padding:12px 28px;border-radius:999px;box-shadow:0 4px 24px rgba(255,165,0,0.7);letter-spacing:0.05em;pointer-events:none;';
        document.body.appendChild(toast);
        setTimeout(function() { toast.style.transition='opacity 1s'; toast.style.opacity='0'; setTimeout(function(){toast.remove();},1000); }, 3500);
    }

    document.addEventListener('input', function(e) {
        if (e.target && e.target.value && e.target.value.toLowerCase().includes('bomboclat')) {
            window.activateBomboclat();
        }
    });
})();
</script>
</body>
</html>
