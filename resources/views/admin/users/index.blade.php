<x-sidebar-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Users</h2>
                <p class="text-sm text-gray-500 mt-1">Manage accounts, titles, and roles.</p>
            </div>

            <div class="flex items-center gap-2">
                @can('users.create')
                    @can('roles.manage')
                        <a href="{{ route('admin.users.create') }}"
                           class="px-3 py-2 rounded-md bg-gray-900 text-white text-sm hover:bg-black">
                            + New User
                        </a>
                    @endcan
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-900 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <form method="GET" class="flex flex-col sm:flex-row gap-2 sm:items-center">
                    <input name="q" value="{{ $q }}"
                           class="w-full sm:max-w-md rounded-md border-gray-300"
                           placeholder="Search name, email, title…" />
                    <div class="flex gap-2">
                        <button class="px-3 py-2 rounded-md border text-sm hover:bg-gray-50">Search</button>
                        <a href="{{ route('admin.users.index') }}" class="px-3 py-2 rounded-md border text-sm hover:bg-gray-50">Reset</a>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Name</th>
                            <th class="px-4 py-3 text-left font-semibold">Email</th>
                            <th class="px-4 py-3 text-left font-semibold">Title</th>
                            <th class="px-4 py-3 text-left font-semibold">Roles</th>
                            <th class="px-4 py-3 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse($users as $u)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-900">{{ $u->name }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $u->email }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $u->title ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @php $roles = $u->roles->pluck('name')->values(); @endphp
                                    @if($roles->isEmpty())
                                        <span class="text-gray-500">—</span>
                                    @else
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($roles as $r)
                                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-gray-100 text-gray-800">
                                                    {{ $r }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @can('users.update')
                                        <a href="{{ route('admin.users.edit', $u) }}" class="text-blue-700 hover:underline">
                                            Edit
                                        </a>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-sidebar-app-layout>
