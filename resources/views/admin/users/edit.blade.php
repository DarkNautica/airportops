<x-sidebar-app-layout>
    <x-slot name="header">
        <h1 class="font-instrument text-xl text-gray-900">Edit User</h1>
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('admin.users.index') }}" class="text-[#2563EB] hover:text-[#3B82F6] text-sm font-medium">&larr; Back to Users</a>
    </x-slot>

    <div class="max-w-3xl space-y-6">

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-900 text-sm">
                {{ session('success') }}
            </div>
        @endif

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

        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            {{-- User Details --}}
            <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                <div class="panel-header">
                    <span class="panel-header-label">USER DETAILS</span>
                </div>

                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input name="name" value="{{ old('name', $user->name) }}"
                               class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input name="email" value="{{ old('email', $user->email) }}"
                               class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
                        @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input name="title" value="{{ old('title', $user->title) }}"
                               placeholder="e.g. Ops Technician, Ops Supervisor, Airport Ops Manager"
                               class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
                        @error('title')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password (optional)</label>
                        <input name="password" type="password"
                               class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
                        <div class="text-xs text-gray-500 mt-1">Leave blank to keep existing password.</div>
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
                    <div class="text-xs text-gray-500 mb-3">Only users with <span class="font-semibold">roles.manage</span> can change roles.</div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($roles as $role)
                            <label class="flex items-center gap-2.5 rounded-lg border border-surface-border p-3 hover:bg-[#F8FAFC] transition cursor-pointer {{ !$canManageRoles ? 'opacity-60' : '' }}">
                                <input type="checkbox" name="roles[]"
                                       value="{{ $role->name }}"
                                       @checked(in_array($role->name, old('roles', $userRoleNames)))
                                       @disabled(!$canManageRoles)
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500/20" />
                                <span class="text-sm text-gray-800">{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>

                    @unless($canManageRoles)
                        <div class="mt-3 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                            You don't have permission to manage roles.
                        </div>
                    @endunless
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-6 flex items-center justify-between">
                <a href="{{ route('admin.users.index') }}" class="text-[#2563EB] hover:text-[#3B82F6] text-sm font-medium">&larr; Back</a>
                <button type="submit"
                        class="inline-flex items-center px-5 py-2.5 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-black transition">
                    Save
                </button>
            </div>
        </form>

        {{-- Danger Zone --}}
        @can('users.delete')
            <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden border-l-4 border-l-red-400">
                <div class="p-6">
                    <div class="font-semibold text-red-800">Danger Zone</div>
                    <div class="text-sm text-red-700 mt-1">Delete this user account permanently.</div>

                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-4"
                          onsubmit="return confirm('Delete this user? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button class="inline-flex items-center px-4 py-2.5 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition">
                            Delete User
                        </button>
                    </form>
                </div>
            </div>
        @endcan

    </div>
</x-sidebar-app-layout>
