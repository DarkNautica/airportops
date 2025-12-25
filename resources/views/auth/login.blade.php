<x-guest-layout>
    <div class="flex min-h-screen bg-white dark:bg-gray-900">

        <!-- LEFT: LOGIN PANEL -->
        <div class="flex flex-1 flex-col justify-center px-8 py-12 sm:px-16 lg:px-24 xl:px-32">
            <div class="mx-auto w-full max-w-xl text-center">

                <!-- HEADER (TEXT ONLY) -->
                <div class="mb-10">
                    <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                        AirportOps
                    </h1>

                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                        Authorized personnel only.
                    </p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-6" :status="session('status')" />

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-6 text-left">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-900 dark:text-gray-100">
                            Email address
                        </label>
                        <div class="mt-2">
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                class="block w-full rounded-md bg-white px-3 py-2.5 text-base text-gray-900
                                       outline outline-1 outline-gray-300 placeholder:text-gray-400
                                       focus:outline-2 focus:outline-indigo-600
                                       dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-900 dark:text-gray-100">
                            Password
                        </label>
                        <div class="mt-2">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                class="block w-full rounded-md bg-white px-3 py-2.5 text-base text-gray-900
                                       outline outline-1 outline-gray-300 placeholder:text-gray-400
                                       focus:outline-2 focus:outline-indigo-600
                                       dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember / Forgot -->
                    <div class="flex items-center justify-between gap-6">
                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input
                                type="checkbox"
                                name="remember"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <span>Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a
                                href="{{ route('password.request') }}"
                                class="text-sm font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400"
                            >
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <!-- Submit -->
                    <div>
                        <button
                            type="submit"
                            class="flex w-full justify-center rounded-md bg-indigo-600 px-4 py-2.5
                                   text-sm font-semibold text-white shadow
                                   hover:bg-indigo-500 focus-visible:outline-2
                                   focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                        >
                            Sign in
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- RIGHT: IMAGE PANEL -->
        <div class="relative hidden w-0 flex-1 lg:block">
            <img
                src="{{ asset('images/airfield-aerial.jpg') }}"
                alt="Airfield"
                class="absolute inset-0 h-full w-full object-cover"
            />
            <div class="absolute inset-0 bg-gray-900/25"></div>
        </div>

    </div>
</x-guest-layout>
