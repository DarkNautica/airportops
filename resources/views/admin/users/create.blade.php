<x-sidebar-app-layout>
    <x-slot name="header">
        <h1 class="font-instrument text-xl text-gray-900">Create User</h1>
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('admin.users.index') }}" class="text-[#2563EB] hover:text-[#3B82F6] text-sm font-medium">&larr; Back to Users</a>
    </x-slot>

    <div class="max-w-3xl space-y-6">

        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-900 text-sm">
                <div class="font-semibold">Fix the following:</div>
                <ul class="list-disc pl-5 mt-2 space-y-1">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            {{-- User Details --}}
            <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                <div class="panel-header">
                    <span class="panel-header-label">USER DETAILS</span>
                </div>

                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input name="name" value="{{ old('name') }}"
                               class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input name="email" value="{{ old('email') }}"
                               class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
                        @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input name="title" value="{{ old('title') }}"
                               class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
                        @error('title')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input name="password" type="password"
                               class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
                        <div class="text-xs text-gray-500 mt-1">Minimum 10 characters.</div>
                        @error('password')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Roles --}}
            <div class="mt-6 bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                <div class="panel-header">
                    <span class="panel-header-label">ROLES</span>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($roles as $role)
                            <label class="flex items-center gap-2.5 rounded-lg border border-surface-border p-3 hover:bg-[#F8FAFC] transition cursor-pointer">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                       @checked(in_array($role->name, old('roles', [])))
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500/20" />
                                <span class="text-sm text-gray-800">{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-6 flex items-center justify-between">
                <a href="{{ route('admin.users.index') }}" class="text-[#2563EB] hover:text-[#3B82F6] text-sm font-medium">&larr; Back</a>
                <button type="submit"
                        class="inline-flex items-center px-5 py-2.5 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-black transition">
                    Create User
                </button>
            </div>
        </form>

    </div>
</x-sidebar-app-layout>
