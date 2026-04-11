<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLEAR139 — The Operations Platform Built for Part 139</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif&family=Geist:wght@100..900&family=Geist+Mono:wght@100..900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Word reveal */
        .word-reveal {
            opacity: 0;
            transform: translateY(24px);
            filter: blur(4px);
            animation: wordReveal 0.7s ease-out forwards;
        }
        .word-reveal-1 { animation-delay: 0.1s; }
        .word-reveal-2 { animation-delay: 0.2s; }
        .word-reveal-3 { animation-delay: 0.3s; }
        .word-reveal-4 { animation-delay: 0.4s; }
        .word-reveal-5 { animation-delay: 0.5s; }
        .word-reveal-6 { animation-delay: 0.6s; }
        @keyframes wordReveal {
            to { opacity: 1; transform: translateY(0); filter: blur(0); }
        }

        /* Fade up */
        .fade-up {
            opacity: 0;
            transform: translateY(16px);
            animation: fadeUp 0.6s ease-out forwards;
        }
        .fade-up-d1 { animation-delay: 0.8s; }
        .fade-up-d2 { animation-delay: 1.0s; }
        .fade-up-d3 { animation-delay: 1.2s; }
        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }

        /* Scroll reveal */
        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        .reveal.revealed {
            opacity: 1;
            transform: translateY(0);
        }

        /* Runway scroll */
        @keyframes runwayScroll {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }

        /* Marquee */
        @keyframes marquee {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }

        /* Grid overlay */
        .grid-overlay {
            background-image:
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* Shadow card */
        .shadow-card {
            box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        }

        /* Runway edge lights */
        .edge-lights {
            background-image: radial-gradient(circle, rgba(255,255,255,0.5) 1px, transparent 1px);
            background-size: 48px 100%;
            background-position: 0 center;
        }

        /* Runway centerline dashes */
        .runway-centerline {
            background: repeating-linear-gradient(
                90deg,
                #D97706 0px,
                #D97706 24px,
                transparent 24px,
                transparent 48px
            );
            animation: runwayScroll 8s linear infinite;
        }

        /* Terminal animation */
        .terminal-line { opacity: 0; animation: terminalFade 0.4s ease-out forwards; }
        .terminal-line:nth-child(1) { animation-delay: 0.5s; }
        .terminal-line:nth-child(2) { animation-delay: 0.9s; }
        .terminal-line:nth-child(3) { animation-delay: 1.3s; }
        .terminal-line:nth-child(4) { animation-delay: 1.7s; }
        .terminal-line:nth-child(5) { animation-delay: 2.1s; }
        .terminal-line:nth-child(6) { animation-delay: 2.5s; }
        .terminal-line:nth-child(7) { animation-delay: 2.9s; }
        .terminal-line:nth-child(8) { animation-delay: 3.3s; }
        @keyframes terminalFade { to { opacity: 1; } }
    </style>
</head>
<body class="font-['Geist',sans-serif] antialiased">

    {{-- ============ NAVIGATION ============ --}}
    <nav id="main-nav" class="fixed top-0 left-0 right-0 z-50 backdrop-blur bg-[#0E1520]/80 border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-0">
                <span class="font-['Geist_Mono',monospace] font-medium text-white tracking-wider text-lg">CLEAR</span>
                <span class="font-['Geist_Mono',monospace] font-medium text-[#F59E0B] tracking-wider text-lg">139</span>
            </a>

            {{-- Center links (desktop) --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="#features" class="nav-link text-white/60 hover:text-white text-[13px] font-medium transition-colors">Features</a>
                <a href="#how-it-works" class="nav-link text-white/60 hover:text-white text-[13px] font-medium transition-colors">How It Works</a>
                <a href="#pricing" class="nav-link text-white/60 hover:text-white text-[13px] font-medium transition-colors">Pricing</a>
            </div>

            {{-- Right CTA (desktop) --}}
            <div class="hidden md:block">
                @auth
                    <a href="/dashboard" class="bg-[#2563EB] hover:bg-[#3B82F6] text-white text-[13px] font-semibold px-5 py-2.5 rounded-lg transition-colors">Dashboard</a>
                @else
                    <a href="mailto:jayden@clear139.com" class="bg-[#2563EB] hover:bg-[#3B82F6] text-white text-[13px] font-semibold px-5 py-2.5 rounded-lg transition-colors">Request Demo</a>
                @endauth
            </div>

            {{-- Hamburger (mobile) --}}
            <button id="mobile-menu-btn" class="md:hidden text-white/60 hover:text-white" aria-label="Toggle menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path id="hamburger-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="hidden md:hidden bg-[#0E1520]/95 backdrop-blur border-b border-white/5 px-6 pb-4">
            <a href="#features" class="block py-2 text-white/60 hover:text-white text-[13px] font-medium transition-colors">Features</a>
            <a href="#how-it-works" class="block py-2 text-white/60 hover:text-white text-[13px] font-medium transition-colors">How It Works</a>
            <a href="#pricing" class="block py-2 text-white/60 hover:text-white text-[13px] font-medium transition-colors">Pricing</a>
            <div class="mt-3">
                @auth
                    <a href="/dashboard" class="inline-block bg-[#2563EB] hover:bg-[#3B82F6] text-white text-[13px] font-semibold px-5 py-2.5 rounded-lg transition-colors">Dashboard</a>
                @else
                    <a href="mailto:jayden@clear139.com" class="inline-block bg-[#2563EB] hover:bg-[#3B82F6] text-white text-[13px] font-semibold px-5 py-2.5 rounded-lg transition-colors">Request Demo</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- ============ HERO ============ --}}
    <section class="relative min-h-screen flex items-center bg-[#0E1520] overflow-hidden">
        {{-- Background layers --}}
        <div class="absolute inset-0 grid-overlay"></div>
        <div class="absolute inset-0" style="background: radial-gradient(ellipse at top right, rgba(37,99,235,0.1) 0%, transparent 60%);"></div>
        <div class="absolute inset-0" style="background: radial-gradient(ellipse at bottom left, rgba(217,119,6,0.06) 0%, transparent 60%);"></div>

        {{-- Hero content --}}
        <div class="relative z-10 max-w-5xl mx-auto px-6 pt-24 pb-32 text-center">
            {{-- Headline --}}
            <h1 class="font-['Instrument_Serif',serif] text-5xl lg:text-7xl text-white leading-tight">
                <span class="word-reveal word-reveal-1 inline-block">The</span>
                <span class="word-reveal word-reveal-2 inline-block">Operations</span>
                <span class="word-reveal word-reveal-3 inline-block">Platform</span>
                <span class="word-reveal word-reveal-4 inline-block">Built</span>
                <span class="word-reveal word-reveal-5 inline-block">for</span>
                <span class="word-reveal word-reveal-6 inline-block">Part&nbsp;139.</span>
            </h1>

            {{-- Subheadline --}}
            <p class="fade-up fade-up-d1 mt-6 text-lg text-white/60 max-w-2xl mx-auto">
                FAA audit-ready inspection logs, tamper-evident records, and shift continuity tools — built by a working airfield operations specialist.
            </p>

            {{-- CTA buttons --}}
            <div class="fade-up fade-up-d2 mt-10 flex flex-wrap justify-center gap-4">
                @guest
                    <a href="mailto:jayden@clear139.com" class="bg-[#2563EB] hover:bg-[#3B82F6] text-white px-6 py-3 rounded-lg font-semibold transition-colors">Request a Demo</a>
                    <a href="#features" class="border border-white/20 text-white/80 hover:text-white hover:border-white/40 px-6 py-3 rounded-lg font-semibold transition-colors">See the Platform</a>
                @else
                    <a href="/dashboard" class="bg-[#2563EB] hover:bg-[#3B82F6] text-white px-6 py-3 rounded-lg font-semibold transition-colors">Go to Dashboard</a>
                @endguest
            </div>

            {{-- Stat strip --}}
            <div class="fade-up fade-up-d3 mt-16 flex flex-wrap justify-center items-center gap-4">
                <span class="font-['Geist_Mono',monospace] text-[11px] text-white/40 uppercase tracking-widest">500+ Part 139 Airports in the US</span>
                <span class="text-white/20">&bull;</span>
                <span class="font-['Geist_Mono',monospace] text-[11px] text-white/40 uppercase tracking-widest">FAA Audit-Ready by Design</span>
                <span class="text-white/20">&bull;</span>
                <span class="font-['Geist_Mono',monospace] text-[11px] text-white/40 uppercase tracking-widest">Built by an Ops Specialist</span>
                <span class="text-white/20">&bull;</span>
                <span class="font-['Geist_Mono',monospace] text-[11px] text-white/40 uppercase tracking-widest">Zero Long-Term Contracts</span>
            </div>
        </div>

        {{-- Animated runway --}}
        <div class="absolute bottom-0 left-0 right-0 h-16">
            <div class="absolute inset-0 bg-[#0a0e18] border-t border-white/5"></div>
            {{-- Top edge lights --}}
            <div class="absolute top-0 left-0 right-0 h-[2px] edge-lights"></div>
            {{-- Bottom edge lights --}}
            <div class="absolute bottom-0 left-0 right-0 h-[2px] edge-lights"></div>
            {{-- Animated centerline --}}
            <div class="absolute top-1/2 -translate-y-1/2 left-0 h-[3px] overflow-hidden" style="width: 100%;">
                <div class="runway-centerline h-full" style="width: 200%;"></div>
            </div>
        </div>
    </section>

    {{-- ============ SOCIAL PROOF BAR ============ --}}
    <section class="bg-[#0a0e18] py-5 border-y border-white/5 overflow-hidden">
        <div class="relative flex" style="width: max-content; animation: marquee 30s linear infinite;">
            @for ($i = 0; $i < 2; $i++)
                <div class="flex items-center gap-6 shrink-0 px-3">
                    <span class="font-['Geist_Mono',monospace] text-[11px] text-white/30 uppercase tracking-widest whitespace-nowrap">Part 139 Inspections</span>
                    <span class="text-white/10">&bull;</span>
                    <span class="font-['Geist_Mono',monospace] text-[11px] text-white/30 uppercase tracking-widest whitespace-nowrap">Pass Along System</span>
                    <span class="text-white/10">&bull;</span>
                    <span class="font-['Geist_Mono',monospace] text-[11px] text-white/30 uppercase tracking-widest whitespace-nowrap">Work Order Management</span>
                    <span class="text-white/10">&bull;</span>
                    <span class="font-['Geist_Mono',monospace] text-[11px] text-white/30 uppercase tracking-widest whitespace-nowrap">NOTAM Tracker</span>
                    <span class="text-white/10">&bull;</span>
                    <span class="font-['Geist_Mono',monospace] text-[11px] text-white/30 uppercase tracking-widest whitespace-nowrap">Wildlife Hazard Logs</span>
                    <span class="text-white/10">&bull;</span>
                    <span class="font-['Geist_Mono',monospace] text-[11px] text-white/30 uppercase tracking-widest whitespace-nowrap">Audit Trail</span>
                    <span class="text-white/10">&bull;</span>
                    <span class="font-['Geist_Mono',monospace] text-[11px] text-white/30 uppercase tracking-widest whitespace-nowrap">Role-Based Access</span>
                    <span class="text-white/10">&bull;</span>
                    <span class="font-['Geist_Mono',monospace] text-[11px] text-white/30 uppercase tracking-widest whitespace-nowrap">FAA Audit Mode</span>
                    <span class="text-white/10 pr-6">&bull;</span>
                </div>
            @endfor
        </div>
    </section>

    {{-- ============ FEATURES ============ --}}
    <section id="features" class="bg-[#F5F7FA] py-24">
        <div class="max-w-6xl mx-auto px-6">
            {{-- Section header --}}
            <div class="text-center">
                <span class="font-['Geist_Mono',monospace] text-[10px] text-[#2563EB] uppercase tracking-widest font-semibold">PLATFORM MODULES</span>
                <h2 class="font-['Instrument_Serif',serif] text-4xl text-gray-900 mt-3">Everything Part 139 requires. Nothing it doesn't.</h2>
            </div>

            {{-- Feature grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mt-14">

                {{-- Card 1: Part 139 Self-Inspections --}}
                <div class="reveal bg-white rounded-xl shadow-card border border-[#E4E9F0] overflow-hidden hover:shadow-md transition">
                    <div class="h-[2px] bg-[#2563EB]"></div>
                    <div class="p-5">
                        <div class="text-2xl mb-3">&#x1F4CB;</div>
                        <h3 class="text-sm font-semibold text-gray-900">Part 139 Self-Inspections</h3>
                        <p class="text-[13px] text-gray-500 mt-2 leading-relaxed">Structured AM/PM checklists, certification workflow, and one-click FAA audit mode.</p>
                    </div>
                </div>

                {{-- Card 2: Pass Along System --}}
                <div class="reveal bg-white rounded-xl shadow-card border border-[#E4E9F0] overflow-hidden hover:shadow-md transition">
                    <div class="h-[2px] bg-[#16A34A]"></div>
                    <div class="p-5">
                        <div class="text-2xl mb-3">&#x1F503;</div>
                        <h3 class="text-sm font-semibold text-gray-900">Pass Along System</h3>
                        <p class="text-[13px] text-gray-500 mt-2 leading-relaxed">Digital shift handoff with dynamic rows, file attachments, and immutable submission locking.</p>
                    </div>
                </div>

                {{-- Card 3: Work Order Management --}}
                <div class="reveal bg-white rounded-xl shadow-card border border-[#E4E9F0] overflow-hidden hover:shadow-md transition">
                    <div class="h-[2px] bg-[#D97706]"></div>
                    <div class="p-5">
                        <div class="text-2xl mb-3">&#x1F527;</div>
                        <h3 class="text-sm font-semibold text-gray-900">Work Order Management</h3>
                        <p class="text-[13px] text-gray-500 mt-2 leading-relaxed">Create, assign, prioritize, and close discrepancies with full accountability.</p>
                    </div>
                </div>

                {{-- Card 4: NOTAM Tracker --}}
                <div class="reveal bg-white rounded-xl shadow-card border border-[#E4E9F0] overflow-hidden hover:shadow-md transition">
                    <div class="h-[2px] bg-[#DC2626]"></div>
                    <div class="p-5">
                        <div class="text-2xl mb-3">&#x1F4E2;</div>
                        <h3 class="text-sm font-semibold text-gray-900">NOTAM Tracker</h3>
                        <p class="text-[13px] text-gray-500 mt-2 leading-relaxed">Activate, monitor, and archive NOTAMs with complete status history.</p>
                    </div>
                </div>

                {{-- Card 5: Wildlife Hazard Log --}}
                <div class="reveal bg-white rounded-xl shadow-card border border-[#E4E9F0] overflow-hidden hover:shadow-md transition">
                    <div class="h-[2px] bg-[#D97706]"></div>
                    <div class="p-5">
                        <div class="text-2xl mb-3">&#x1F426;</div>
                        <h3 class="text-sm font-semibold text-gray-900">Wildlife Hazard Log</h3>
                        <p class="text-[13px] text-gray-500 mt-2 leading-relaxed">FAA Advisory Circular compliant logging for wildlife strikes and observations.</p>
                    </div>
                </div>

                {{-- Card 6: Immutable Audit Trail --}}
                <div class="reveal bg-white rounded-xl shadow-card border border-[#E4E9F0] overflow-hidden hover:shadow-md transition">
                    <div class="h-[2px] bg-[#2563EB]"></div>
                    <div class="p-5">
                        <div class="text-2xl mb-3">&#x1F512;</div>
                        <h3 class="text-sm font-semibold text-gray-900">Immutable Audit Trail</h3>
                        <p class="text-[13px] text-gray-500 mt-2 leading-relaxed">Cryptographic HMAC-SHA256 hash chain. Every action logged, timestamped, and verified.</p>
                    </div>
                </div>

                {{-- Card 7: Role-Based Access --}}
                <div class="reveal bg-white rounded-xl shadow-card border border-[#E4E9F0] overflow-hidden hover:shadow-md transition">
                    <div class="h-[2px] bg-[#16A34A]"></div>
                    <div class="p-5">
                        <div class="text-2xl mb-3">&#x1F465;</div>
                        <h3 class="text-sm font-semibold text-gray-900">Role-Based Access</h3>
                        <p class="text-[13px] text-gray-500 mt-2 leading-relaxed">Admin, Supervisor, Staff, and Viewer roles with granular permission control.</p>
                    </div>
                </div>

                {{-- Card 8: FAA Audit Mode --}}
                <div class="reveal bg-white rounded-xl shadow-card border border-[#E4E9F0] overflow-hidden hover:shadow-md transition">
                    <div class="h-[2px] bg-[#2563EB]"></div>
                    <div class="p-5">
                        <div class="text-2xl mb-3">&#x1F5A8;</div>
                        <h3 class="text-sm font-semibold text-gray-900">FAA Audit Mode</h3>
                        <p class="text-[13px] text-gray-500 mt-2 leading-relaxed">One-click printable PDF layouts for every module. Always inspection-ready.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

{{-- SECTION 4: THE AUDIT CHAIN --}}
<section id="audit-chain" class="bg-[#0E1520] py-24 border-t border-white/5">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            {{-- LEFT SIDE --}}
            <div>
                <span class="font-mono text-[10px] text-[#2563EB] uppercase tracking-widest font-semibold">TAMPER-EVIDENT RECORDS</span>
                <h2 class="font-serif text-4xl text-white mt-3">Built for the day the FAA shows up.</h2>
                <p class="mt-4 text-white/50 leading-relaxed">Every action in Clear139 is cryptographically hashed into an immutable chain. Records lock permanently after certification. No edits, no deletions, no questions.</p>

                <div class="mt-8 space-y-5">
                    {{-- Bullet 1 --}}
                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 shrink-0 rounded-xl bg-[#2563EB]/10 border border-[#2563EB]/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-[#2563EB]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <div>
                            <div class="text-white font-semibold text-sm">Immutable Record Locking</div>
                            <div class="text-white/40 text-[13px] mt-1">Certified inspections lock permanently. Admin unlock creates a new audit entry.</div>
                        </div>
                    </div>

                    {{-- Bullet 2 --}}
                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 shrink-0 rounded-xl bg-[#2563EB]/10 border border-[#2563EB]/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-[#2563EB]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <div class="text-white font-semibold text-sm">Full User Accountability</div>
                            <div class="text-white/40 text-[13px] mt-1">Every action tied to a user, IP address, timestamp, and hash.</div>
                        </div>
                    </div>

                    {{-- Bullet 3 --}}
                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 shrink-0 rounded-xl bg-[#2563EB]/10 border border-[#2563EB]/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-[#2563EB]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <div class="text-white font-semibold text-sm">FAA Audit Mode</div>
                            <div class="text-white/40 text-[13px] mt-1">One-click printable PDF for every module. Always inspection-ready.</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT SIDE: TERMINAL WINDOW --}}
            <div class="bg-[#0a0e18] rounded-xl border border-white/10 overflow-hidden shadow-modal">
                {{-- Title bar --}}
                <div class="h-8 bg-[#0a0e18] border-b border-white/5 flex items-center px-4 gap-2">
                    <div class="w-2 h-2 rounded-full bg-[#EF4444]"></div>
                    <div class="w-2 h-2 rounded-full bg-[#EAB308]"></div>
                    <div class="w-2 h-2 rounded-full bg-[#22C55E]"></div>
                    <span class="font-mono text-[10px] text-white/30 ml-3">audit_chain.log</span>
                </div>
                {{-- Terminal body --}}
                <div class="p-5 font-mono text-[11px] leading-6 h-[320px] overflow-hidden">
                    <div class="terminal-line"><span class="text-white/30">[2026-04-11 14:32:07Z]</span> <span class="text-[#2563EB]">CREATED</span>  <span class="text-white/40">inspection INSP-000847</span>  <span class="text-white/60">user:j.robbins</span>  <span class="text-white/40">ip:10.0.1.42</span></div>
                    <div class="terminal-line"><span class="text-white/30">[2026-04-11 14:32:07Z]</span> <span class="text-[#16A34A]">HASH</span>     <span class="text-white/40">sha256:a3f8c1...e92b</span>  <span class="text-[#16A34A]">chain:VALID</span>  <span class="text-white/40">prev:7d2e01...f841</span></div>
                    <div class="terminal-line"><span class="text-white/30">[2026-04-11 14:45:12Z]</span> <span class="text-[#2563EB]">CERTIFIED</span> <span class="text-white/40">inspection INSP-000847</span>  <span class="text-white/60">user:j.robbins</span>  <span class="text-[#F59E0B]">LOCKED</span></div>
                    <div class="terminal-line"><span class="text-white/30">[2026-04-11 14:45:12Z]</span> <span class="text-[#16A34A]">HASH</span>     <span class="text-white/40">sha256:e7b2d4...1ca9</span>  <span class="text-[#16A34A]">chain:VALID</span>  <span class="text-white/40">prev:a3f8c1...e92b</span></div>
                    <div class="terminal-line"><span class="text-white/30">[2026-04-11 15:01:33Z]</span> <span class="text-[#2563EB]">CREATED</span>  <span class="text-white/40">pass_along PA-2026-04-11</span>  <span class="text-white/60">user:m.chen</span>  <span class="text-white/40">shift:0600-1400</span></div>
                    <div class="terminal-line"><span class="text-white/30">[2026-04-11 15:01:33Z]</span> <span class="text-[#16A34A]">HASH</span>     <span class="text-white/40">sha256:d91f7a...3b82</span>  <span class="text-[#16A34A]">chain:VALID</span>  <span class="text-white/40">prev:e7b2d4...1ca9</span></div>
                    <div class="terminal-line"><span class="text-white/30">[2026-04-11 15:30:00Z]</span> <span class="text-[#2563EB]">SUBMITTED</span> <span class="text-white/40">pass_along PA-2026-04-11</span>  <span class="text-white/60">user:m.chen</span>  <span class="text-[#F59E0B]">LOCKED</span></div>
                    <div class="terminal-line"><span class="text-white/30">[2026-04-11 15:30:00Z]</span> <span class="text-[#16A34A]">HASH</span>     <span class="text-white/40">sha256:b4c629...8e15</span>  <span class="text-[#16A34A]">chain:VALID</span>  <span class="text-white/40">prev:d91f7a...3b82</span></div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- SECTION 5: HOW IT WORKS --}}
<section id="how-it-works" class="bg-[#F5F7FA] py-24">
    <div class="max-w-5xl mx-auto px-6 text-center">
        <span class="font-mono text-[10px] text-[#2563EB] uppercase tracking-widest">SIMPLE BY DESIGN</span>
        <h2 class="font-serif text-4xl text-gray-900 mt-3">From shift start to FAA inspection. Covered.</h2>

        <div class="mt-16 grid grid-cols-1 md:grid-cols-4 gap-8 relative">
            {{-- Connecting line --}}
            <div class="hidden md:block absolute h-px bg-[#E4E9F0] top-6 left-[12.5%] right-[12.5%]"></div>

            {{-- Step 1 --}}
            <div class="reveal">
                <div class="w-12 h-12 rounded-full bg-[#0E1520] text-white font-mono font-bold text-sm flex items-center justify-center mx-auto relative z-10">1</div>
                <div class="mt-5 text-sm font-semibold text-gray-900">Log In</div>
                <div class="mt-2 text-[13px] text-gray-500 leading-relaxed">Your airport, your data &mdash; isolated and secure.</div>
            </div>

            {{-- Step 2 --}}
            <div class="reveal">
                <div class="w-12 h-12 rounded-full bg-[#0E1520] text-white font-mono font-bold text-sm flex items-center justify-center mx-auto relative z-10">2</div>
                <div class="mt-5 text-sm font-semibold text-gray-900">Run Inspections</div>
                <div class="mt-2 text-[13px] text-gray-500 leading-relaxed">Structured checklists. Certify and lock when complete.</div>
            </div>

            {{-- Step 3 --}}
            <div class="reveal">
                <div class="w-12 h-12 rounded-full bg-[#0E1520] text-white font-mono font-bold text-sm flex items-center justify-center mx-auto relative z-10">3</div>
                <div class="mt-5 text-sm font-semibold text-gray-900">Hand Off Shifts</div>
                <div class="mt-2 text-[13px] text-gray-500 leading-relaxed">Pass Along logs with full accountability and attachments.</div>
            </div>

            {{-- Step 4 --}}
            <div class="reveal">
                <div class="w-12 h-12 rounded-full bg-[#0E1520] text-white font-mono font-bold text-sm flex items-center justify-center mx-auto relative z-10">4</div>
                <div class="mt-5 text-sm font-semibold text-gray-900">Pass the Audit</div>
                <div class="mt-2 text-[13px] text-gray-500 leading-relaxed">Everything printable, timestamped, and hash-verified.</div>
            </div>
        </div>
    </div>
</section>

{{-- SECTION 6: PRICING --}}
<section id="pricing" class="bg-white py-24">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <span class="font-mono text-[10px] text-[#2563EB] uppercase tracking-widest">PRICING</span>
        <h2 class="font-serif text-4xl text-gray-900 mt-3">Simple. Transparent. No surprises.</h2>

        <div class="mt-14 grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Card 1: Non-Hub / Small Hub --}}
            <div class="reveal bg-white rounded-xl border border-[#E4E9F0] shadow-card overflow-hidden text-left" style="border-top: 2px solid #2563EB;">
                <div class="p-6 border-b border-[#E4E9F0]">
                    <div class="font-semibold text-gray-900">Non-Hub / Small Hub</div>
                    <div class="mt-3">
                        <span class="font-serif text-5xl text-gray-900">$299</span>
                        <span class="text-gray-500 text-sm">/month</span>
                    </div>
                </div>
                <div class="p-6 space-y-3">
                    <div class="text-[13px] text-gray-600"><span class="text-gray-400 mr-2">&rarr;</span>Up to 15 users</div>
                    <div class="text-[13px] text-gray-600"><span class="text-gray-400 mr-2">&rarr;</span>All core modules included</div>
                    <div class="text-[13px] text-gray-600"><span class="text-gray-400 mr-2">&rarr;</span>Tamper-evident audit chain</div>
                    <div class="text-[13px] text-gray-600"><span class="text-gray-400 mr-2">&rarr;</span>Unlimited inspections &amp; work orders</div>
                    <div class="text-[13px] text-gray-600"><span class="text-gray-400 mr-2">&rarr;</span>Email support</div>
                    <div class="text-[13px] text-gray-600"><span class="text-gray-400 mr-2">&rarr;</span>Free onboarding session</div>
                    <div class="pt-4">
                        <a href="mailto:jayden@clear139.com" class="block w-full py-2.5 rounded-lg border border-[#2563EB] text-[#2563EB] text-sm font-semibold text-center hover:bg-[#2563EB]/5 transition-colors">Request Demo</a>
                    </div>
                </div>
            </div>

            {{-- Card 2: Medium / Large Hub (FEATURED) --}}
            <div class="reveal bg-white rounded-xl border border-[#E4E9F0] shadow-modal overflow-hidden text-left relative" style="border-top: 2px solid #D97706;">
                <div class="p-6 border-b border-[#E4E9F0] relative">
                    <span class="absolute top-4 right-4 font-mono text-[10px] bg-[#D97706]/10 text-[#D97706] px-2 py-0.5 rounded-full">RECOMMENDED</span>
                    <div class="font-semibold text-gray-900">Medium / Large Hub</div>
                    <div class="mt-3">
                        <span class="font-serif text-5xl text-gray-900">$499</span>
                        <span class="text-gray-500 text-sm">/month</span>
                    </div>
                </div>
                <div class="p-6 space-y-3">
                    <div class="text-[13px] text-gray-600"><span class="text-gray-400 mr-2">&rarr;</span>Unlimited users</div>
                    <div class="text-[13px] text-gray-600"><span class="text-gray-400 mr-2">&rarr;</span>All core modules included</div>
                    <div class="text-[13px] text-gray-600"><span class="text-gray-400 mr-2">&rarr;</span>Tamper-evident audit chain</div>
                    <div class="text-[13px] text-gray-600"><span class="text-gray-400 mr-2">&rarr;</span>Unlimited inspections &amp; work orders</div>
                    <div class="text-[13px] text-gray-600"><span class="text-gray-400 mr-2">&rarr;</span>Priority support</div>
                    <div class="text-[13px] text-gray-600"><span class="text-gray-400 mr-2">&rarr;</span>Dedicated onboarding + migration</div>
                    <div class="text-[13px] text-gray-600"><span class="text-gray-400 mr-2">&rarr;</span>Custom role configuration</div>
                    <div class="text-[13px] text-gray-600"><span class="text-gray-400 mr-2">&rarr;</span>API access (coming soon)</div>
                    <div class="pt-4">
                        <a href="mailto:jayden@clear139.com" class="block w-full py-2.5 rounded-lg bg-[#2563EB] text-white text-sm font-semibold text-center hover:bg-[#3B82F6] transition-colors">Request Demo</a>
                    </div>
                </div>
            </div>

        </div>

        <p class="mt-8 text-[13px] text-gray-500">Free pilot program available for early adopter airports. No long-term contracts.</p>
    </div>
</section>

{{-- SECTION 7: CTA SECTION --}}
<section class="bg-[#0E1520] py-24 text-center">
    <div class="max-w-4xl mx-auto px-6">
        @guest
            <h2 class="font-serif text-4xl text-white">Ready to clear your compliance backlog?</h2>
            <p class="mt-4 text-white/50 text-lg max-w-2xl mx-auto">See Clear139 in action with a live demo built around your airport's workflow.</p>
            <div class="mt-8">
                <a href="mailto:jayden@clear139.com" class="inline-block bg-[#2563EB] hover:bg-[#3B82F6] text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors">Request a Demo</a>
            </div>
            <div class="mt-6 font-mono text-[11px] text-white/30">jayden@clear139.com &nbsp;&middot;&nbsp; clear139.com</div>
        @endguest
        @auth
            <h2 class="font-serif text-4xl text-white">Welcome back.</h2>
            <p class="mt-4 text-white/50 text-lg max-w-2xl mx-auto">See Clear139 in action with a live demo built around your airport's workflow.</p>
            <div class="mt-8">
                <a href="/dashboard" class="inline-block bg-[#2563EB] hover:bg-[#3B82F6] text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors">Go to Dashboard</a>
            </div>
        @endauth
    </div>
</section>

{{-- SECTION 8: FOOTER --}}
<footer class="bg-[#0a0e18] border-t border-white/5">
    <div class="max-w-6xl mx-auto px-6 py-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            {{-- Logo --}}
            <div class="font-mono text-sm tracking-wide">
                <span class="text-white font-bold">CLEAR</span><span class="text-[#D97706] font-bold">139</span>
            </div>
            {{-- Tagline --}}
            <div class="font-mono text-[11px] text-white/30">FAA Part 139 Compliance Platform</div>
            {{-- Contact --}}
            <div class="font-mono text-[11px] text-white/30">jayden@clear139.com</div>
        </div>
        <div class="mt-6 pt-6 border-t border-white/5">
            <div class="text-center font-mono text-[10px] text-white/20">&copy; 2026 Clear139. All rights reserved.</div>
        </div>
    </div>
</footer>

{{-- CLOSING JAVASCRIPT --}}
<script>
    // 1. Intersection Observer for scroll-reveal
    document.addEventListener('DOMContentLoaded', () => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    });

    // 2. Active nav section tracking
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('[data-nav-link]');
    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            const top = section.offsetTop - 100;
            if (scrollY >= top) current = section.id;
        });
        navLinks.forEach(link => {
            link.classList.toggle('text-white', link.getAttribute('href') === '#' + current);
            link.classList.toggle('text-white/60', link.getAttribute('href') !== '#' + current);
        });
    });

    // 3. Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            const target = document.querySelector(a.getAttribute('href'));
            if (target) target.scrollIntoView({ behavior: 'smooth' });
        });
    });

    // 4. Mobile menu toggle
    const menuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }
</script>

</body>
</html>
