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

    // ✅ HARD DEFAULT: 1 section + 1 row (prevents a bunch of empty rows)
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

    // ✅ Ensure each section has at least 1 row
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
    <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
        <div class="font-semibold">Fix the following:</div>
        <ul class="mt-2 list-disc pl-5 space-y-1">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if ($locked)
    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
        This record is locked/submitted. Editing is blocked.
    </div>
@endif

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div>
                <div class="text-xs text-slate-500">AirportOps</div>
                <div class="text-lg font-semibold text-slate-900">Daily Checklist & Pass-Along</div>
            </div>

            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold
                {{ ($passAlong?->status ?? 'draft') === 'submitted' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                {{ strtoupper($passAlong?->status ?? 'DRAFT') }}
            </span>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <label class="text-xs font-semibold uppercase tracking-wide text-slate-600">Date</label>
                <input type="date" name="date" value="{{ $value('date', now()->toDateString()) }}"
                       @disabled($locked)
                       class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-slate-900 focus:ring-slate-900" />
            </div>

            <div class="md:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wide text-slate-600">Specialist on Shift</label>
                <input type="text" name="specialist_name" value="{{ $value('specialist_name') }}"
                       @disabled($locked)
                       placeholder="Name"
                       class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-slate-900 focus:ring-slate-900" />
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-600">Start</label>
                    <input type="time" name="shift_start_time" value="{{ $value('shift_start_time') }}"
                           @disabled($locked)
                           class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-slate-900 focus:ring-slate-900" />
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-600">End</label>
                    <input type="time" name="shift_end_time" value="{{ $value('shift_end_time') }}"
                           @disabled($locked)
                           class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-slate-900 focus:ring-slate-900" />
                </div>
            </div>
        </div>
    </div>

    {{-- DAILY TASK CHECKLIST --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="text-sm font-semibold text-slate-900">Daily Task Checklist</div>

        <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
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
                <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-3 hover:bg-slate-50">
                    <input type="hidden" name="{{ $i['key'] }}" value="0">
                    <input type="checkbox" name="{{ $i['key'] }}" value="1"
                           @checked($checked($i['key']))
                           @disabled($locked)
                           class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900" />
                    <span class="text-sm text-slate-800">{{ $i['label'] }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- SIGNIFICANT ACTIVITY --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="text-sm font-semibold text-slate-900">Significant Activity / Watch Items</div>
        <textarea name="significant_activity" rows="5"
                  @disabled($locked)
                  placeholder="What happened, what to watch, NOTAM/work order references, etc."
                  class="mt-3 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-slate-900 focus:ring-slate-900">{{ $value('significant_activity') }}</textarea>
    </div>

    {{-- PASS ALONG ROWS --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <div>
                <div class="text-sm font-semibold text-slate-900">Pass Along (Pages 2–3)</div>
                <div class="text-xs text-slate-500 mt-1">Add only what you need. No wasted space.</div>
            </div>
        </div>

        <div id="sections-root" class="mt-5 space-y-6">
            @foreach($sections as $sIndex => $section)
                @php $rows = $section['rows'] ?? []; @endphp

                <div class="section-block rounded-2xl border border-slate-200 overflow-hidden" data-section-index="{{ $sIndex }}">
                    <div class="bg-slate-50 px-4 py-3 flex items-center justify-between gap-3">
                        <input type="text"
                               name="sections[{{ $sIndex }}][title]"
                               value="{{ $section['title'] ?? 'Pass Along' }}"
                               @disabled($locked)
                               class="w-full rounded-md border-slate-300 text-sm font-semibold shadow-sm focus:border-slate-900 focus:ring-slate-900" />
                        <div class="text-xs text-slate-500 whitespace-nowrap">Section {{ $sIndex + 1 }}</div>
                    </div>

                    <div class="rows-wrap divide-y divide-slate-200">
                        @foreach($rows as $rIndex => $row)
                            @php
                                $k = $rowAttKey($sIndex, $rIndex);
                                $rowAtt = $attachmentsByRow->get($k, collect());
                            @endphp

                            <div class="row-block p-4" data-row-index="{{ $rIndex }}">
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                                    {{-- Item --}}
                                    <div class="md:col-span-3">
                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-600">Item</label>
                                        <input type="text"
                                               name="sections[{{ $sIndex }}][rows][{{ $rIndex }}][label]"
                                               value="{{ $row['label'] ?? '' }}"
                                               @disabled($locked)
                                               placeholder="Runway • ARFF • Weather • Ops Note…"
                                               class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-slate-900 focus:ring-slate-900" />
                                    </div>

                                    {{-- Description --}}
                                    <div class="md:col-span-6">
                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-600">Description</label>
                                        <textarea
                                            name="sections[{{ $sIndex }}][rows][{{ $rIndex }}][value]"
                                            rows="2"
                                            @disabled($locked)
                                            placeholder="Details / location / action taken / notifications…"
                                            class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-slate-900 focus:ring-slate-900">{{ $row['value'] ?? '' }}</textarea>
                                    </div>

                                    {{-- Photo upload --}}
                                    <div class="md:col-span-3">
                                        <div class="flex items-center justify-between gap-2">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600">Photo / File</label>

                                            @unless($locked)
                                                <button type="button"
                                                        class="remove-row-btn text-xs font-semibold text-rose-700 hover:underline"
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
                                               class="mt-1 block w-full text-sm text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-slate-900 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-black" />
                                        <div class="text-[11px] text-slate-500 mt-1">JPG/PNG/WEBP/PDF • up to 12MB each</div>
                                    </div>
                                </div>

                                {{-- Existing attachments for this row --}}
                                @if($rowAtt->count())
                                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($rowAtt as $att)
                                            <div class="rounded-xl border border-slate-200 p-3 flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <div class="text-xs font-semibold text-slate-900 truncate">{{ $att->original_name }}</div>
                                                    <div class="text-[11px] text-slate-500">
                                                        {{ strtoupper($att->mime ?? '') }}
                                                        @if($att->size) • {{ number_format($att->size / 1024, 1) }} KB @endif
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('pass-alongs.attachments.download', [$passAlong, $att]) }}"
                                                       class="text-xs font-semibold text-slate-900 hover:underline">
                                                        Download
                                                    </a>

                                                    @unless($locked)
                                                        <form method="POST" action="{{ route('pass-alongs.attachments.delete', [$passAlong, $att]) }}"
                                                              onsubmit="return confirm('Delete this attachment?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="text-xs font-semibold text-rose-700 hover:underline">Delete</button>
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

                    <div class="px-4 py-3 bg-slate-50 flex items-center justify-between gap-3">
                        <div class="text-xs text-slate-500">
                            Tip: Keep “Item” short. Put the operational reality in “Description.”
                        </div>

                        @unless($locked)
                            <button type="button"
                                    class="add-row-btn rounded-md bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-black">
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
    <div class="row-block p-4" data-row-index="__R__">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
            <div class="md:col-span-3">
                <label class="text-xs font-semibold uppercase tracking-wide text-slate-600">Item</label>
                <input type="text"
                       name="sections[__S__][rows][__R__][label]"
                       value=""
                       placeholder="Runway • ARFF • Weather • Ops Note…"
                       class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-slate-900 focus:ring-slate-900" />
            </div>

            <div class="md:col-span-6">
                <label class="text-xs font-semibold uppercase tracking-wide text-slate-600">Description</label>
                <textarea
                    name="sections[__S__][rows][__R__][value]"
                    rows="2"
                    placeholder="Details / location / action taken / notifications…"
                    class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-slate-900 focus:ring-slate-900"></textarea>
            </div>

            <div class="md:col-span-3">
                <div class="flex items-center justify-between gap-2">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-600">Photo / File</label>
                    <button type="button"
                            class="remove-row-btn text-xs font-semibold text-rose-700 hover:underline"
                            title="Remove this row">
                        Remove
                    </button>
                </div>

                <input type="file"
                       name="row_attachments[__S__][__R__][]"
                       accept=".jpg,.jpeg,.png,.webp,.pdf"
                       multiple
                       class="mt-1 block w-full text-sm text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-slate-900 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-black" />
                <div class="text-[11px] text-slate-500 mt-1">JPG/PNG/WEBP/PDF • up to 12MB each</div>
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
