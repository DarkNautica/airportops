<x-sidebar-app-layout>
    <x-slot name="header">
        <div>
            <div class="text-xs text-gray-500">Admin</div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create User</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

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

            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="text-sm font-medium text-gray-700">Name</label>
                        <input name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-md border-gray-300" />
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Email</label>
                        <input name="email" value="{{ old('email') }}" class="mt-1 w-full rounded-md border-gray-300" />
                        @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Title</label>
                        <input name="title" value="{{ old('title') }}" class="mt-1 w-full rounded-md border-gray-300" />
                        @error('title')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Password</label>
                        <input name="password" type="password" class="mt-1 w-full rounded-md border-gray-300" />
                        <div class="text-xs text-gray-500 mt-1">Minimum 10 characters.</div>
                        @error('password')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <div class="text-sm font-semibold text-gray-900">Roles</div>
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($roles as $role)
                                <label class="flex items-center gap-2 rounded-md border border-gray-200 p-2">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                           @checked(in_array($role->name, old('roles', []))) />
                                    <span class="text-sm text-gray-800">{{ $role->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-between">
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-700 hover:underline">← Back</a>
                        <button class="px-4 py-2 rounded-md bg-gray-900 text-white text-sm hover:bg-black">
                            Create User
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-sidebar-app-layout>
