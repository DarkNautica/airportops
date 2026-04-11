{{-- resources/views/pass_alongs/edit.blade.php --}}
<x-layouts.sidebar-app>
    <x-slot name="header">
        <h1 class="font-instrument text-xl text-gray-900">Edit Pass Along <span class="font-mono text-[11px] text-slate-400">#{{ $passAlong->id }}</span></h1>
    </x-slot>

    <x-slot name="actions">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('pass-alongs.show', $passAlong) }}"
               class="inline-flex items-center px-3 py-2 rounded-md border border-surface-border bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm">
                &larr; Back
            </a>

            <a href="{{ route('pass-alongs.index') }}"
               class="inline-flex items-center px-3 py-2 rounded-md border border-surface-border bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm">
                All Pass Alongs
            </a>

            @if($passAlong->is_locked)
                <x-badge variant="locked">Locked (Submitted)</x-badge>
            @endif
        </div>
    </x-slot>

    <div class="min-h-screen bg-surface pb-10">
        <div class="w-full px-6 lg:px-10 py-8 space-y-6">

            {{-- Locked Banner --}}
            @if($passAlong->is_locked)
                <div class="c139-card border-l-4 border-l-emerald-400">
                    <div class="p-4">
                        <div class="font-semibold text-emerald-900">This Pass Along is locked.</div>
                        <div class="text-sm text-emerald-800 mt-1">
                            It was submitted on {{ $passAlong->submitted_at?->format('Y-m-d H:i') ?? '—' }}.
                            Only an authorized user can unlock it to edit.
                        </div>
                    </div>
                </div>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="c139-card border-l-4 border-l-red-400">
                    <div class="p-4">
                        <div class="font-semibold text-red-900">Fix these fields:</div>
                        <ul class="mt-2 list-disc pl-5 text-sm text-red-800 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="c139-card">
                <div class="panel-header">
                    <div>
                        <span class="panel-header-label">EDIT DRAFT</span>
                        <div class="font-mono text-[11px] text-slate-400 mt-0.5">AirportOps &middot; Pass Along</div>
                    </div>
                    <span class="font-mono text-[11px] text-slate-400">
                        Last updated: {{ $passAlong->updated_at?->format('Y-m-d H:i') ?? '—' }}
                    </span>
                </div>

                <div class="p-6">
                    <form method="POST"
                          action="{{ route('pass-alongs.update', $passAlong) }}"
                          enctype="multipart/form-data"
                          class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- enctype is REQUIRED for file uploads --}}
                        @include('pass_alongs._form', [
                            'passAlong' => $passAlong,
                            'defaultSections' => [],
                        ])

                        <div class="flex flex-wrap items-center justify-end gap-3 pt-4 border-t border-surface-border">
                            <a href="{{ route('pass-alongs.show', $passAlong) }}"
                               class="inline-flex items-center px-4 py-2.5 rounded-lg border border-surface-border bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>

                            <button type="submit"
                                    @disabled($passAlong->is_locked)
                                    class="inline-flex items-center px-5 py-2.5 rounded-lg bg-gray-900 text-sm font-semibold text-white hover:bg-black shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-layouts.sidebar-app>
