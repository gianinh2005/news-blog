<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Hiển thị form yêu cầu quên mật khẩu.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Gửi email khôi phục mật khẩu.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.exists' => 'Không tìm thấy tài khoản nào khớp với email này.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user->is_active) {
            return back()->withErrors(['email' => 'Tài khoản này hiện đang bị khóa. Vui lòng liên hệ ban quản trị.']);
        }

        // Tạo token ngẫu nhiên bảo mật cao
        $token = Str::random(64);

        // Lưu bản ghi token mã hóa vào bảng password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $request->email,
        ]);

        // Gửi Mailable Email qua hệ thống Mail của Laravel
        try {
            Mail::to($user->email)->send(new ResetPasswordMail(
                userName: $user->name,
                userEmail: $user->email,
                resetUrl: $resetUrl
            ));
        } catch (\Throwable $e) {
            // Trong trường hợp cấu hình mail server có sự cố, log lỗi và thông báo
            report($e);
        }

        return back()->with('status', 'Chúng tôi đã gửi hướng dẫn đặt lại mật khẩu đến email của bạn! Vui lòng kiểm tra hộp thư đến (hoặc thư mục Spam/Log).');
    }

    /**
     * Hiển thị form đặt lại mật khẩu mới từ link token trong email.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with([
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Thực hiện đổi mật khẩu mới bằng token.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'email.exists' => 'Không tìm thấy tài khoản.',
            'password.min' => 'Mật khẩu mới phải có tối thiểu 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $record || ! Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã qua sử dụng.']);
        }

        // Kiểm tra thời hạn hiệu lực 60 phút
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return back()->withErrors(['email' => 'Liên kết khôi phục này đã hết hạn. Vui lòng gửi lại yêu cầu mới.']);
        }

        // Cập nhật mật khẩu mới
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Xóa token khỏi DB để token không thể tái sử dụng lần 2
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Mật khẩu của bạn đã được cập nhật thành công! Vui lòng đăng nhập.');
    }
}
