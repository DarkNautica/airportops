{{-- resources/views/work_orders/_form.blade.php --}}

@csrf

<div class="mb-4">
    <label class="block text-sm font-medium mb-1">Title</label>
    <input type="text" name="title"
           value="{{ old('title', $workOrder->title ?? '') }}"
           class="w-full border rounded px-3 py-2">
    @error('title')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium mb-1">Description</label>
    <textarea name="description" rows="4"
              class="w-full border rounded px-3 py-2">{{ old('description', $workOrder->description ?? '') }}</textarea>
    @error('description')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label class="block text-sm font-medium mb-1">Location</label>
    <input type="text" name="location"
           value="{{ old('location', $workOrder->location ?? '') }}"
           class="w-full border rounded px-3 py-2">
    @error('location')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4 flex gap-4">
    <div class="flex-1">
        <label class="block text-sm font-medium mb-1">Priority</label>
        <select name="priority" class="w-full border rounded px-3 py-2">
            @php
                $priorities = ['Low', 'Medium', 'High', 'Critical'];
            @endphp
            @foreach($priorities as $p)
                <option value="{{ $p }}"
                    @selected(old('priority', $workOrder->priority ?? 'Medium') === $p)>
                    {{ $p }}
                </option>
            @endforeach
        </select>
        @error('priority')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex-1">
        <label class="block text-sm font-medium mb-1">Status</label>
        <select name="status" class="w-full border rounded px-3 py-2">
            @php
                $statuses = ['Open', 'In Progress', 'On Hold', 'Completed', 'Cancelled'];
            @endphp
            @foreach($statuses as $s)
                <option value="{{ $s }}"
                    @selected(old('status', $workOrder->status ?? 'Open') === $s)>
                    {{ $s }}
                </option>
            @endforeach
        </select>
        @error('status')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mb-4">
    <label class="block text-sm font-medium mb-1">Due Date</label>
    <input type="date" name="due_date"
           value="{{ old('due_date', isset($workOrder->due_date) ? $workOrder->due_date->format('Y-m-d') : '') }}"
           class="w-full border rounded px-3 py-2">
    @error('due_date')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="flex justify-end gap-2">
    <a href="{{ route('work-orders.index') }}"
       class="px-4 py-2 border rounded">
        Cancel
    </a>
    <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded">
        Save
    </button>
</div>
