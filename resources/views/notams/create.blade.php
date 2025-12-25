<x-sidebar-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create NOTAM</h2>
                <p class="text-sm text-gray-500 mt-1">Draft internally. We’ll sync against official sources later.</p>
            </div>

            <a href="{{ route('notams.index') }}"
               class="px-3 py-2 rounded-md border text-sm text-gray-700 hover:bg-gray-50">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <div class="font-semibold text-gray-900">NOTAM Details</div>
                    <div class="text-xs text-gray-500">Station defaults to KAVL.</div>
                </div>

                <form method="POST" action="{{ route('notams.store') }}" class="p-5 space-y-5">
                    @csrf

                    @if ($errors->any())
                        <div class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-900">
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
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Station</label>
                            <input name="station" value="{{ old('station', 'KAVL') }}"
                                   class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900 text-sm">
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</label>
                            <select name="status"
                                    class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900 text-sm">
                                @foreach (['Draft','Active','Expired','Cancelled'] as $opt)
                                    <option value="{{ $opt }}" @selected(old('status','Draft') === $opt)>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Category</label>
                            <select name="category"
                                    class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900 text-sm">
                                @foreach (['Runway','Taxiway','Apron/Ramp','Lighting','NAVAID','Obstruction/Crane','Construction','Other'] as $opt)
                                    <option value="{{ $opt }}" @selected(old('category','Other') === $opt)>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Subject</label>
                            <input name="subject" value="{{ old('subject') }}"
                                   placeholder="RWY 17/35 EDGE LIGHTS OTS"
                                   class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900 text-sm">
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Effective From (Z)</label>
                            <input type="datetime-local" name="effective_from"
                                   value="{{ old('effective_from') }}"
                                   class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900 text-sm">
                            <div class="text-xs text-gray-500 mt-1">Use UTC times.</div>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Effective To (Z)</label>
                            <input type="datetime-local" name="effective_to"
                                   value="{{ old('effective_to') }}"
                                   class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">NOTAM Text</label>
                        <textarea name="notam_text" rows="7"
                                  placeholder="Paste the full NOTAM text here (as filed)."
                                  class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900 text-sm font-mono">{{ old('notam_text') }}</textarea>
                        <div class="text-xs text-gray-500 mt-1">
                            Keep it exact. We’ll use this to reconcile against official NOTAM sources.
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('notams.index') }}"
                           class="px-3 py-2 rounded-md border text-sm text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-3 py-2 rounded-md bg-gray-900 text-white text-sm font-semibold hover:bg-black">
                            Save NOTAM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-sidebar-app-layout>
