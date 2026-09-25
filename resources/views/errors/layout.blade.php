<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $code }} — Arlysere</title>
    <link rel="icon" type="image/png" href="/favicon.png?v=2">
    <meta name="theme-color" content="#4f46e5">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-6 font-sans">
    <div class="text-center max-w-md">
        <div class="text-8xl font-black text-indigo-100 leading-none select-none mb-2">{{ $code }}</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $title }}</h1>
        <p class="text-gray-500 mb-8">{{ $message }}</p>
        <div class="flex gap-3 justify-center">
            <a href="javascript:history.back()"
                class="px-5 py-2.5 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                ← Retour
            </a>
            <a href="/"
                class="px-5 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition-colors shadow-sm">
                Accueil
            </a>
        </div>
    </div>
    <p class="mt-12 text-xs text-gray-300 tracking-widest uppercase">Arlysere</p>
</body>
</html>
