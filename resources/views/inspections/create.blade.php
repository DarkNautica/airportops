{{-- resources/views/inspections/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Part 139 Daily Safety Self-Inspection
        </h2>
    </x-slot>

    @php
        // Checklist structure (you can edit labels anytime)
        $sections = [
            'PAVEMENT AREAS' => [
                ['key' => 'pavement_lip_over_3', 'label' => 'Pavement lip over 3"'],
                ['key' => 'holes_over_5_dia', 'label' => 'Holes > 5" dia, > 3" deep'],
                ['key' => 'cracks_spalling_bumps', 'label' => 'Cracks / spalling / bumps'],
                ['key' => 'fod', 'label' => 'FOD (gravel, debris, etc.)'],
                ['key' => 'rubber_deposits', 'label' => 'Rubber deposits'],
                ['key' => 'ponding_edge_dams', 'label' => 'Ponding / edge dams'],
            ],
            'SAFETY AREAS' => [
                ['key' => 'ruts_humps_erosion', 'label' => 'Ruts / humps / erosion'],
                ['key' => 'drainage_construction', 'label' => 'Drainage / construction'],
                ['key' => 'objects_frangible_base', 'label' => 'Objects / frangible base'],
            ],
            'MARKINGS / SIGNS' => [
                ['key' => 'visibility_standard', 'label' => 'Visibility standard'],
                ['key' => 'hold_lines_signs', 'label' => 'Hold lines / signs'],
                ['key' => 'frangible_signs', 'label' => 'Frangible signs'],
            ],
            'LIGHTING' => [
                ['key' => 'lighting_obscured_dirty_fading', 'label' => 'Obscured / dirty / fading'],
                ['key' => 'lighting_damaged_missing', 'label' => 'Damaged / missing'],
                ['key' => 'lighting_inoperative', 'label' => 'Inoperative'],
                ['key' => 'lighting_faulty_aim', 'label' => 'Faulty aim / adjustment'],
            ],
            'NAVIGATIONAL AIDS' => [
                ['key' => 'rotating_beacon', 'label' => 'Rotating beacon'],
                ['key' => 'wind_indicators', 'label' => 'Wind indicators'],
                ['key' => 'reils_papi_ils', 'label' => 'REILs / PAPI / ILS systems'],
            ],
            'OBSTRUCTIONS' => [
                ['key' => 'obstruction_lights', 'label' => 'Obstruction lights'],
                ['key' => 'cranes_trees', 'label' => 'Cranes / trees'],
            ],
            'WILDLIFE HAZARDS' => [
                ['key' => 'wildlife_present_location', 'label' => 'Wildlife present / location'],
                ['key' => 'complying_whmp', 'label' => 'Complying with WHMP'],
            ],
            'FUEL FARMS' => [
                ['key' => 'fuel_fencing_gates_signs', 'label' => 'Fencing / gates / signs'],
                ['key' => 'fuel_marking_labeling', 'label' => 'Fuel marking / labeling'],
                ['key' => 'fuel_extinguishers_ground_clips', 'label' => 'Fire exting. / ground clips'],
                ['key' => 'fuel_leaks_vegetation', 'label' => 'Fuel leaks / vegetation'],
            ],
            'SNOW & ICE' => [
                ['key' => 'snow_surface_conditions', 'label' => 'Surface conditions'],
                ['key' => 'snow_bank_clearance', 'label' => 'Snow bank clearance'],
                ['key' => 'snow_lights_signs_obscured', 'label' => 'Lights / signs obscured'],
                ['key' => 'snow_navaids_fire_access', 'label' => 'Navaids / fire access'],
            ],
            'ARFF' => [
                ['key' => 'arff_equipment_crew_availability', 'label' => 'Equipment / crew availability'],
                ['key' => 'arff_response_routes_clear', 'label' => 'Response routes clear'],
            ],
            'PUBLIC PROTECTION' => [
                ['key' => 'public_fencing_gates_signs', 'label' => 'Fencing / gates / signs'],
                ['key' => 'public_unauthorized_persons_vehicles', 'label' => 'Unauthorized persons / veh.'],
            ],
            'CONSTRUCTION' => [
                ['key' => 'construction_barricades_lights', 'label' => 'Barricades / lights'],
                ['key' => 'construction_equipment_parking', 'label' => 'Equipment parking'],
            ],
        ];
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if ($errors->any())
                        <div class="mb-4 p-4 rounded border border-red-300 bg-red-50 text-red-800">
                            <div class="font-semibold mb-2">Fix these fields:</div>
                            <ul class="list-disc pl-5 text-sm space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('inspections.store') }}">
                        @csrf

                        {{-- Header (Printable style) --}}
                        <div class="border rounded-lg p-5">
                            <div class="text-center">
                                <div class="text-lg font-bold tracking-wide">ASHEVILLE REGIONAL AIRPORT</div>
                                <div class="text-base font-semibold">AIRPORT SAFETY SELF-INSPECTION CHECKLIST</div>
                                <div class="text-sm text-gray-600">(FAA Part 139 – Daily Inspection)</div>
                            </div>

                            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Date</label>
                                    <input type="date" name="inspection_date"
                                           value="{{ old('inspection_date', now()->toDateString()) }}"
                                           class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Day</label>
                                    <input type="text" name="inspection_day"
                                           value="{{ old('inspection_day', now()->format('l')) }}"
                                           placeholder="e.g., Thursday"
                                           class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Overall</label>
                                    <select name="overall_status"
                                            class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="Satisfactory" {{ old('overall_status') === 'Satisfactory' ? 'selected' : '' }}>Satisfactory</option>
                                        <option value="Unsatisfactory" {{ old('overall_status') === 'Unsatisfactory' ? 'selected' : '' }}>Unsatisfactory</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Time blocks --}}
                            <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Crash Phone Test (hrs)</label>
                                    <input type="time" name="crash_phone_test_time" value="{{ old('crash_phone_test_time') }}"
                                           class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">AM Inspection (hrs)</label>
                                    <input type="time" name="am_time" value="{{ old('am_time') }}"
                                           class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">PM Inspection (hrs)</label>
                                    <input type="time" name="pm_time" value="{{ old('pm_time') }}"
                                           class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Other Inspection (hrs)</label>
                                    <input type="time" name="other_time" value="{{ old('other_time') }}"
                                           class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                            {{-- By lines --}}
                            <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">By</label>
                                    <input type="text" name="by_1" value="{{ old('by_1', auth()->user()->name ?? '') }}"
                                           class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">By</label>
                                    <input type="text" name="by_2" value="{{ old('by_2') }}"
                                           class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">By</label>
                                    <input type="text" name="by_3" value="{{ old('by_3') }}"
                                           class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">By</label>
                                    <input type="text" name="by_4" value="{{ old('by_4') }}"
                                           class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>

                        {{-- Sections --}}
                        <div class="mt-8 space-y-8">
                            <div class="text-sm font-semibold text-gray-700">
                                FACILITIES / CONDITIONS
                            </div>

                            @foreach ($sections as $sectionTitle => $items)
                                <div class="border rounded-lg overflow-hidden">
                                    <div class="px-4 py-3 bg-gray-50 flex items-center justify-between">
                                        <div class="font-semibold text-gray-800">{{ $sectionTitle }}</div>
                                        <div class="text-xs text-gray-500">Select S / U / N/A for AM and PM.</div>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                                            <thead class="bg-white">
                                                <tr class="text-gray-700">
                                                    <th class="px-4 py-2 text-left font-medium">Item</th>
                                                    <th class="px-4 py-2 text-center font-medium w-56">AM (S / U / N/A)</th>
                                                    <th class="px-4 py-2 text-center font-medium w-56">PM (S / U / N/A)</th>
                                                    <th class="px-4 py-2 text-left font-medium">Remarks</th>
                                                </tr>
                                            </thead>

                                            <tbody class="divide-y divide-gray-200">
                                                @foreach ($items as $item)
                                                    @php
                                                        $amName = "checklist[{$item['key']}][am]";
                                                        $pmName = "checklist[{$item['key']}][pm]";
                                                        $rmName = "checklist[{$item['key']}][remarks]";
                                                    @endphp

                                                    <tr>
                                                        <td class="px-4 py-2 text-gray-900">
                                                            {{ $item['label'] }}
                                                        </td>

                                                        {{-- AM tri-state --}}
                                                        <td class="px-4 py-2 text-center">
                                                            <label class="inline-flex items-center gap-1">
                                                                <input type="radio" name="{{ $amName }}" value="S" {{ old($amName) === 'S' ? 'checked' : '' }}>
                                                                <span class="text-xs">S</span>
                                                            </label>

                                                            <label class="inline-flex items-center gap-1 ml-3">
                                                                <input type="radio" name="{{ $amName }}" value="U" {{ old($amName) === 'U' ? 'checked' : '' }}>
                                                                <span class="text-xs">U</span>
                                                            </label>

                                                            <label class="inline-flex items-center gap-1 ml-3">
                                                                <input type="radio" name="{{ $amName }}" value="NA" {{ old($amName) === 'NA' ? 'checked' : '' }}>
                                                                <span class="text-xs">N/A</span>
                                                            </label>
                                                        </td>

                                                        {{-- PM tri-state --}}
                                                        <td class="px-4 py-2 text-center">
                                                            <label class="inline-flex items-center gap-1">
                                                                <input type="radio" name="{{ $pmName }}" value="S" {{ old($pmName) === 'S' ? 'checked' : '' }}>
                                                                <span class="text-xs">S</span>
                                                            </label>

                                                            <label class="inline-flex items-center gap-1 ml-3">
                                                                <input type="radio" name="{{ $pmName }}" value="U" {{ old($pmName) === 'U' ? 'checked' : '' }}>
                                                                <span class="text-xs">U</span>
                                                            </label>

                                                            <label class="inline-flex items-center gap-1 ml-3">
                                                                <input type="radio" name="{{ $pmName }}" value="NA" {{ old($pmName) === 'NA' ? 'checked' : '' }}>
                                                                <span class="text-xs">N/A</span>
                                                            </label>
                                                        </td>

                                                        <td class="px-4 py-2">
                                                            <input type="text" name="{{ $rmName }}" value="{{ old($rmName) }}"
                                                                   placeholder="Notes / location / action taken..."
                                                                   class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
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
                        <div class="mt-8">
                            <label class="block text-sm font-medium text-gray-700">General Findings / Notes</label>
                            <textarea name="findings" rows="4"
                                      class="mt-1 w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Summary of issues found, NOTAMs entered, work orders created, wildlife actions, etc.">{{ old('findings') }}</textarea>
                        </div>

                        <div class="mt-8 flex items-center justify-end gap-3">
                            <a href="{{ route('inspections.index') }}"
                               class="px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>

                            <button type="submit"
                                    class="px-5 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">
                                Save Inspection
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
