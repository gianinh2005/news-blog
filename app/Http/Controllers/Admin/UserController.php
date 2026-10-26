<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách tài khoản.
     */
    public function index()
    {
        $users = User::query()
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Thay đổi vai trò user/admin.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['user', 'admin'])],
        ]);

        $newRole = $validated['role'];
        $actor = $request->user();

        // Không cho Admin tự thay đổi quyền của chính mình.
        if ($actor->id === $user->id && $newRole !== $user->role) {
            throw ValidationException::withMessages([
                'role' => 'Bạn không thể tự thay đổi quyền của mình.',
            ]);
        }

        // Không cho hạ quyền Admin đang hoạt động cuối cùng.
        if (
            $user->role === 'admin'
            && $newRole !== 'admin'
            && $user->is_active
            && User::where('role', 'admin')
                ->where('is_active', true)
                ->count() <= 1
        ) {
            throw ValidationException::withMessages([
                'role' => 'Không thể hạ quyền Admin đang hoạt động cuối cùng.',
            ]);
        }

        $user->role = $newRole;
        $user->save();

        return back()->with('success', 'Đã cập nhật vai trò người dùng.');
    }

    /**
     * Khóa hoặc mở khóa tài khoản.
     */
    public function toggleStatus(Request $request, User $user)
    {
        $actor = $request->user();

        // Không cho Admin tự khóa tài khoản của mình.
        if ($actor->id === $user->id) {
            throw ValidationException::withMessages([
                'status' => 'Bạn không thể khóa hoặc mở khóa tài khoản của chính mình.',
            ]);
        }

        // Không cho khóa Admin đang hoạt động cuối cùng.
        if (
            $user->role === 'admin'
            && $user->is_active
            && User::where('role', 'admin')
                ->where('is_active', true)
                ->count() <= 1
        ) {
            throw ValidationException::withMessages([
                'status' => 'Không thể khóa Admin đang hoạt động cuối cùng.',
            ]);
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $message = $user->is_active
            ? 'Đã mở khóa tài khoản.'
            : 'Đã khóa tài khoản.';

        return back()->with('success', $message);
    }
}
