<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
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
}
