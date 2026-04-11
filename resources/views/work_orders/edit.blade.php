<x-sidebar-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Work Order
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('work-orders.update', $workOrder) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700" for="title">Title</label>
                            <input id="title" name="title" type="text"
                                   value="{{ old('title', $workOrder->title) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('title')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700" for="location">Location</label>
                            <input id="location" name="location" type="text"
                                   value="{{ old('location', $workOrder->location) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('location')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700" for="priority">Priority</label>
                            <select id="priority" name="priority"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @foreach (\App\Enums\WorkOrderPriority::all() as $priority)
                                    <option value="{{ $priority }}"
                                        @selected(old('priority', $workOrder->priority) === $priority)>
                                        {{ $priority }}
                                    </option>
                                @endforeach
                            </select>
                            @error('priority')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700" for="status">Status</label>
                            <select id="status" name="status"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @foreach (\App\Enums\WorkOrderStatus::all() as $status)
                                    <option value="{{ $status }}"
                                        @selected(old('status', $workOrder->status) === $status)>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700" for="due_date">Due Date</label>
                            <input id="due_date" name="due_date" type="date"
                                   value="{{ old('due_date', optional($workOrder->due_date)->format('Y-m-d')) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('due_date')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700" for="description">Description</label>
                            <textarea id="description" name="description" rows="4"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $workOrder->description) }}</textarea>
                            @error('description')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ route('work-orders.index') }}"
                               class="text-sm text-gray-600 hover:text-gray-900">
                                Cancel
                            </a>

                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm text-white hover:bg-blue-700">
                                Save Changes
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-sidebar-app-layout>
