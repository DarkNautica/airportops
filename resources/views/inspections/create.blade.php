{{-- resources/views/inspections/create.blade.php --}}
<x-sidebar-app-layout>
    <x-slot name="header">
        <h1 class="font-instrument text-xl text-gray-900">New Inspection</h1>
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('inspections.index') }}"
           class="inline-flex items-center gap-1 px-3 py-2 rounded-md border border-gray-300 bg-white text-gray-800 text-sm font-semibold hover:bg-gray-50 shadow-sm">
            &larr; Back
        </a>
    </x-slot>

    @php
        $sections = config('checklist');
    @endphp

    <div class="min-h-screen bg-surface pb-10">
        <div class="w-full px-6 lg:px-10 py-8 space-y-6">

            @if ($errors->any())
                <div class="bg-white rounded-xl shadow-card overflow-hidden border-l-4 border-l-red-400">
                    <div class="px-5 py-4">
                        <div class="text-sm font-semibold text-gray-900 mb-2">Fix these fields:</div>
                        <ul class="list-disc pl-5 text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('inspections.store') }}">
                @csrf

                {{-- Header card --}}
                <div class="c139-card">
                    <div class="panel-header">
                        <span class="panel-header-label">Inspection Details</span>
                    </div>

                    <div class="p-5 space-y-6">
                        <div class="text-center">
                            <div class="font-instrument text-lg text-gray-900">ASHEVILLE REGIONAL AIRPORT</div>
                            <div class="text-sm font-semibold text-gray-700">AIRPORT SAFETY SELF-INSPECTION CHECKLIST</div>
                            <div class="text-xs text-slate-500">(FAA Part 139 -- Daily Inspection)</div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" name="inspection_date"
                                       value="{{ old('inspection_date', now()->toDateString()) }}"
                                       class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Day</label>
                                <input type="text" name="inspection_day"
                                       value="{{ old('inspection_day', now()->format('l')) }}"
                                       placeholder="e.g., Thursday"
                                       class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Overall</label>
                                <select name="overall_status"
                                        class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                                    <option value="Satisfactory" {{ old('overall_status') === 'Satisfactory' ? 'selected' : '' }}>Satisfactory</option>
                                    <option value="Unsatisfactory" {{ old('overall_status') === 'Unsatisfactory' ? 'selected' : '' }}>Unsatisfactory</option>
                                </select>
                            </div>
                        </div>

                        {{-- Time blocks --}}
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Crash Phone Test (hrs)</label>
                                <input type="time" name="crash_phone_test_time" value="{{ old('crash_phone_test_time') }}"
                                       class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">AM Inspection (hrs)</label>
                                <input type="time" name="am_time" value="{{ old('am_time') }}"
                                       class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">PM Inspection (hrs)</label>
                                <input type="time" name="pm_time" value="{{ old('pm_time') }}"
                                       class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Other Inspection (hrs)</label>
                                <input type="time" name="other_time" value="{{ old('other_time') }}"
                                       class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                            </div>
                        </div>

                        {{-- By lines --}}
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">By</label>
                                <input type="text" name="by_1" value="{{ old('by_1', auth()->user()->name ?? '') }}"
                                       class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">By</label>
                                <input type="text" name="by_2" value="{{ old('by_2') }}"
                                       class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">By</label>
                                <input type="text" name="by_3" value="{{ old('by_3') }}"
                                       class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">By</label>
                                <input type="text" name="by_4" value="{{ old('by_4') }}"
                                       class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sections --}}
                <div class="mt-6 space-y-6">
                    <h2 class="font-instrument text-lg text-gray-900">Facilities / Conditions</h2>

                    @foreach ($sections as $sectionTitle => $items)
                        <div class="c139-card">
                            <div class="panel-header">
                                <span class="font-instrument text-base text-gray-900">{{ $sectionTitle }}</span>
                                <span class="font-mono text-[10px] text-slate-500">S / U / N/A for AM and PM</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="c139-table w-full">
                                    <thead>
                                        <tr>
                                            <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Item</th>
                                            <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-center w-56">AM (S / U / N/A)</th>
                                            <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-center w-56">PM (S / U / N/A)</th>
                                            <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Remarks</th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-surface-border">
                                        @foreach ($items as $item)
                                            @php
                                                $amName = "checklist[{$item['key']}][am]";
                                                $pmName = "checklist[{$item['key']}][pm]";
                                                $rmName = "checklist[{$item['key']}][remarks]";
                                            @endphp

                                            <tr class="h-11 hover:bg-[#F8FAFC] transition-colors">
                                                <td class="px-4 py-2.5 text-sm text-gray-900">
                                                    {{ $item['label'] }}
                                                </td>

                                                {{-- AM tri-state --}}
                                                <td class="px-4 py-2.5 text-center">
                                                    <label class="inline-flex items-center gap-1 cursor-pointer">
                                                        <input type="radio" name="{{ $amName }}" value="S" {{ old($amName) === 'S' ? 'checked' : '' }}
                                                               class="text-emerald-600 focus:ring-emerald-500/20">
                                                        <span class="text-xs font-medium text-gray-700">S</span>
                                                    </label>

                                                    <label class="inline-flex items-center gap-1 ml-3 cursor-pointer">
                                                        <input type="radio" name="{{ $amName }}" value="U" {{ old($amName) === 'U' ? 'checked' : '' }}
                                                               class="text-red-600 focus:ring-red-500/20">
                                                        <span class="text-xs font-medium text-gray-700">U</span>
                                                    </label>

                                                    <label class="inline-flex items-center gap-1 ml-3 cursor-pointer">
                                                        <input type="radio" name="{{ $amName }}" value="NA" {{ old($amName) === 'NA' ? 'checked' : '' }}
                                                               class="text-slate-500 focus:ring-slate-500/20">
                                                        <span class="text-xs font-medium text-gray-700">N/A</span>
                                                    </label>
                                                </td>

                                                {{-- PM tri-state --}}
                                                <td class="px-4 py-2.5 text-center">
                                                    <label class="inline-flex items-center gap-1 cursor-pointer">
                                                        <input type="radio" name="{{ $pmName }}" value="S" {{ old($pmName) === 'S' ? 'checked' : '' }}
                                                               class="text-emerald-600 focus:ring-emerald-500/20">
                                                        <span class="text-xs font-medium text-gray-700">S</span>
                                                    </label>

                                                    <label class="inline-flex items-center gap-1 ml-3 cursor-pointer">
                                                        <input type="radio" name="{{ $pmName }}" value="U" {{ old($pmName) === 'U' ? 'checked' : '' }}
                                                               class="text-red-600 focus:ring-red-500/20">
                                                        <span class="text-xs font-medium text-gray-700">U</span>
                                                    </label>

                                                    <label class="inline-flex items-center gap-1 ml-3 cursor-pointer">
                                                        <input type="radio" name="{{ $pmName }}" value="NA" {{ old($pmName) === 'NA' ? 'checked' : '' }}
                                                               class="text-slate-500 focus:ring-slate-500/20">
                                                        <span class="text-xs font-medium text-gray-700">N/A</span>
                                                    </label>
                                                </td>

                                                <td class="px-4 py-2.5">
                                                    <input type="text" name="{{ $rmName }}" value="{{ old($rmName) }}"
                                                           placeholder="Notes / location / action taken..."
                                                           class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Footer notes --}}
                <div class="mt-6 c139-card">
                    <div class="panel-header">
                        <span class="panel-header-label">General Findings / Notes</span>
                    </div>
                    <div class="p-5">
                        <textarea name="findings" rows="4"
                                  class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                                  placeholder="Summary of issues found, NOTAMs entered, work orders created, wildlife actions, etc.">{{ old('findings') }}</textarea>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <a href="{{ route('inspections.index') }}"
                       class="px-4 py-2.5 rounded-lg border border-surface-border text-sm font-semibold text-gray-700 hover:bg-[#F8FAFC] transition">
                        Cancel
                    </a>

                    <button type="submit"
                            class="px-5 py-2.5 rounded-lg bg-gray-900 text-white text-sm font-semibold hover:bg-black shadow-sm transition">
                        Save Inspection
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-sidebar-app-layout>
