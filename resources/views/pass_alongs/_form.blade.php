{{-- resources/views/pass_alongs/_form.blade.php --}}
@php
    /** @var \App\Models\PassAlong|null $passAlong */
    $isEdit = !is_null($passAlong);
    $locked = $isEdit && ($passAlong->is_locked || $passAlong->status === 'submitted');

    $value = function(string $key, $default = null) use ($passAlong) {
        if (old($key) !== null) return old($key);
        if (!$passAlong) return $default;
        return data_get($passAlong, $key, $default);
    };

    // Pull sections from old() first, then model, then default.
    $sections = old('sections');
    if ($sections === null) $sections = $passAlong?->sections;

    // HARD DEFAULT: 1 section + 1 row (prevents a bunch of empty rows)
    if (empty($sections) || !is_array($sections)) {
        $sections = [
            [
                'title' => 'Pass Along',
                'rows' => [
                    ['label' => '', 'value' => ''],
                ],
            ],
        ];
    }

    // Ensure each section has at least 1 row
    foreach ($sections as $i => $sec) {
        if (!isset($sections[$i]['rows']) || !is_array($sections[$i]['rows']) || count($sections[$i]['rows']) === 0) {
            $sections[$i]['rows'] = [['label' => '', 'value' => '']];
        }
    }

    $checked = function(string $key) use ($value) {
        return (bool) $value($key, false);
    };

    // Attachments grouped by row coords: "section:row" => collection
    $attachmentsByRow = collect();
    if ($passAlong && $passAlong->relationLoaded('attachments')) {
        $attachmentsByRow = $passAlong->attachments
            ->groupBy(fn($a) => (string)($a->section_index ?? 'null').':'.(string)($a->row_index ?? 'null'));
    }

    $rowAttKey = fn($s, $r) => (string)$s.':'.(string)$r;
@endphp

@if ($errors->any())
    <div class="c139-card border-l-4 border-l-red-400 mb-6">
        <div class="p-4">
            <div class="font-semibold text-red-900">Fix the following:</div>
            <ul class="mt-2 list-disc pl-5 text-sm text-red-800 space-y-1">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

@if ($locked)
    <div class="c139-card border-l-4 border-l-amber-400 mb-6">
        <div class="p-4 text-sm text-amber-900">
            This record is locked/submitted. Editing is blocked.
        </div>
    </div>
@endif

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="c139-card">
        <div class="panel-header">
            <div>
                <span class="panel-header-label">DAILY CHECKLIST & PASS-ALONG</span>
                <div class="font-mono text-[11px] text-slate-400 mt-0.5">AirportOps</div>
            </div>
            @if($passAlong)
                <x-badge variant="{{ ($passAlong->status ?? 'draft') === 'submitted' ? 'certified' : 'draft' }}">
                    {{ strtoupper($passAlong->status ?? 'DRAFT') }}
                </x-badge>
            @else
                <x-badge variant="draft">DRAFT</x-badge>
            @endif
        </div>

        <div class="p-5 grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="date" value="{{ $value('date', now()->toDateString()) }}"
                       @disabled($locked)
                       class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Specialist on Shift</label>
                <input type="text" name="specialist_name" value="{{ $value('specialist_name') }}"
                       @disabled($locked)
                       placeholder="Name"
                       class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start</label>
                    <input type="time" name="shift_start_time" value="{{ $value('shift_start_time') }}"
                           @disabled($locked)
                           class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End</label>
                    <input type="time" name="shift_end_time" value="{{ $value('shift_end_time') }}"
                           @disabled($locked)
                           class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
                </div>
            </div>
        </div>
    </div>

    {{-- DAILY TASK CHECKLIST --}}
    <div class="c139-card">
        <div class="panel-header">
            <span class="panel-header-label">DAILY TASK CHECKLIST</span>
        </div>

        <div class="p-5 grid grid-cols-1 gap-3 md:grid-cols-2">
            @php
                $items = [
                    ['key'=>'am_part_139', 'label'=>'AM Part-139 Inspection'],
                    ['key'=>'am_perimeter', 'label'=>'AM Perimeter Inspection'],
                    ['key'=>'am_terminal', 'label'=>'AM Terminal Inspection'],
                    ['key'=>'pm_part_139', 'label'=>'PM Part-139 Inspection'],
                    ['key'=>'pm_terminal', 'label'=>'PM Terminal Inspection'],
                    ['key'=>'ramp_apron_patrol', 'label'=>'Ramp/Apron Patrol'],
                    ['key'=>'wildlife_patrol', 'label'=>'Wildlife Patrol'],
                ];
            @endphp

            @foreach($items as $i)
                <label class="flex items-center gap-3 rounded-lg border border-surface-border bg-white px-3 py-2.5 hover:bg-[#F8FAFC] cursor-pointer transition-colors">
                    <input type="hidden" name="{{ $i['key'] }}" value="0">
                    <input type="checkbox" name="{{ $i['key'] }}" value="1"
                           @checked($checked($i['key']))
                           @disabled($locked)
                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500/20" />
                    <span class="text-sm text-gray-800">{{ $i['label'] }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- SIGNIFICANT ACTIVITY --}}
    <div class="c139-card">
        <div class="panel-header">
            <span class="panel-header-label">SIGNIFICANT ACTIVITY / WATCH ITEMS</span>
        </div>

        <div class="p-5">
            <textarea name="significant_activity" rows="5"
                      @disabled($locked)
                      placeholder="What happened, what to watch, NOTAM/work order references, etc."
                      class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">{{ $value('significant_activity') }}</textarea>
        </div>
    </div>

    {{-- PASS ALONG ROWS --}}
    <div class="c139-card">
        <div class="panel-header">
            <div>
                <span class="panel-header-label">PASS ALONG (PAGES 2-3)</span>
                <div class="font-mono text-[11px] text-slate-400 mt-0.5">Add only what you need. No wasted space.</div>
            </div>
        </div>

        <div id="sections-root" class="divide-y divide-surface-border">
            @foreach($sections as $sIndex => $section)
                @php $rows = $section['rows'] ?? []; @endphp

                <div class="section-block" data-section-index="{{ $sIndex }}">
                    <div class="bg-[#F8FAFC] px-5 py-3 flex items-center justify-between gap-3 border-b border-surface-border">
                        <input type="text"
                               name="sections[{{ $sIndex }}][title]"
                               value="{{ $section['title'] ?? 'Pass Along' }}"
                               @disabled($locked)
                               class="w-full rounded-lg border border-surface-border bg-white px-3 py-2 text-sm font-semibold focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
                        <div class="font-mono text-[11px] text-slate-400 whitespace-nowrap">Section {{ $sIndex + 1 }}</div>
                    </div>

                    <div class="rows-wrap divide-y divide-gray-100">
                        @foreach($rows as $rIndex => $row)
                            @php
                                $k = $rowAttKey($sIndex, $rIndex);
                                $rowAtt = $attachmentsByRow->get($k, collect());
                            @endphp

                            <div class="row-block p-5" data-row-index="{{ $rIndex }}">
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                                    {{-- Item --}}
                                    <div class="md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Item</label>
                                        <input type="text"
                                               name="sections[{{ $sIndex }}][rows][{{ $rIndex }}][label]"
                                               value="{{ $row['label'] ?? '' }}"
                                               @disabled($locked)
                                               placeholder="Runway / ARFF / Weather / Ops Note..."
                                               class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
                                    </div>

                                    {{-- Description --}}
                                    <div class="md:col-span-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea
                                            name="sections[{{ $sIndex }}][rows][{{ $rIndex }}][value]"
                                            rows="2"
                                            @disabled($locked)
                                            placeholder="Details / location / action taken / notifications..."
                                            class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">{{ $row['value'] ?? '' }}</textarea>
                                    </div>

                                    {{-- Photo upload --}}
                                    <div class="md:col-span-3">
                                        <div class="flex items-center justify-between gap-2 mb-1">
                                            <label class="block text-sm font-medium text-gray-700">Photo / File</label>

                                            @unless($locked)
                                                <button type="button"
                                                        class="remove-row-btn text-xs font-semibold text-red-600 hover:underline"
                                                        title="Remove this row">
                                                    Remove
                                                </button>
                                            @endunless
                                        </div>

                                        <input type="file"
                                               name="row_attachments[{{ $sIndex }}][{{ $rIndex }}][]"
                                               @disabled($locked)
                                               accept=".jpg,.jpeg,.png,.webp,.pdf"
                                               multiple
                                               class="block w-full text-sm text-gray-700 file:mr-3 file:rounded-lg file:border-0 file:bg-gray-900 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-black" />
                                        <div class="font-mono text-[11px] text-slate-400 mt-1">JPG/PNG/WEBP/PDF up to 12MB each</div>
                                    </div>
                                </div>

                                {{-- Existing attachments for this row --}}
                                @if($rowAtt->count())
                                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($rowAtt as $att)
                                            <div class="rounded-lg border border-surface-border p-3 flex items-start justify-between gap-3 bg-white">
                                                <div class="min-w-0">
                                                    <div class="text-xs font-semibold text-gray-900 truncate">{{ $att->original_name }}</div>
                                                    <div class="font-mono text-[11px] text-slate-400">
                                                        {{ strtoupper($att->mime ?? '') }}
                                                        @if($att->size) &middot; {{ number_format($att->size / 1024, 1) }} KB @endif
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('pass-alongs.attachments.download', [$passAlong, $att]) }}"
                                                       class="text-xs font-semibold text-gray-900 hover:underline">
                                                        Download
                                                    </a>

                                                    @unless($locked)
                                                        <form method="POST" action="{{ route('pass-alongs.attachments.delete', [$passAlong, $att]) }}"
                                                              onsubmit="return confirm('Delete this attachment?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="text-xs font-semibold text-red-600 hover:underline">Delete</button>
                                                        </form>
                                                    @endunless
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="px-5 py-3 bg-[#F8FAFC] flex items-center justify-between gap-3 border-t border-surface-border">
                        <div class="font-mono text-[11px] text-slate-400">
                            Tip: Keep "Item" short. Put the operational reality in "Description."
                        </div>

                        @unless($locked)
                            <button type="button"
                                    class="add-row-btn inline-flex items-center px-3 py-2 rounded-lg bg-gray-900 text-xs font-semibold text-white hover:bg-black shadow-sm">
                                + Add Row
                            </button>
                        @endunless
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

{{-- TEMPLATE for new rows (JS clones + rewrites indexes) --}}
<template id="row-template">
    <div class="row-block p-5" data-row-index="__R__">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Item</label>
                <input type="text"
                       name="sections[__S__][rows][__R__][label]"
                       value=""
                       placeholder="Runway / ARFF / Weather / Ops Note..."
                       class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
            </div>

            <div class="md:col-span-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea
                    name="sections[__S__][rows][__R__][value]"
                    rows="2"
                    placeholder="Details / location / action taken / notifications..."
                    class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></textarea>
            </div>

            <div class="md:col-span-3">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <label class="block text-sm font-medium text-gray-700">Photo / File</label>
                    <button type="button"
                            class="remove-row-btn text-xs font-semibold text-red-600 hover:underline"
                            title="Remove this row">
                        Remove
                    </button>
                </div>

                <input type="file"
                       name="row_attachments[__S__][__R__][]"
                       accept=".jpg,.jpeg,.png,.webp,.pdf"
                       multiple
                       class="block w-full text-sm text-gray-700 file:mr-3 file:rounded-lg file:border-0 file:bg-gray-900 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-black" />
                <div class="font-mono text-[11px] text-slate-400 mt-1">JPG/PNG/WEBP/PDF up to 12MB each</div>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('click', function (e) {
    // Add Row
    if (e.target && e.target.classList.contains('add-row-btn')) {
        const sectionBlock = e.target.closest('.section-block');
        const sIndex = sectionBlock.getAttribute('data-section-index');
        const rowsWrap = sectionBlock.querySelector('.rows-wrap');

        const existingRows = rowsWrap.querySelectorAll('.row-block');
        const nextR = existingRows.length;

        const tpl = document.getElementById('row-template').innerHTML
            .replaceAll('__S__', sIndex)
            .replaceAll('__R__', String(nextR));

        rowsWrap.insertAdjacentHTML('beforeend', tpl);
        return;
    }

    // Remove Row (never allow removing last row)
    if (e.target && e.target.classList.contains('remove-row-btn')) {
        const sectionBlock = e.target.closest('.section-block');
        const rowsWrap = sectionBlock.querySelector('.rows-wrap');
        const rows = rowsWrap.querySelectorAll('.row-block');

        if (rows.length <= 1) {
            alert('You need at least one row.');
            return;
        }

        const row = e.target.closest('.row-block');
        row.remove();
        return;
    }
});
</script>
