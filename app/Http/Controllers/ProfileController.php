<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        $activeTab = $request->query('tab', 'profile');

        $savedPosts = $user->savedPosts()
            ->with(['user', 'category'])
            ->paginate(6, ['*'], 'saved_page')
            ->withQueryString();

        $followingAuthors = $user->following()
            ->withCount('publishedPosts')
            ->paginate(10, ['*'], 'following_page')
            ->withQueryString();

        return view('profile.edit', compact('user', 'activeTab', 'savedPosts', 'followingAuthors'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        $user->name = $validated['name'];
        $user->bio = $validated['bio'] ?? null;

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')
                ->store('avatars', 'public');

            if ($path === false) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'avatar' => 'Không thể lưu ảnh đại diện. Vui lòng thử lại.',
                    ]);
            }

            $oldAvatar = $user->avatar;
            $user->avatar = $path;
        }

        $user->save();

        if (
            $request->hasFile('avatar')
            && ! empty($oldAvatar)
            && str_starts_with($oldAvatar, 'avatars/')
        ) {
            Storage::disk('public')->delete($oldAvatar);
        }

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Cập nhật thông tin thành công.');
    }

    /**
     * Đổi mật khẩu cho người dùng đã đăng nhập.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
            'password.different' => 'Mật khẩu mới không được trùng với mật khẩu hiện tại.',
        ]);

        $user = $request->user();
        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('password_success', 'Đổi mật khẩu thành công!');
    }
}
