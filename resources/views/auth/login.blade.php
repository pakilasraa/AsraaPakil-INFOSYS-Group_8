<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="min-h-screen flex items-center justify-center py-10">
        <div class="w-11/12 sm:max-w-md mx-auto">
            <div class="bg-white/90 backdrop-blur rounded-2xl shadow-md border border-amber-100 p-6 sm:p-8">
                <div class="flex flex-col items-center gap-2 mb-6">
                    <div class="h-16 w-16 rounded-full bg-amber-100 flex items-center justify-center shadow-inner">
                        <!-- Café cup icon -->
                        <img src="{{ asset('images/images.png') }}" alt="Logo" class="h-16 w-16 object-contain" />
                    </div>
                    <h1 class="text-2xl font-semibold text-cafe-900">Welcome!</h1>
                    <p class="text-sm text-cafe-700">DeSeventeen Cafe and Resto</p>
                </div>

                <form method="POST" action="{{ route('login') }}" x-data="{ show: false }">
                    @csrf

                    <!-- Email Address -->
                    <div class="space-y-1">
                        <label for="email" class="text-sm font-medium text-cafe-900">Email address</label>
                        <input id="email" type="email" name="email" autocomplete="username" autofocus required value="{{ old('email') }}"
                               class="w-full rounded-lg border input-cafe px-3 py-2 bg-white/60" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4 space-y-1">
                        <label for="password" class="text-sm font-medium text-cafe-900">Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password"
                                   class="w-full rounded-lg border input-cafe px-3 py-2 pr-10 bg-white/60" />
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-cafe-700 focus:outline-none" aria-label="Toggle password visibility">
                                <template x-if="!show">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/></svg>
                                </template>
                                <template x-if="show">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5"><path d="M3.707 2.293 2.293 3.707l3.17 3.17C3.76 8.16 2.56 9.77 2 12c0 0 3 7 10 7 2.23 0 4.12-.65 5.69-1.62l3.31 3.31 1.41-1.41L3.707 2.293zM12 17c-4.97 0-7.61-3.64-8.64-5 .5-.69 1.28-1.68 2.35-2.55l2.16 2.16A5 5 0 0 0 12 17zm0-10c4.97 0 7.61 3.64 8.64 5-.33.45-.78 1.05-1.34 1.65l-2.2-2.2A5 5 0 0 0 12 7z"/></svg>
                                </template>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between mt-4">
                        <label for="remember_me" class="inline-flex items-center gap-2">
                            <input id="remember_me" type="checkbox" name="remember" class="rounded border-[#E7C9A9] text-amber-700 focus:ring-amber-500">
                            <span class="text-sm text-cafe-700">Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a class="text-sm text-cafe-700 hover:text-cafe-900" href="{{ route('password.request') }}">Forgot password?</a>
                        @endif
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="w-full py-2.5 rounded-xl font-medium btn-cafe focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
