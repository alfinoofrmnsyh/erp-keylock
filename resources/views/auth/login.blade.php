<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - Keylock Indonesia</title>
    
    <!-- Favicon & PWA -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/keylock-logo.svg') }}">
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#253B82">

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        loginBlue: '#253B82',
                        loginOrange: '#EF6B25',
                        loginOrangeHover: '#DE5A14',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        /* Pastikan pas dengan layar desktop tanpa scrollbar */
        @media (min-width: 1024px) {
            html, body {
                height: 100vh;
                overflow: hidden;
            }
        }
    </style>
</head>
<body class="bg-white font-sans antialiased text-slate-800 selection:bg-amber-400 selection:text-slate-950 h-full w-full overflow-x-hidden" 
      x-data="{ showPassword: false }">

    <div class="min-h-screen lg:h-screen w-full flex flex-col lg:flex-row overflow-x-hidden lg:overflow-hidden">
        
        <!-- ========================================================================= -->
        <!-- LEFT PANEL: LOGO & 3D ISOMETRIC DASHBOARD ILLUSTRATION                    -->
        <!-- ========================================================================= -->
        <div class="w-full lg:w-[57%] xl:w-[58%] h-full bg-white flex flex-col justify-between p-6 lg:p-10 xl:p-12 relative shrink-0">
            
            <!-- Brand Header (Matching Image 2 top left branding) -->
            <div class="flex items-center space-x-3.5 shrink-0 z-10">
                <div class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/keylock-logo.svg') }}" alt="Keylock Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <span class="font-extrabold text-xl sm:text-2xl tracking-tight text-slate-900 block leading-tight">Keylock Indonesia</span>
                </div>
            </div>

            <!-- Isometric Dashboard Illustration (Image 3) -->
            <div class="flex-1 flex items-center justify-center py-4 my-auto min-h-0 overflow-hidden">
                <img src="{{ asset('images/login-illustration.png') }}" 
                     alt="Keylock Mini-ERP Dashboard Illustration" 
                     class="max-h-[60vh] xl:max-h-[66vh] w-auto max-w-[92%] object-contain select-none pointer-events-none drop-shadow-sm">
            </div>

            <!-- Left Footer Note -->
            <div class="text-xs text-slate-400 font-medium hidden lg:block shrink-0 z-10">
                &copy; {{ date('Y') }} PT Keylock Indonesia &bull; Mini-ERP & Operational Management
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- RIGHT PANEL: ROYAL BLUE LOGIN FORM (MATCHING IMAGE 2 EXACTLY)             -->
        <!-- ========================================================================= -->
        <div class="w-full lg:w-[43%] xl:w-[42%] h-full bg-[#253B82] flex flex-col justify-between p-6 sm:p-10 lg:p-12 xl:p-14 text-white relative overflow-y-auto lg:overflow-hidden shrink-0 shadow-2xl">
            
            <!-- Decorative Subtle Light Ring Accent -->
            <div class="absolute -top-32 -right-32 w-80 h-80 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>

            <div class="my-auto max-w-md w-full mx-auto space-y-5 relative z-10 py-4">
                
                <!-- Greetings Typography (Image 2) -->
                <div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-none">
                        Hello!
                    </h1>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-none mt-2">
                        Welcome back!
                    </h2>
                    <p class="text-white/80 font-medium text-sm sm:text-base mt-2.5">
                        Let’s Login to Your Account
                    </p>
                </div>

                <!-- Session Flash Message -->
                @if (session('status'))
                    <div class="p-3 rounded-2xl bg-emerald-500/20 border border-emerald-400/50 text-emerald-100 text-xs font-semibold flex items-center space-x-2">
                        <svg class="w-4 h-4 text-emerald-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="p-3 rounded-2xl bg-rose-500/20 border border-rose-400/50 text-rose-100 text-xs font-semibold space-y-1">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-rose-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ $error }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Username / Email Field with Gold/Amber Rounded Pill Border (Image 2) -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-white mb-1.5">Username</label>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email', 'admin@keylock.co.id') }}" 
                               required 
                               autofocus 
                               autocomplete="username"
                               placeholder="admin@keylock.co.id"
                               class="w-full bg-white text-slate-900 text-sm font-medium rounded-2xl sm:rounded-[22px] border-[2.5px] border-[#F59E0B] px-5 py-3 placeholder-slate-400 outline-none focus:ring-4 focus:ring-amber-400/30 transition shadow-inner">
                    </div>

                    <!-- Password Field with Gold/Amber Rounded Pill Border (Image 2) -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-white mb-1.5">Password</label>
                        <div class="relative">
                            <input id="password" 
                                   :type="showPassword ? 'text' : 'password'" 
                                   name="password" 
                                   value="password" 
                                   required 
                                   autocomplete="current-password"
                                   placeholder="••••••••"
                                   class="w-full bg-white text-slate-900 text-sm font-medium rounded-2xl sm:rounded-[22px] border-[2.5px] border-[#F59E0B] px-5 py-3 pr-12 placeholder-slate-400 outline-none focus:ring-4 focus:ring-amber-400/30 transition shadow-inner">
                            
                            <button type="button" 
                                    @click="showPassword = !showPassword" 
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition p-1"
                                    title="Tampilkan / Sembunyikan Password">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me (Lupa kata sandi dihapus) -->
                    <div class="flex items-center text-xs pt-0.5">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                            <input id="remember_me" type="checkbox" name="remember" checked
                                   class="rounded border-white/40 text-amber-500 focus:ring-amber-400 bg-white/20">
                            <span class="ml-2 text-white/90 font-medium">Ingat sesi saya</span>
                        </label>
                    </div>

                    <!-- Vibrant Orange Pill Login Button (Matching Image 2) -->
                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full bg-[#EF6B25] hover:bg-[#DE5A14] active:scale-[0.99] text-white font-extrabold py-3.5 px-6 rounded-2xl sm:rounded-[22px] shadow-lg shadow-orange-950/25 text-base sm:text-lg transition-all duration-150 flex items-center justify-center space-x-2 tracking-wide">
                            <span>Login</span>
                        </button>
                    </div>
                </form>

                <!-- Sign In helper note (Matching Image 2) -->
                <div class="text-center text-xs text-white/70 pt-1">
                    Don’t have an account? 
                    <button type="button" onclick="alert('Pendaftaran akun dikelola secara terpusat oleh Admin IT PT Keylock Indonesia.')" 
                            class="text-white font-bold underline underline-offset-2 hover:text-amber-300 transition ml-1">
                        Sign In now
                    </button>
                </div>

            </div>

            <!-- Footer Links (Matching Image 2: Privacy Policy & Cookies Settings) -->
            <div class="pt-4 flex items-center justify-between text-xs text-white/50 relative z-10 shrink-0">
                <a href="javascript:void(0)" onclick="alert('Kebijakan Privasi PT Keylock Indonesia.')" class="hover:text-white transition">
                    Privacy Policy
                </a>
                <a href="javascript:void(0)" onclick="alert('Pengaturan Cookie Sistem Keylock.')" class="hover:text-white transition">
                    Cookies Settings
                </a>
            </div>

        </div>

    </div>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator && (window.location.protocol === 'https:' || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1')) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js', { scope: '/' })
                    .then(function(reg) {
                        // Registration successful
                    })
                    .catch(function(err) {
                        console.debug('[PWA] ServiceWorker registration note:', err.message);
                    });
            });
        }
    </script>
</body>
</html>
