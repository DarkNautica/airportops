{{-- resources/views/work_orders/edit.blade.php --}}
<x-sidebar-app-layout>
    <x-slot name="header">
        <h1 class="font-instrument text-xl text-gray-900">Edit Work Order</h1>
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('work-orders.show', $workOrder) }}"
           class="inline-flex items-center gap-1.5 rounded-lg border border-surface-border bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition">
            View
        </a>
        <a href="{{ route('work-orders.index') }}"
           class="inline-flex items-center gap-1.5 rounded-lg border border-surface-border bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition">
            Back to List
        </a>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                <div class="panel-header px-6 py-4 border-b border-surface-border">
                    <span class="panel-header-label">WORK ORDER DETAILS</span>
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('work-orders.update', $workOrder) }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-5">
                            {{-- Title --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1" for="title">Title</label>
                                <input
                                    id="title"
                                    name="title"
                                    type="text"
                                    value="{{ old('title', $workOrder->title) }}"
                                    class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                >
                                @error('title')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Location --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1" for="location">Location</label>
                                <input
                                    id="location"
                                    name="location"
                                    type="text"
                                    value="{{ old('location', $workOrder->location) }}"
                                    class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                >
                                @error('location')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Priority / Status / Due Date --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1" for="priority">Priority</label>
                                    <select
                                        id="priority"
                                        name="priority"
                                        class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                    >
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

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1" for="status">Status</label>
                                    <select
                                        id="status"
                                        name="status"
                                        class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                    >
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

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1" for="due_date">Due Date</label>
                                    <input
                                        id="due_date"
                                        name="due_date"
                                        type="date"
                                        value="{{ old('due_date', optional($workOrder->due_date)->format('Y-m-d')) }}"
                                        class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                    >
                                    @error('due_date')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Description --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1" for="description">Description</label>
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="4"
                                    class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                >{{ old('description', $workOrder->description) }}</textarea>
                                @error('description')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-surface-border">
                            <a href="{{ route('work-orders.index') }}"
                               class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                Cancel
                            </a>

                            <button type="submit"
                                    class="inline-flex items-center rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-black transition">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-sidebar-app-layout>
