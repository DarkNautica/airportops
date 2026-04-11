<x-sidebar-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="font-mono text-[11px] text-slate-400 uppercase tracking-wide">NOTAM</div>
                <h1 class="font-instrument text-xl text-gray-900">
                    {{ $notam->notam_number }}
                </h1>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('notams.index') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-surface-border px-3.5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                    Back
                </a>

                @if(!$notam->is_locked)
                    <a href="{{ route('notams.edit', $notam) }}"
                       class="inline-flex items-center rounded-lg border border-surface-border px-3.5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Edit
                    </a>
                @endif

                {{-- Workflow --}}
                @if(!$notam->is_locked && $notam->status !== 'Active')
                    <form method="POST" action="{{ route('notams.activate', $notam) }}">
                        @csrf
                        <button class="inline-flex items-center rounded-lg bg-emerald-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition-colors">
                            Set Active
                        </button>
                    </form>
                @endif

                @if(!$notam->is_locked && $notam->status !== 'Cancelled')
                    <form method="POST" action="{{ route('notams.cancel', $notam) }}">
                        @csrf
                        <button class="inline-flex items-center rounded-lg bg-red-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 transition-colors">
                            Cancel
                        </button>
                    </form>
                @endif

                {{-- Locking --}}
                @if(!$notam->is_locked)
                    <form method="POST" action="{{ route('notams.lock', $notam) }}">
                        @csrf
                        <button class="inline-flex items-center rounded-lg bg-slate-800 px-3.5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-900 transition-colors">
                            Lock
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('notams.unlock', $notam) }}">
                        @csrf
                        <button class="inline-flex items-center rounded-lg border border-surface-border px-3.5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Unlock
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Flash --}}
            @if (session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Summary strip --}}
            @php
                $status = $notam->status ?? '—';
                $badgeVariant = match ($status) {
                    'Active' => 'active',
                    'Draft' => 'draft',
                    'Expired' => 'warning',
                    'Cancelled' => 'cancelled',
                    default => 'neutral',
                };
            @endphp

            <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                <div class="panel-header">
                    <span class="panel-header-label">SUMMARY</span>
                </div>
                <div class="p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="space-y-1">
                        <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Station</div>
                        <div class="text-sm font-semibold text-gray-900">{{ $notam->station }}</div>
                    </div>

                    <div class="space-y-1">
                        <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Category</div>
                        <div class="text-sm font-semibold text-gray-900">{{ $notam->category }}</div>
                    </div>

                    <div class="space-y-1">
                        <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Status</div>
                        <x-badge :variant="$badgeVariant">{{ $status }}</x-badge>
                    </div>

                    <div class="space-y-1">
                        <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Lock</div>
                        @if($notam->is_locked)
                            <x-badge variant="locked">LOCKED</x-badge>
                            <div class="font-mono text-[11px] text-slate-400 mt-1">
                                {{ $notam->locked_at?->format('Y-m-d H:i') ?? '—' }}Z
                                by {{ $notam->lockedBy?->name ?? '—' }}
                            </div>
                        @else
                            <div class="text-sm text-slate-400">Unlocked</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Effective Dates --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                    <div class="panel-header">
                        <span class="panel-header-label">EFFECTIVE FROM</span>
                    </div>
                    <div class="p-5">
                        <div class="font-mono text-[11px] text-gray-900 text-lg font-semibold">
                            {{ $notam->effective_from?->format('Y-m-d H:i') ?? '—' }}Z
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                    <div class="panel-header">
                        <span class="panel-header-label">EFFECTIVE TO</span>
                    </div>
                    <div class="p-5">
                        <div class="font-mono text-[11px] text-gray-900 text-lg font-semibold">
                            {{ $notam->effective_to?->format('Y-m-d H:i') ?? '—' }}Z
                        </div>
                    </div>
                </div>
            </div>

            {{-- Subject --}}
            <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                <div class="panel-header">
                    <span class="panel-header-label">SUBJECT</span>
                </div>
                <div class="p-5">
                    <div class="text-sm font-semibold text-gray-900">
                        {{ $notam->subject ?? '—' }}
                    </div>
                </div>
            </div>

            {{-- NOTAM Text --}}
            <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                <div class="panel-header">
                    <span class="panel-header-label">NOTAM TEXT</span>
                </div>
                <div class="p-5">
                    <pre class="whitespace-pre-wrap font-mono text-sm text-gray-900 bg-[#F8FAFC] border border-surface-border rounded-lg p-4">{{ $notam->notam_text }}</pre>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="bg-white rounded-xl shadow-card border border-red-200 overflow-hidden">
                <div class="panel-header" style="border-bottom-color: rgb(254 202 202);">
                    <span class="panel-header-label text-red-600">DANGER ZONE</span>
                </div>
                <div class="p-5 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        This removes the NOTAM record from the system. Delete is blocked when locked.
                    </div>

                    @if(!$notam->is_locked)
                        <form method="POST" action="{{ route('notams.destroy', $notam) }}"
                              onsubmit="return confirm('Delete this NOTAM? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button class="inline-flex items-center rounded-lg bg-red-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 transition-colors">
                                Delete
                            </button>
                        </form>
                    @else
                        <button class="inline-flex items-center rounded-lg border border-surface-border px-3.5 py-2 text-sm font-medium text-gray-400 cursor-not-allowed" disabled>
                            Delete (Locked)
                        </button>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-sidebar-app-layout>
