@php
    // When creating, controller passes $defaultSections.
    // When editing, the model already has sections.
    $sections = old('sections', $passAlong->sections ?? $defaultSections ?? []);
@endphp

<div class="space-y-6">

    {{-- Top meta row (matches PDF page 1) --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Date</label>
            <input type="date"
                   name="date"
                   value="{{ old('date', optional($passAlong->date ?? null)->format('Y-m-d')) }}"
                   class="mt-1 w-full rounded border-gray-300" required>
            @error('date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Specialist on Shift (Name)</label>
            <input type="text"
                   name="specialist_name"
                   value="{{ old('specialist_name', $passAlong->specialist_name ?? '') }}"
                   class="mt-1 w-full rounded border-gray-300"
                   placeholder="Name">
            @error('specialist_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700">Start Time</label>
                <input type="time"
                       name="shift_start_time"
                       value="{{ old('shift_start_time', $passAlong->shift_start_time ?? '') }}"
                       class="mt-1 w-full rounded border-gray-300">
                @error('shift_start_time') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">End Time</label>
                <input type="time"
                       name="shift_end_time"
                       value="{{ old('shift_end_time', $passAlong->shift_end_time ?? '') }}"
                       class="mt-1 w-full rounded border-gray-300">
                @error('shift_end_time') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Significant Activity / Watch Items --}}
    <div>
        <label class="block text-sm font-medium text-gray-700">Significant Activity / Watch Items</label>
        <textarea name="significant_activity"
                  rows="4"
                  class="mt-1 w-full rounded border-gray-300"
                  placeholder="Enter significant activity, watch items, notable conditions...">{{ old('significant_activity', $passAlong->significant_activity ?? '') }}</textarea>
        @error('significant_activity') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Daily Task Checklist (page 1) --}}
    <div class="rounded border border-gray-200 p-4">
        <div class="font-semibold text-gray-800 mb-3">Daily Task Checklist</div>

        @php
            $checks = [
                'am_part_139'       => 'AM Part-139 Inspection',
                'am_perimeter'      => 'AM Perimeter Inspection',
                'am_terminal'       => 'AM Terminal Inspection',
                'pm_part_139'       => 'PM Part-139 Inspection',
                'pm_terminal'       => 'PM Terminal Inspection',
                'ramp_apron_patrol' => 'Ramp/Apron Patrol',
                'wildlife_patrol'   => 'Wildlife Patrol',
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($checks as $field => $label)
                <label class="flex items-center gap-3 p-2 rounded hover:bg-gray-50">
                    <input type="hidden" name="{{ $field }}" value="0">
                    <input type="checkbox"
                           name="{{ $field }}"
                           value="1"
                           class="rounded border-gray-300"
                           {{ old($field, $passAlong->{$field} ?? false) ? 'checked' : '' }}>
                    <span class="text-sm text-gray-800">{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Pages 2–3 blank grids (structured rows) --}}
    <div class="rounded border border-gray-200 p-4">
        <div class="font-semibold text-gray-800 mb-3">Pass Along (Pages 2–3)</div>

        <div class="space-y-6">
            @foreach($sections as $sIndex => $section)
                <div class="rounded border border-gray-200 p-3">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div class="font-medium text-gray-800">
                            <input type="text"
                                   name="sections[{{ $sIndex }}][title]"
                                   value="{{ old("sections.$sIndex.title", $section['title'] ?? 'Pass Along') }}"
                                   class="w-full rounded border-gray-300"
                                   placeholder="Section title">
                            @error("sections.$sIndex.title") <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        @php $rows = $section['rows'] ?? []; @endphp

                        @foreach($rows as $rIndex => $row)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div class="md:col-span-1">
                                    <input type="text"
                                           name="sections[{{ $sIndex }}][rows][{{ $rIndex }}][label]"
                                           value="{{ old("sections.$sIndex.rows.$rIndex.label", $row['label'] ?? '') }}"
                                           class="w-full rounded border-gray-300"
                                           placeholder="Label (optional)">
                                </div>

                                <div class="md:col-span-2">
                                    <input type="text"
                                           name="sections[{{ $sIndex }}][rows][{{ $rIndex }}][value]"
                                           value="{{ old("sections.$sIndex.rows.$rIndex.value", $row['value'] ?? '') }}"
                                           class="w-full rounded border-gray-300"
                                           placeholder="Value / notes">
                                </div>
                            </div>
                        @endforeach

                        @error("sections.$sIndex.rows") <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            @endforeach
        </div>

        @error('sections') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
    </div>

</div>
