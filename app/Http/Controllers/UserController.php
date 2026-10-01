<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * System login accounts. Admin only (enforced on the route group).
 */
class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', ['users' => User::query()->orderBy('name')->paginate(25)]);
    }

    public function create(): View
    {
        return view('users.create', ['user' => new User, 'roles' => UserRole::cases()]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = new User(['name' => $data['name'], 'email' => $data['email'], 'password' => $data['password']]);
        $user->role = UserRole::from($data['role']);
        $user->is_active = $data['is_active'];
        $user->save();

        return redirect()->route('users.index')->with('success', "User {$user->name} created.");
    }

    public function edit(User $user): View
    {
        return view('users.edit', ['user' => $user, 'roles' => UserRole::cases()]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $role = UserRole::from($data['role']);

        if ($request->user()->is($user) && ($role !== UserRole::Admin || ! $data['is_active'])) {
            return back()->withInput()->withErrors(['role' => 'You cannot remove your own administrator access or deactivate yourself.']);
        }

        $user->fill(['name' => $data['name'], 'email' => $data['email']]);
        if (! empty($data['password'])) {
            $user->password = $data['password'];
            // Sign the user out of "remember me" sessions on other devices.
            $user->setRememberToken(null);
        }
        $user->role = $role;
        $user->is_active = $data['is_active'];
        $user->save();

        if (! $user->is_active) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return redirect()->route('users.index')->with('success', "User {$user->name} updated.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        DB::table('sessions')->where('user_id', $user->id)->delete();

        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
