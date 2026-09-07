<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        Gate::authorize('users.manage');

        return view('users.index', ['users' => User::orderBy('name')->paginate(20)]);
    }

    public function create(): View
    {
        Gate::authorize('users.manage');

        return view('users.form', ['user' => new User()]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        Gate::authorize('users.manage');

        User::create($request->validated());

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function edit(User $user): View
    {
        Gate::authorize('users.manage');

        return view('users.form', compact('user'));
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('users.manage');

        $data = $request->validated();

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated.');
    }
}
