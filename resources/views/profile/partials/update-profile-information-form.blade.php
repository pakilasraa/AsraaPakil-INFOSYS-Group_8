<section class="max-w-xl">
    <header class="mb-4">
        <h2 class="text-lg font-semibold text-cafe-900">Profile Settings</h2>
        <p class="mt-1 text-sm text-cafe-700">Update your username and password.</p>
    </header>

    <div class="bg-white rounded-xl shadow p-4">
        <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('patch')

            <div>
                <label class="text-sm text-cafe-900">Username</label>
                <input id="username" name="username" type="text" class="mt-1 block w-full rounded-lg border input-cafe px-3 py-2" value="{{ old('username', $user->username) }}" required autocomplete="username" />
                @error('username')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="text-sm text-cafe-900">Password</label>
                <input id="password" name="password" type="password" class="mt-1 block w-full rounded-lg border input-cafe px-3 py-2" autocomplete="new-password" />
                @error('password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="text-sm text-cafe-900">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-lg border input-cafe px-3 py-2" autocomplete="new-password" />
            </div>

            <div class="text-sm text-cafe-700">
                <div>Last login: {{ optional($user->last_login)->format('Y-m-d H:i') ?? '—' }}</div>
                <div>Last password change: {{ optional($user->last_password_change)->format('Y-m-d H:i') ?? '—' }}</div>
            </div>

            <div class="pt-2">
                <button class="px-4 py-2 rounded-md btn-cafe">Update Profile</button>
                @if (session('status') === 'profile-updated')
                    <span class="ml-2 text-sm text-cafe-700">Saved.</span>
                @endif
            </div>
        </form>
    </div>
</section>
