<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogger;
use App\Services\TeacherService;
use App\Support\Roles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $users = User::query()
            ->with('roles')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%')
                        ->orWhere('username', 'like', '%'.$search.'%');
                });
            })
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->whereHas('roles', fn ($q) => $q->where('name', $request->string('role')->toString()));
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'roles' => Roles::labels(),
            'filters' => $request->only(['search', 'role']),
            'assignableRoles' => [
                Roles::KEPALA_SEKOLAH => Roles::label(Roles::KEPALA_SEKOLAH),
                Roles::SUPER_ADMIN => Roles::label(Roles::SUPER_ADMIN),
            ],
        ]);
    }

    public function store(Request $request, TeacherService $teacherService): RedirectResponse
    {
        Gate::authorize('viewAny', User::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:100', Rule::unique('users', 'username')],
            'email' => ['nullable', 'email', 'max:150', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::in([Roles::SUPER_ADMIN, Roles::KEPALA_SEKOLAH])],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
            'must_change_password' => true,
        ]);

        $user->assignRole($data['role']);

        AuditLogger::log('user.created', $user, 'Membuat akun '.$data['role'].': '.$user->name);

        return back()->with('success', 'Akun berhasil dibuat. Password wajib diganti saat login pertama.');
    }

    public function resetPassword(User $user, TeacherService $teacherService): RedirectResponse
    {
        Gate::authorize('resetPassword', $user);

        $password = $teacherService->generatePassword();

        $user->update([
            'password' => $password,
            'must_change_password' => true,
        ]);

        AuditLogger::log('user.password-reset', $user, 'Reset password akun: '.$user->name);

        return back()->with('generated_account', [
            'name' => $user->name,
            'username' => $user->username,
            'password' => $password,
        ]);
    }

    public function toggleActive(User $user): RedirectResponse
    {
        Gate::authorize('toggleActive', $user);

        $user->update(['is_active' => ! $user->is_active]);

        AuditLogger::log('user.toggled', $user, ($user->is_active ? 'Mengaktifkan' : 'Menonaktifkan').' akun: '.$user->name);

        return back()->with('success', 'Status akun berhasil diubah menjadi '.($user->is_active ? 'aktif' : 'nonaktif').'.');
    }
}
