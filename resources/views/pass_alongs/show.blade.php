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
    <x-slot name="header">
        <h1 class="font-instrument text-xl text-gray-900">
            Pass Along <span class="font-mono text-[11px] text-slate-400">#{{ $passAlong->id }}</span>
        </h1>
    </x-slot>

    <x-slot name="actions">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('pass-alongs.index') }}"
               class="inline-flex items-center px-3 py-2 rounded-md border border-surface-border bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm">
                &larr; Back
            </a>

            <a href="{{ route('pass-alongs.print', $passAlong) }}"
               target="_blank"
               class="inline-flex items-center px-3 py-2 rounded-md border border-surface-border bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm">
                Print
            </a>

            <a href="{{ route('pass-alongs.pdf', $passAlong) }}"
               class="inline-flex items-center px-3 py-2 rounded-md border border-surface-border bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm">
                PDF
            </a>

            @can('update', $passAlong)
                @if(!$passAlong->is_locked)
                    <a href="{{ route('pass-alongs.edit', $passAlong) }}"
                       class="inline-flex items-center px-3 py-2 rounded-md bg-gray-900 text-sm font-semibold text-white hover:bg-black shadow-sm">
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
                                class="inline-flex items-center px-3 py-2 rounded-md bg-emerald-700 text-sm font-semibold text-white hover:bg-emerald-600 shadow-sm">
                            Submit &amp; Lock
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
                                class="inline-flex items-center px-3 py-2 rounded-md bg-red-600 text-sm font-semibold text-white hover:bg-red-700 shadow-sm">
                            Unlock
                        </button>
                    </form>
                @endif
            @endcan
        </div>
    </x-slot>

    <div class="min-h-screen bg-surface pb-10">
        <div class="w-full px-6 lg:px-10 py-8 space-y-6">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="c139-card border-l-4 border-l-emerald-400">
                    <div class="p-4">
                        <div class="font-semibold text-emerald-900">Success</div>
                        <div class="text-sm text-emerald-800 mt-1">{{ session('success') }}</div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="c139-card border-l-4 border-l-red-400">
                    <div class="p-4">
                        <div class="font-semibold text-red-900">Blocked</div>
                        <div class="text-sm text-red-800 mt-1">{{ session('error') }}</div>
                    </div>
                </div>
            @endif

            {{-- Summary Card --}}
            <div class="c139-card">
                <div class="panel-header">
                    <span class="panel-header-label">SUMMARY</span>
                    <div class="flex items-center gap-2">
                        @if($passAlong->is_locked)
                            <x-badge variant="certified">Submitted</x-badge>
                            <x-badge variant="locked">Locked</x-badge>
                        @else
                            <x-badge variant="draft">Draft</x-badge>
                            <x-badge variant="open">Editable</x-badge>
                        @endif
                    </div>
                </div>

                <div class="p-5">
                    {{-- Lock details --}}
                    @if($passAlong->is_locked)
                        <div class="mb-4 text-sm text-slate-500">
                            <span class="font-mono text-[11px]">{{ $passAlong->locked_at?->format('Y-m-d H:i') ?? '—' }}</span>
                            &middot;
                            @if(method_exists($passAlong, 'lockedBy'))
                                by {{ optional($passAlong->lockedBy)->name ?? '—' }}
                            @else
                                by {{ $passAlong->locked_by ?? '—' }}
                            @endif
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Date</div>
                            <div class="mt-1 font-semibold text-gray-900 font-mono text-[11px]">
                                {{ optional($passAlong->date)->format('Y-m-d') ?? '—' }}
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Specialist on Shift</div>
                            <div class="mt-1 font-semibold text-gray-900">
                                {{ $passAlong->specialist_name ?? '—' }}
                            </div>
                        </div>

                        <div>
                            <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Shift</div>
                            <div class="mt-1 font-semibold text-gray-900 font-mono text-[11px]">
                                {{ $passAlong->shift_start_time ?? '—' }} &rarr; {{ $passAlong->shift_end_time ?? '—' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Daily Checklist --}}
            <div class="c139-card">
                <div class="panel-header">
                    <span class="panel-header-label">DAILY TASK CHECKLIST</span>
                </div>

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

                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    @foreach($items as $key => $label)
                        <div class="flex items-center gap-3 rounded-lg border border-surface-border px-3 py-2.5">
                            @if($passAlong->$key)
                                <span class="inline-flex h-5 w-5 items-center justify-center rounded bg-emerald-100 text-emerald-700">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                </span>
                            @else
                                <span class="inline-flex h-5 w-5 items-center justify-center rounded bg-slate-100 text-slate-400">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </span>
                            @endif
                            <span class="text-gray-800">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Significant Activity --}}
            <div class="c139-card">
                <div class="panel-header">
                    <span class="panel-header-label">SIGNIFICANT ACTIVITY / WATCH ITEMS</span>
                </div>
                <div class="p-5">
                    <div class="whitespace-pre-wrap text-sm text-gray-800">
                        {{ $passAlong->significant_activity ?: '—' }}
                    </div>
                </div>
            </div>

            {{-- Pass Along Sections --}}
            <div class="c139-card">
                <div class="panel-header">
                    <span class="panel-header-label">PASS ALONG DETAILS (PAGES 2-3)</span>
                    <span class="font-mono text-[11px] text-slate-400">Attachments appear beside their row as download buttons.</span>
                </div>

                @php $sections = $passAlong->sections ?? []; @endphp

                @if(empty($sections))
                    <div class="p-5 text-sm text-gray-500">—</div>
                @else
                    <div class="divide-y divide-surface-border">
                        @foreach($sections as $si => $section)
                            <div>
                                {{-- Section sub-header --}}
                                <div class="px-5 py-3 bg-[#F8FAFC] border-b border-surface-border">
                                    <span class="font-instrument text-base text-gray-900">{{ $section['title'] ?? 'Pass Along' }}</span>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="c139-table min-w-full text-sm">
                                        <thead>
                                            <tr class="border-b border-surface-border">
                                                <th class="w-[28%]">Item</th>
                                                <th>Description</th>
                                                <th class="w-[22%]">Attachment(s)</th>
                                            </tr>
                                        </thead>

                                        <tbody class="divide-y divide-gray-100">
                                            @foreach(($section['rows'] ?? []) as $ri => $row)
                                                @php
                                                    $label = trim((string)($row['label'] ?? ''));
                                                    $desc  = trim((string)($row['value'] ?? ''));
                                                    $rowAtt = $attachmentsByRow->get($rowKey($si, $ri), collect());
                                                @endphp

                                                @if($label !== '' || $desc !== '' || $rowAtt->count())
                                                    <tr class="align-top">
                                                        <td>
                                                            <div class="font-semibold text-gray-900">
                                                                {{ $label !== '' ? $label : '—' }}
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <div class="text-gray-700 whitespace-pre-wrap">
                                                                {{ $desc !== '' ? $desc : '—' }}
                                                            </div>
                                                        </td>

                                                        <td>
                                                            @if($rowAtt->count())
                                                                <div class="flex flex-col gap-2">
                                                                    @foreach($rowAtt as $att)
                                                                        <div class="flex items-center justify-between gap-2 rounded-lg border border-surface-border bg-white px-3 py-2">
                                                                            <div class="min-w-0">
                                                                                <div class="text-xs font-semibold text-gray-900 truncate" title="{{ $att->original_name }}">
                                                                                    {{ $att->original_name }}
                                                                                </div>
                                                                                <div class="font-mono text-[11px] text-slate-500">
                                                                                    {{ strtoupper($att->mime ?? '') }}
                                                                                    @if($att->size) &middot; {{ number_format($att->size / 1024, 1) }} KB @endif
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

            {{-- All Attachments --}}
            <div class="c139-card">
                <div class="panel-header">
                    <span class="panel-header-label">ALL ATTACHMENTS</span>
                    <span class="font-mono text-[11px] text-slate-400">{{ $passAlong->attachments->count() }} files</span>
                </div>

                @if($passAlong->attachments->count())
                    <ul class="divide-y divide-gray-100">
                        @foreach($passAlong->attachments as $file)
                            <li class="px-5 py-3 flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="font-medium text-gray-900 truncate text-sm">
                                        {{ $file->original_name }}
                                    </div>
                                    <div class="font-mono text-[11px] text-slate-500">
                                        Uploaded {{ $file->created_at->format('Y-m-d H:i') }}
                                        by {{ optional($file->uploader)->name ?? '—' }}
                                        @if(!is_null($file->section_index) && !is_null($file->row_index))
                                            &middot; row: {{ $file->section_index + 1 }}.{{ $file->row_index + 1 }}
                                        @endif
                                    </div>
                                </div>

                                <a href="{{ route('pass-alongs.attachments.download', ['pass_along' => $passAlong, 'attachment' => $file]) }}"
                                   class="shrink-0 rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">
                                    Download
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="p-5 text-sm text-gray-500">No attachments uploaded.</div>
                @endif
            </div>

        </div>
    </div>
</x-layouts.sidebar-app>
