<x-sidebar-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="text-xs text-gray-500">NOTAM</div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $notam->notam_number }}
                </h2>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('notams.index') }}"
                   class="px-3 py-2 rounded-md border text-sm text-gray-700 hover:bg-gray-50">
                    ← Back
                </a>

                @if(!$notam->is_locked)
                    <a href="{{ route('notams.edit', $notam) }}"
                       class="px-3 py-2 rounded-md border text-sm text-gray-700 hover:bg-gray-50">
                        Edit
                    </a>
                @endif

                {{-- Workflow --}}
                @if(!$notam->is_locked && $notam->status !== 'Active')
                    <form method="POST" action="{{ route('notams.activate', $notam) }}">
                        @csrf
                        <button class="px-3 py-2 rounded-md bg-green-600 text-white text-sm font-semibold hover:bg-green-700">
                            Set Active
                        </button>
                    </form>
                @endif

                @if(!$notam->is_locked && $notam->status !== 'Cancelled')
                    <form method="POST" action="{{ route('notams.cancel', $notam) }}">
                        @csrf
                        <button class="px-3 py-2 rounded-md bg-red-600 text-white text-sm font-semibold hover:bg-red-700">
                            Cancel
                        </button>
                    </form>
                @endif

                {{-- Locking --}}
                @if(!$notam->is_locked)
                    <form method="POST" action="{{ route('notams.lock', $notam) }}">
                        @csrf
                        <button class="px-3 py-2 rounded-md bg-gray-900 text-white text-sm font-semibold hover:bg-black">
                            Lock
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('notams.unlock', $notam) }}">
                        @csrf
                        <button class="px-3 py-2 rounded-md border text-sm text-gray-700 hover:bg-gray-50">
                            Unlock
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Flash --}}
            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-900">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-900">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Summary strip --}}
            @php
                $status = $notam->status ?? '—';
                $badge = match ($status) {
                    'Active' => 'bg-green-100 text-green-800',
                    'Draft' => 'bg-gray-100 text-gray-800',
                    'Expired' => 'bg-yellow-100 text-yellow-800',
                    'Cancelled' => 'bg-red-100 text-red-800',
                    default => 'bg-gray-100 text-gray-800',
                };
            @endphp

            <div class="bg-white rounded-lg border border-gray-200 p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="space-y-1">
                    <div class="text-xs text-gray-500">Station</div>
                    <div class="font-semibold text-gray-900">{{ $notam->station }}</div>
                </div>

                <div class="space-y-1">
                    <div class="text-xs text-gray-500">Category</div>
                    <div class="font-semibold text-gray-900">{{ $notam->category }}</div>
                </div>

                <div class="space-y-1">
                    <div class="text-xs text-gray-500">Status</div>
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $badge }}">
                        {{ $status }}
                    </span>
                    @if($notam->is_locked)
                        <div class="text-xs font-semibold text-gray-900 mt-1">LOCKED</div>
                        <div class="text-xs text-gray-500">
                            {{ $notam->locked_at?->format('Y-m-d H:i') ?? '—' }}Z
                            by {{ $notam->lockedBy?->name ?? '—' }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Times --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg border border-gray-200 p-5">
                    <div class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Effective From</div>
                    <div class="mt-1 text-lg font-semibold text-gray-900">
                        {{ $notam->effective_from?->format('Y-m-d H:i') ?? '—' }}Z
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-5">
                    <div class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Effective To</div>
                    <div class="mt-1 text-lg font-semibold text-gray-900">
                        {{ $notam->effective_to?->format('Y-m-d H:i') ?? '—' }}Z
                    </div>
                </div>
            </div>

            {{-- Subject + Text --}}
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <div class="font-semibold text-gray-900">Subject</div>
                    <div class="text-xs text-gray-500">Ops-friendly summary line.</div>
                </div>
                <div class="p-5">
                    <div class="text-lg font-semibold text-gray-900">
                        {{ $notam->subject ?? '—' }}
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <div class="font-semibold text-gray-900">NOTAM Text</div>
                        <div class="text-xs text-gray-500">Stored exactly as filed for reconciliation.</div>
                    </div>
                </div>

                <div class="p-5">
                    <pre class="whitespace-pre-wrap font-mono text-sm text-gray-900 bg-gray-50 border border-gray-200 rounded-md p-4">{{ $notam->notam_text }}</pre>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <div class="font-semibold text-gray-900">Danger Zone</div>
                    <div class="text-xs text-gray-500">Delete is blocked when locked.</div>
                </div>
                <div class="p-5 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        This removes the NOTAM record from the system.
                    </div>

                    @if(!$notam->is_locked)
                        <form method="POST" action="{{ route('notams.destroy', $notam) }}"
                              onsubmit="return confirm('Delete this NOTAM? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button class="px-3 py-2 rounded-md bg-red-600 text-white text-sm font-semibold hover:bg-red-700">
                                Delete
                            </button>
                        </form>
                    @else
                        <button class="px-3 py-2 rounded-md border text-sm text-gray-400 cursor-not-allowed" disabled>
                            Delete (Locked)
                        </button>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-sidebar-app-layout>
