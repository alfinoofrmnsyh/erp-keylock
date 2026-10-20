<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>About Us - Keylock ID</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0a1424] text-white py-20 px-6">
    <div class="max-w-3xl mx-auto text-center">
        
        <span class="text-amber-400 text-sm font-semibold tracking-wider uppercase bg-amber-400/10 px-3 py-1 rounded-full">
            {{ $content['badge'] ?? 'ABOUT US' }}
        </span>
        
        <h1 class="text-4xl font-bold mt-4 mb-6 leading-tight">
            {{ $content['title'] ?? 'Profil PT Keylock Indonesia' }}
        </h1>
        
        <div class="text-slate-400 text-lg leading-relaxed text-justify">
            {!! $content['description'] ?? 'Kami adalah penyedia solusi logistik terpercaya...' !!}
        </div>

    </div>
</body>
</html>