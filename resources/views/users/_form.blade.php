<div class="form-grid">
    <x-form.input name="name" label="Full name" :value="$user->name" required maxlength="255" autofocus />
    <x-form.input name="email" label="Email (login)" type="email" :value="$user->email" required maxlength="255" autocomplete="off" />
    <x-form.select name="role" label="Role" required :value="$user->role ?? \App\Enums\UserRole::Staff"
                   :options="collect($roles)->mapWithKeys(fn ($r) => [$r->value => $r->label()])->all()"
                   hint="Administrators can change settings, manage users and delete records." />
    <x-form.checkbox name="is_active" label="Account active" :checked="$user->exists ? $user->is_active : true" wrapper-class="mt-3" />
    <x-form.input name="password" type="password" :label="$user->exists ? 'New password' : 'Password'" :required="! $user->exists" autocomplete="new-password" maxlength="255"
                  :hint="$user->exists ? 'Leave blank to keep the current password.' : 'At least 8 characters with letters and numbers.'" />
    <x-form.input name="password_confirmation" type="password" label="Confirm password" :required="! $user->exists" autocomplete="new-password" maxlength="255" />
</div>
