@php($user = $user ?? null)

<div>
    <x-input-label for="name" value="Name" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $user->name ?? '') }}" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="email" value="Email" />
    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email', $user->email ?? '') }}" required />
    <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="role" value="Role" />
    <select id="role" name="role" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">
        <option value="staff" {{ old('role', $user->role ?? 'staff') === 'staff' ? 'selected' : '' }}>Staff</option>
        <option value="admin" {{ old('role', $user->role ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
    </select>
    <x-input-error :messages="$errors->get('role')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="password" :value="$user ? 'New Password (leave blank to keep current)' : 'Password'" />
    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" {{ $user ? '' : 'required' }} />
    <x-input-error :messages="$errors->get('password')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="password_confirmation" value="Confirm Password" />
    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" {{ $user ? '' : 'required' }} />
</div>
