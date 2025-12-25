{{-- resources/views/pass_alongs/edit.blade.php --}}
<x-layouts.sidebar-app>
    <x-slot name="title">Edit Pass Along #{{ $passAlong->id }}</x-slot>

    <x-slot name="actions">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('pass-alongs.show', $passAlong) }}"
               class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-semibold shadow-sm hover:bg-gray-50">
                ← Back
            </a>

            <a href="{{ route('pass-alongs.index') }}"
               class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-semibold shadow-sm hover:bg-gray-50">
                All Pass Alongs
            </a>

            @if($passAlong->is_locked)
                <span class="inline-flex items-center rounded-md bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-800 ring-1 ring-inset ring-emerald-600/20">
                    Locked (Submitted)
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-10 bg-gradient-to-b from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Banner --}}
            @if($passAlong->is_locked)
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900 shadow-sm">
                    <div class="font-semibold">This Pass Along is locked.</div>
                    <div class="text-sm mt-1">
                        It was submitted on {{ $passAlong->submitted_at?->format('Y-m-d H:i') ?? '—' }}.
                        Only an authorized user can unlock it to edit.
                    </div>
                </div>
            @endif

            {{-- Validation --}}
            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-900 shadow-sm">
                    <div class="font-semibold">Fix these fields:</div>
                    <ul class="mt-2 list-disc pl-5 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 bg-white">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <div class="text-xs text-gray-500">AirportOps • Pass Along</div>
                            <div class="text-base font-semibold text-gray-900">Edit Draft</div>
                        </div>

                        <div class="text-xs text-gray-500">
                            Last updated: {{ $passAlong->updated_at?->format('Y-m-d H:i') ?? '—' }}
                        </div>
                    </div>
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

                        <div class="flex flex-wrap items-center justify-end gap-3 pt-4 border-t border-gray-200">
                            <a href="{{ route('pass-alongs.show', $passAlong) }}"
                               class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold hover:bg-gray-50">
                                Cancel
                            </a>

                            <button type="submit"
                                    @disabled($passAlong->is_locked)
                                    class="rounded-md bg-gray-900 px-5 py-2 text-sm font-semibold text-white hover:bg-black disabled:opacity-50 disabled:cursor-not-allowed">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-layouts.sidebar-app>
