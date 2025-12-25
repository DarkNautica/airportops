{{-- resources/views/work_orders/create.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Work Order') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <form method="POST" action="{{ route('work-orders.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Title
                        </label>
                        <input
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            required
                        >
                        @error('title')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Description
                        </label>
                        <textarea
                            name="description"
                            rows="4"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        >{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Location
                        </label>
                        <input
                            type="text"
                            name="location"
                            value="{{ old('location') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        >
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Priority
                            </label>
                            <select
                                name="priority"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                required
                            >
                                <option value="Low">Low</option>
                                <option value="Normal" selected>Normal</option>
                                <option value="High">High</option>
                                <option value="Critical">Critical</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Status
                            </label>
                            <select
                                name="status"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                required
                            >
                                <option value="Open" selected>Open</option>
                                <option value="In Progress">In Progress</option>
                                <option value="On Hold">On Hold</option>
                                <option value="Closed">Closed</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Due Date
                            </label>
                            <input
                                type="date"
                                name="due_date"
                                value="{{ old('due_date') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            >
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a
                            href="{{ route('work-orders.index') }}"
                            class="text-gray-600 hover:underline"
                        >
                            Cancel
                        </a>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                        >
                            Create Work Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
