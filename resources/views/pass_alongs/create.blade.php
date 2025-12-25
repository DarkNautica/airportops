<x-layouts.sidebar-app>
    <x-slot name="title">New Pass Along</x-slot>

    <x-slot name="actions">
        <a href="{{ route('pass-alongs.index') }}"
           class="rounded-md border px-3 py-2 text-sm font-semibold hover:bg-gray-50">
            ← Back to Pass Alongs
        </a>
    </x-slot>

    <div class="bg-white rounded-xl p-6 shadow">
        <form method="POST"
              action="{{ route('pass-alongs.store') }}"
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf

            @include('pass_alongs._form', [
                'passAlong' => null,
                'defaultSections' => $defaultSections
            ])

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('pass-alongs.index') }}"
                   class="rounded-md border px-4 py-2 text-sm font-semibold hover:bg-gray-50">
                    Cancel
                </a>

                <button type="submit"
                        class="rounded-md bg-gray-900 px-5 py-2 text-sm font-semibold text-white hover:bg-black">
                    Save Draft
                </button>
            </div>
        </form>
    </div>
</x-layouts.sidebar-app>
