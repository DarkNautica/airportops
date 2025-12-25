{{-- resources/views/pass_alongs/show.blade.php --}}
@php
    // Map attachments by row coordinate "section:row"
    $attachmentsByRow = collect();

    if ($passAlong->relationLoaded('attachments')) {
        $attachmentsByRow = $passAlong->attachments
            ->groupBy(fn($a) => (string)($a->section_index ?? 'null') . ':' . (string)($a->row_index ?? 'null'));
    }

    $rowKey = fn($s, $r) => (string)$s . ':' . (string)$r;
@endphp

<x-layouts.sidebar-app>
    <x-slot name="title">
        Pass Along #{{ $passAlong->id }}
    </x-slot>

    <x-slot name="actions">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('pass-alongs.index') }}"
               class="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                ← Back
            </a>

            <a href="{{ route('pass-alongs.print', $passAlong) }}"
               target="_blank"
               class="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Print
            </a>

            <a href="{{ route('pass-alongs.pdf', $passAlong) }}"
               class="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                PDF
            </a>

            @can('update', $passAlong)
                @if(!$passAlong->is_locked)
                    <a href="{{ route('pass-alongs.edit', $passAlong) }}"
                       class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Edit
                    </a>
                @endif
            @endcan

            @can('submit', $passAlong)
                @if($passAlong->status !== 'submitted')
                    <form method="POST" action="{{ route('pass-alongs.submit', $passAlong) }}">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Submit and lock this pass along?');"
                                class="rounded-md bg-emerald-700 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-600">
                            Submit & Lock
                        </button>
                    </form>
                @endif
            @endcan

            @can('unlock', $passAlong)
                @if($passAlong->is_locked)
                    <form method="POST" action="{{ route('pass-alongs.unlock', $passAlong) }}">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Unlock this pass along?');"
                                class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                            Unlock
                        </button>
                    </form>
                @endif
            @endcan
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- FLASH --}}
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900 shadow-sm">
                <div class="font-semibold">Success</div>
                <div class="text-sm mt-1">{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-900 shadow-sm">
                <div class="font-semibold">Blocked</div>
                <div class="text-sm mt-1">{{ session('error') }}</div>
            </div>
        @endif

        {{-- LOCK STATUS --}}
        <div class="rounded-xl border bg-white p-4 shadow-sm">
            <div class="flex flex-wrap items-center gap-3 text-sm">
                <span class="font-semibold text-gray-700">Status:</span>

                @if($passAlong->is_locked)
                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-800">
                        SUBMITTED • LOCKED
                    </span>
                    <span class="text-xs text-gray-500">
                        {{ $passAlong->locked_at?->format('Y-m-d H:i') ?? '—' }}
                        @if(method_exists($passAlong, 'lockedBy'))
                            by {{ optional($passAlong->lockedBy)->name ?? '—' }}
                        @else
                            by {{ $passAlong->locked_by ?? '—' }}
                        @endif
                    </span>
                @else
                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-800">
                        DRAFT • EDITABLE
                    </span>
                @endif
            </div>
        </div>

        {{-- HEADER INFO --}}
        <div class="rounded-xl border bg-white p-6 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <div class="text-gray-500">Date</div>
                    <div class="font-semibold text-gray-900">
                        {{ optional($passAlong->date)->format('Y-m-d') ?? '—' }}
                    </div>
                </div>

                <div class="md:col-span-2">
                    <div class="text-gray-500">Specialist on Shift</div>
                    <div class="font-semibold text-gray-900">
                        {{ $passAlong->specialist_name ?? '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-gray-500">Shift</div>
                    <div class="font-semibold text-gray-900">
                        {{ $passAlong->shift_start_time ?? '—' }} → {{ $passAlong->shift_end_time ?? '—' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- DAILY CHECKLIST --}}
        <div class="rounded-xl border bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Daily Task Checklist</h3>

            @php
                $items = [
                    'am_part_139' => 'AM Part-139 Inspection',
                    'am_perimeter' => 'AM Perimeter Inspection',
                    'am_terminal' => 'AM Terminal Inspection',
                    'pm_part_139' => 'PM Part-139 Inspection',
                    'pm_terminal' => 'PM Terminal Inspection',
                    'ramp_apron_patrol' => 'Ramp / Apron Patrol',
                    'wildlife_patrol' => 'Wildlife Patrol',
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                @foreach($items as $key => $label)
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-5 w-5 items-center justify-center rounded border border-gray-300 text-xs font-bold">
                            {{ $passAlong->$key ? '✓' : '' }}
                        </span>
                        <span class="text-gray-800">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- SIGNIFICANT ACTIVITY --}}
        <div class="rounded-xl border bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900 mb-2">Significant Activity / Watch Items</h3>
            <div class="whitespace-pre-wrap text-sm text-gray-800">
                {{ $passAlong->significant_activity ?: '—' }}
            </div>
        </div>

        {{-- PASS ALONG SECTIONS --}}
        <div class="rounded-xl border bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-3 mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Pass Along Details (Pages 2–3)</h3>
                <div class="text-xs text-gray-500">Attachments appear beside their row as download buttons.</div>
            </div>

            @php $sections = $passAlong->sections ?? []; @endphp

            @if(empty($sections))
                <p class="text-sm text-gray-500">—</p>
            @else
                <div class="space-y-6">
                    @foreach($sections as $si => $section)
                        <div class="rounded-xl border border-gray-200 overflow-hidden">
                            <div class="px-4 py-3 bg-gray-50 font-semibold text-gray-900">
                                {{ $section['title'] ?? 'Pass Along' }}
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-white">
                                        <tr class="border-b">
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-[28%]">Item</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Description</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-[22%]">Attachment(s)</th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y">
                                        @foreach(($section['rows'] ?? []) as $ri => $row)
                                            @php
                                                $label = trim((string)($row['label'] ?? ''));
                                                $desc  = trim((string)($row['value'] ?? ''));
                                                $rowAtt = $attachmentsByRow->get($rowKey($si, $ri), collect());
                                            @endphp

                                            @if($label !== '' || $desc !== '' || $rowAtt->count())
                                                <tr class="align-top">
                                                    <td class="px-4 py-3">
                                                        <div class="font-semibold text-gray-900">
                                                            {{ $label !== '' ? $label : '—' }}
                                                        </div>
                                                    </td>

                                                    <td class="px-4 py-3">
                                                        <div class="text-gray-700 whitespace-pre-wrap">
                                                            {{ $desc !== '' ? $desc : '—' }}
                                                        </div>
                                                    </td>

                                                    <td class="px-4 py-3">
                                                        @if($rowAtt->count())
                                                            <div class="flex flex-col gap-2">
                                                                @foreach($rowAtt as $att)
                                                                    <div class="flex items-center justify-between gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2">
                                                                        <div class="min-w-0">
                                                                            <div class="text-xs font-semibold text-gray-900 truncate" title="{{ $att->original_name }}">
                                                                                {{ $att->original_name }}
                                                                            </div>
                                                                            <div class="text-[11px] text-gray-500">
                                                                                {{ strtoupper($att->mime ?? '') }}
                                                                                @if($att->size) • {{ number_format($att->size / 1024, 1) }} KB @endif
                                                                            </div>
                                                                        </div>

                                                                        <a href="{{ route('pass-alongs.attachments.download', ['pass_along' => $passAlong, 'attachment' => $att]) }}"
                                                                           class="shrink-0 rounded-md bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-black">
                                                                            Download
                                                                        </a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <span class="text-xs text-gray-400">—</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- GENERAL ATTACHMENTS (optional but useful) --}}
        <div class="rounded-xl border bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">All Attachments</h3>

            @if($passAlong->attachments->count())
                <ul class="divide-y text-sm">
                    @foreach($passAlong->attachments as $file)
                        <li class="py-3 flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <div class="font-medium text-gray-900 truncate">
                                    {{ $file->original_name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Uploaded {{ $file->created_at->format('Y-m-d H:i') }}
                                    by {{ optional($file->uploader)->name ?? '—' }}
                                    @if(!is_null($file->section_index) && !is_null($file->row_index))
                                        • row: {{ $file->section_index + 1 }}.{{ $file->row_index + 1 }}
                                    @endif
                                </div>
                            </div>

                            <a href="{{ route('pass-alongs.attachments.download', ['pass_along' => $passAlong, 'attachment' => $file]) }}"
                               class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
                                Download
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-sm text-gray-500">No attachments uploaded.</div>
            @endif
        </div>

    </div>
</x-layouts.sidebar-app>
