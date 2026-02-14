<?php

namespace App\Http\Controllers\cms;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userRepository;
    private $roleRepository;

    public function __construct(UserRepositoryInterface $userRepo, RoleRepositoryInterface $roleRepo)
    {
        $this->userRepository = $userRepo;
        $this->roleRepository = $roleRepo;
    }

    public function index()
    {
        $users = $this->userRepository->getAll()->orderBy('id', 'desc')->paginate(10);

        return view('cms.pages.users.index', compact('users'));
    }

    public function create()
    {
        $roles = $this->roleRepository->getAll();
        return view('cms.pages.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'min:8', 'regex:/[0-9]/', 'confirmed'],
            'role' => 'required',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email already exists',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.regex' => 'Password must contain at least 1 number',
            'role.required' => 'Role is required',
            'is_active.required' => 'Status is required',
        ]);

        $this->userRepository->create($validated);

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function edit($uuid)
    {
        $user = $this->userRepository->getByUuid($uuid);
        $roles = $this->roleRepository->getAll();
        return view('cms.pages.users.update', compact('user', 'roles'));
    }

    public function update(Request $request, $uuid)
    {
        $user = $this->userRepository->getByUuid($uuid);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', 'min:8', 'regex:/[0-9]/', 'confirmed'],
            'role' => 'required',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email already exists',
            'password.min' => 'Password must be at least 8 characters',
            'password.regex' => 'Password must contain at least 1 number',
            'role.required' => 'Role is required',
            'is_active.required' => 'Status is required',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $this->userRepository->update($uuid, $validated);

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy($uuid)
    {
        $this->userRepository->delete($uuid);
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    public function profile($uuid)
    {
        $user = $this->userRepository->getByUuid($uuid);

        if (auth()->user()->uuid !== $uuid) {
            abort(403, 'Unauthorized action.');
        }

        return view('cms.pages.users.profile', compact('user'));
    }

    public function updateProfile(Request $request, $uuid)
    {
        if (auth()->user()->uuid !== $uuid) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|min:6|confirmed',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $this->userRepository->update($uuid, $validated);

        return redirect()->route('users.profile', $uuid)->with('success', 'Profile updated successfully!');
    }
}
