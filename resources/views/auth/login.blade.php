<x-guest-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Instrument+Serif&display=swap');

        .font-serif { font-family: 'Instrument Serif', serif; }

        .grid-overlay {
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* Word reveal animation */
        .word-reveal {
            opacity: 0;
            transform: translateY(20px);
            animation: wordReveal 0.7s ease-out forwards;
        }
        .word-reveal-1 { animation-delay: 0.1s; }
        .word-reveal-2 { animation-delay: 0.3s; }
        .word-reveal-3 { animation-delay: 0.5s; }

        @keyframes wordReveal {
            to { opacity: 1; transform: translateY(0); }
        }

        /* Fade up animation */
        .fade-up {
            opacity: 0;
            transform: translateY(12px);
            animation: fadeUp 0.6s ease-out forwards;
        }
        .fade-up-delay-1 { animation-delay: 0.15s; }
        .fade-up-delay-2 { animation-delay: 0.3s; }
        .fade-up-delay-3 { animation-delay: 0.45s; }
        .fade-up-delay-4 { animation-delay: 0.6s; }

        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }

        /* Floating label */
        .float-label input:focus ~ label,
        .float-label input:not(:placeholder-shown) ~ label {
            transform: translateY(-22px) scale(0.75);
            color: #64748b;
        }
        .float-label label {
            transition: all 0.2s ease;
            transform-origin: left top;
        }
    </style>

    <div class="flex min-h-screen">

        <!-- LEFT PANEL: Brand / Hero -->
        <div class="hidden lg:flex flex-1 relative overflow-hidden" style="background-color: #0E1520;">
            <!-- Grid overlay -->
            <div class="absolute inset-0 grid-overlay"></div>
            <!-- Radial blue glow -->
            <div class="absolute inset-0" style="background: radial-gradient(ellipse at 30% 80%, rgba(37,99,235,0.12) 0%, transparent 60%);"></div>

            <div class="relative z-10 flex flex-col justify-center max-w-lg mx-auto px-12 w-full">
                <!-- Headline -->
                <div>
                    <div class="text-5xl font-serif text-white word-reveal word-reveal-1">Operations.</div>
                    <div class="text-5xl font-serif text-white word-reveal word-reveal-2">Compliance.</div>
                    <div class="text-5xl font-serif word-reveal word-reveal-3" style="color: #F59E0B;">Cleared.</div>
                </div>

                <!-- Runway strip -->
                <div class="mt-12 flex justify-start">
                    <div class="relative" style="width: 280px; height: 72px;">
                        {{-- Outer glow --}}
                        <div class="absolute -inset-3 rounded-xl opacity-40" style="background: radial-gradient(ellipse, rgba(255,255,255,0.06) 0%, transparent 70%);"></div>

                        {{-- Asphalt surface --}}
                        <div class="absolute inset-0 rounded-md" style="background: linear-gradient(180deg, #14182a 0%, #1a1e35 50%, #14182a 100%); box-shadow: 0 0 20px rgba(0,0,0,0.4);"></div>

                        {{-- Edge lights — top --}}
                        <div class="absolute top-0 left-4 right-4 flex justify-between items-center h-[3px]">
                            <span class="block w-4 h-[2px] rounded-full" style="background: rgba(255,255,255,0.7); box-shadow: 0 0 6px rgba(255,255,255,0.4);"></span>
                            <span class="block w-3 h-[2px] rounded-full" style="background: rgba(255,255,255,0.4);"></span>
                            <span class="block w-3 h-[2px] rounded-full" style="background: rgba(255,255,255,0.5);"></span>
                            <span class="block w-3 h-[2px] rounded-full" style="background: rgba(255,255,255,0.4);"></span>
                            <span class="block w-3 h-[2px] rounded-full" style="background: rgba(255,255,255,0.5);"></span>
                            <span class="block w-3 h-[2px] rounded-full" style="background: rgba(255,255,255,0.4);"></span>
                            <span class="block w-4 h-[2px] rounded-full" style="background: rgba(255,255,255,0.7); box-shadow: 0 0 6px rgba(255,255,255,0.4);"></span>
                        </div>

                        {{-- Edge lights — bottom --}}
                        <div class="absolute bottom-0 left-4 right-4 flex justify-between items-center h-[3px]">
                            <span class="block w-4 h-[2px] rounded-full" style="background: rgba(255,255,255,0.7); box-shadow: 0 0 6px rgba(255,255,255,0.4);"></span>
                            <span class="block w-3 h-[2px] rounded-full" style="background: rgba(255,255,255,0.4);"></span>
                            <span class="block w-3 h-[2px] rounded-full" style="background: rgba(255,255,255,0.5);"></span>
                            <span class="block w-3 h-[2px] rounded-full" style="background: rgba(255,255,255,0.4);"></span>
                            <span class="block w-3 h-[2px] rounded-full" style="background: rgba(255,255,255,0.5);"></span>
                            <span class="block w-3 h-[2px] rounded-full" style="background: rgba(255,255,255,0.4);"></span>
                            <span class="block w-4 h-[2px] rounded-full" style="background: rgba(255,255,255,0.7); box-shadow: 0 0 6px rgba(255,255,255,0.4);"></span>
                        </div>

                        {{-- Dashed amber centerline --}}
                        <div class="absolute top-1/2 left-8 right-8 -translate-y-1/2 h-[2px] flex items-center justify-between gap-[6px]">
                            <span class="flex-1 h-[2px] rounded-full" style="background: #D97706; box-shadow: 0 0 4px rgba(217,119,6,0.5);"></span>
                            <span class="w-2"></span>
                            <span class="flex-1 h-[2px] rounded-full" style="background: #D97706; box-shadow: 0 0 4px rgba(217,119,6,0.5);"></span>
                            <span class="w-2"></span>
                            <span class="flex-1 h-[2px] rounded-full" style="background: #D97706; box-shadow: 0 0 4px rgba(217,119,6,0.5);"></span>
                            <span class="w-2"></span>
                            <span class="flex-1 h-[2px] rounded-full" style="background: #D97706; box-shadow: 0 0 4px rgba(217,119,6,0.5);"></span>
                            <span class="w-2"></span>
                            <span class="flex-1 h-[2px] rounded-full" style="background: #D97706; box-shadow: 0 0 4px rgba(217,119,6,0.5);"></span>
                        </div>

                        {{-- White edge lines --}}
                        <div class="absolute top-[6px] bottom-[6px] left-[6px] w-[2px] rounded-full" style="background: rgba(255,255,255,0.35); box-shadow: 0 0 4px rgba(255,255,255,0.15);"></div>
                        <div class="absolute top-[6px] bottom-[6px] right-[6px] w-[2px] rounded-full" style="background: rgba(255,255,255,0.35); box-shadow: 0 0 4px rgba(255,255,255,0.15);"></div>

                        {{-- Runway designators --}}
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 font-mono font-bold text-white text-[13px]" style="text-shadow: 0 0 8px rgba(255,255,255,0.3);">17</div>
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 font-mono font-bold text-white text-[13px]" style="text-shadow: 0 0 8px rgba(255,255,255,0.3);">35</div>

                        {{-- Threshold markings (left) --}}
                        <div class="absolute left-[28px] top-[14px] bottom-[14px] flex flex-col justify-between">
                            <span class="block w-[2px] h-[8px] rounded-full bg-white/40"></span>
                            <span class="block w-[2px] h-[8px] rounded-full bg-white/40"></span>
                            <span class="block w-[2px] h-[8px] rounded-full bg-white/40"></span>
                        </div>

                        {{-- Threshold markings (right) --}}
                        <div class="absolute right-[28px] top-[14px] bottom-[14px] flex flex-col justify-between">
                            <span class="block w-[2px] h-[8px] rounded-full bg-white/40"></span>
                            <span class="block w-[2px] h-[8px] rounded-full bg-white/40"></span>
                            <span class="block w-[2px] h-[8px] rounded-full bg-white/40"></span>
                        </div>
                    </div>
                </div>

                <!-- Zulu clock -->
                <div class="mt-10">
                    <span id="zulu-clock" class="font-mono text-sm" style="color: rgba(255,255,255,0.6);">--:--:--Z UTC</span>
                </div>

                <!-- Feature callouts -->
                <div class="mt-10 flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <span class="h-1.5 w-1.5 rounded-full flex-shrink-0" style="background-color: #2563EB;"></span>
                        <span class="font-mono text-[11px]" style="color: rgba(255,255,255,0.4);">FAA Part 139 daily inspection tracking</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="h-1.5 w-1.5 rounded-full flex-shrink-0" style="background-color: #2563EB;"></span>
                        <span class="font-mono text-[11px]" style="color: rgba(255,255,255,0.4);">Tamper-evident audit chain</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="h-1.5 w-1.5 rounded-full flex-shrink-0" style="background-color: #2563EB;"></span>
                        <span class="font-mono text-[11px]" style="color: rgba(255,255,255,0.4);">Real-time METAR/TAF integration</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Login Form -->
        <div class="flex flex-1 flex-col justify-center items-center bg-white">
            <div class="w-full max-w-sm px-8">

                <!-- Logo -->
                <div class="text-center fade-up">
                    <span class="font-mono font-semibold text-xl tracking-wider text-slate-900">CLEAR</span><span class="font-mono font-semibold text-xl tracking-wider" style="color: #D97706;">139</span>
                </div>
                <div class="text-center mt-1 fade-up fade-up-delay-1">
                    <span class="font-mono text-[10px] text-slate-400 uppercase tracking-widest">Part 139 Operations Platform</span>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-6 mt-6" :status="session('status')" />

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="mt-10 space-y-6 fade-up fade-up-delay-2">
                    @csrf

                    <!-- Email -->
                    <div class="relative float-label">
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder=" "
                            class="peer block w-full h-12 px-4 pt-3 border border-slate-200 rounded-lg text-sm font-sans text-slate-900 bg-white
                                   focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:outline-none
                                   transition"
                        />
                        <label for="email" class="absolute left-4 top-3.5 text-sm text-slate-400 pointer-events-none">
                            Email address
                        </label>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="relative float-label">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            placeholder=" "
                            class="peer block w-full h-12 px-4 pt-3 border border-slate-200 rounded-lg text-sm font-sans text-slate-900 bg-white
                                   focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:outline-none
                                   transition"
                        />
                        <label for="password" class="absolute left-4 top-3.5 text-sm text-slate-400 pointer-events-none">
                            Password
                        </label>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember / Forgot -->
                    <div class="flex items-center justify-between gap-4">
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input
                                type="checkbox"
                                name="remember"
                                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            />
                            <span>Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a
                                href="{{ route('password.request') }}"
                                class="text-sm font-medium text-blue-600 hover:text-blue-500"
                            >
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <!-- Submit -->
                    <div>
                        <button
                            type="submit"
                            class="flex w-full justify-center items-center h-11 rounded-lg bg-blue-600 hover:bg-blue-700
                                   text-sm font-semibold text-white shadow-sm transition"
                        >
                            Sign in
                        </button>
                    </div>
                </form>

                <!-- Trust line -->
                <div class="mt-8 flex items-center justify-center gap-1.5 fade-up fade-up-delay-4">
                    <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    <span class="font-mono text-[10px] text-slate-400 tracking-wide">Secured by FAA-auditable encryption</span>
                </div>

            </div>
        </div>

    </div>

    <!-- Zulu Clock Script -->
    <script>
        function updateZuluClock() {
            const now = new Date();
            const h = String(now.getUTCHours()).padStart(2, '0');
            const m = String(now.getUTCMinutes()).padStart(2, '0');
            const s = String(now.getUTCSeconds()).padStart(2, '0');
            document.getElementById('zulu-clock').textContent = h + ':' + m + ':' + s + 'Z UTC';
        }
        updateZuluClock();
        setInterval(updateZuluClock, 1000);
    </script>
</x-guest-layout>
