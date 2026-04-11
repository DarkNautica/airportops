<x-sidebar-app-layout>
    <x-slot name="header">
        <h1 class="font-instrument text-xl text-gray-900">Users</h1>
        <p class="text-sm text-gray-500 mt-1">Manage accounts, titles, and roles.</p>
    </x-slot>

    <x-slot name="actions">
        @can('users.create')
            @can('roles.manage')
                <a href="{{ route('admin.users.create') }}"
                   class="inline-flex items-center px-3 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-black transition">
                    + Create User
                </a>
            @endcan
        @endcan
    </x-slot>

    <div class="space-y-6">

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-900 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Search --}}
        <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
            <div class="p-4">
                <form method="GET" class="flex flex-col sm:flex-row gap-2 sm:items-center">
                    <input name="q" value="{{ $q }}"
                           class="w-full sm:max-w-md rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                           placeholder="Search name, email, title..." />
                    <div class="flex gap-2">
                        <button class="inline-flex items-center px-3 py-2.5 rounded-lg border border-surface-border bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Search</button>
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-3 py-2.5 rounded-lg border border-surface-border bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
            <div class="panel-header">
                <span class="panel-header-label">USER ACCOUNTS</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-surface-border">
                            <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Name</th>
                            <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Email</th>
                            <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Title</th>
                            <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Roles</th>
                            <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-surface-border">
                        @forelse($users as $u)
                            <tr class="hover:bg-[#F8FAFC]">
                                <td class="px-4 py-2.5 font-medium text-sm text-gray-900">{{ $u->name }}</td>
                                <td class="px-4 py-2.5 text-sm text-gray-700">{{ $u->email }}</td>
                                <td class="px-4 py-2.5 text-sm text-gray-700">{{ $u->title ?? '—' }}</td>
                                <td class="px-4 py-2.5">
                                    @php
                                        $roles = $u->roles->pluck('name')->values();

                                        $roleBadge = function($name) {
                                            return match (strtolower($name)) {
                                                'admin' => 'critical',
                                                'ops supervisor' => 'in_progress',
                                                'ops staff' => 'open',
                                                'viewer' => 'neutral',
                                                default => 'neutral',
                                            };
                                        };
                                    @endphp
                                    @if($roles->isEmpty())
                                        <span class="text-gray-400">—</span>
                                    @else
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($roles as $r)
                                                <x-badge variant="{{ $roleBadge($r) }}">{{ $r }}</x-badge>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    @can('users.update')
                                        <a href="{{ route('admin.users.edit', $u) }}" class="text-[#2563EB] hover:text-[#3B82F6] text-sm font-medium">
                                            Edit
                                        </a>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-gray-500 text-sm">
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-surface-border bg-white">
                {{ $users->links() }}
            </div>
        </div>

    </div>
</x-sidebar-app-layout>
