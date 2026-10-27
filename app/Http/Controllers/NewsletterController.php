<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email của bạn.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
        ]);

        $subscriber = NewsletterSubscriber::where('email', $validated['email'])->first();

        if ($subscriber) {
            if (! $subscriber->is_active) {
                $subscriber->update(['is_active' => true]);
                $message = 'Cảm ơn bạn! Địa chỉ email đã được kích hoạt lại để nhận bản tin.';
            } else {
                $message = 'Email của bạn đã được đăng ký nhận bản tin từ trước rồi nhé!';
            }
        } else {
            NewsletterSubscriber::create([
                'email' => $validated['email'],
                'is_active' => true,
            ]);
            $message = 'Đăng ký nhận bản tin định kỳ thành công! Cảm ơn bạn đã quan tâm.';
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
