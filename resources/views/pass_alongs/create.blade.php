<x-layouts.sidebar-app>
    <x-slot name="header">
        <h1 class="font-instrument text-xl text-gray-900">New Pass Along</h1>
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('pass-alongs.index') }}"
           class="inline-flex items-center px-3 py-2 rounded-md border border-surface-border bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm">
            &larr; Back to Pass Alongs
        </a>
    </x-slot>

    <div class="min-h-screen bg-surface pb-10">
        <div class="w-full px-6 lg:px-10 py-8 space-y-6">

            <div class="c139-card">
                <div class="panel-header">
                    <span class="panel-header-label">PASS ALONG DETAILS</span>
                </div>

                <div class="p-6">
                    <form method="POST"
                          action="{{ route('pass-alongs.store') }}"
                          enctype="multipart/form-data"
                          class="space-y-6">
                        @csrf

                        @include('pass_alongs._form', [
                            'passAlong' => null,
                            'defaultSections' => $defaultSections
                        ])

                        <div class="flex flex-wrap items-center justify-end gap-3 pt-4 border-t border-surface-border">
                            <a href="{{ route('pass-alongs.index') }}"
                               class="inline-flex items-center px-4 py-2.5 rounded-lg border border-surface-border bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>

                            <button type="submit"
                                    class="inline-flex items-center px-5 py-2.5 rounded-lg bg-gray-900 text-sm font-semibold text-white hover:bg-black shadow-sm">
                                Save Draft
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-layouts.sidebar-app>
