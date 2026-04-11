<x-sidebar-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="font-instrument text-xl text-gray-900">Create NOTAM</h1>
            <a href="{{ route('notams.index') }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-surface-border px-3.5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                <div class="panel-header">
                    <span class="panel-header-label">NOTAM DETAILS</span>
                </div>

                <form method="POST" action="{{ route('notams.store') }}" class="p-5 space-y-5">
                    @csrf

                    @if ($errors->any())
                        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            <div class="font-semibold">Fix the following:</div>
                            <ul class="list-disc pl-5 mt-2 space-y-1">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Station</label>
                            <input name="station" value="{{ old('station', 'KAVL') }}"
                                   class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            @error('station')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status"
                                    class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                @foreach (['Draft','Active','Expired','Cancelled'] as $opt)
                                    <option value="{{ $opt }}" @selected(old('status','Draft') === $opt)>{{ $opt }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="category"
                                    class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                @foreach (['Runway','Taxiway','Apron/Ramp','Lighting','NAVAID','Obstruction/Crane','Construction','Other'] as $opt)
                                    <option value="{{ $opt }}" @selected(old('category','Other') === $opt)>{{ $opt }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                            <input name="subject" value="{{ old('subject') }}"
                                   placeholder="RWY 17/35 EDGE LIGHTS OTS"
                                   class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            @error('subject')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Effective From (Z)</label>
                            <input type="datetime-local" name="effective_from"
                                   value="{{ old('effective_from') }}"
                                   class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            <div class="text-xs text-slate-400 mt-1">Use UTC times.</div>
                            @error('effective_from')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Effective To (Z)</label>
                            <input type="datetime-local" name="effective_to"
                                   value="{{ old('effective_to') }}"
                                   class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            @error('effective_to')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NOTAM Text</label>
                        <textarea name="notam_text" rows="7"
                                  placeholder="Paste the full NOTAM text here (as filed)."
                                  class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">{{ old('notam_text') }}</textarea>
                        <div class="text-xs text-slate-400 mt-1">
                            Keep it exact. We'll use this to reconcile against official NOTAM sources.
                        </div>
                        @error('notam_text')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <a href="{{ route('notams.index') }}"
                           class="inline-flex items-center rounded-lg border border-surface-border px-3.5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center rounded-lg bg-blue-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition-colors">
                            Save NOTAM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-sidebar-app-layout>
