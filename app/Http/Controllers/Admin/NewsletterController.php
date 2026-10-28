<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::query()->latest();

        if ($search = $request->input('q')) {
            $query->where('email', 'like', "%{$search}%");
        }

        $subscribers = $query->paginate(15)->withQueryString();

        return view('admin.subscribers.index', compact('subscribers', 'search'));
    }

    public function destroy(NewsletterSubscriber $subscriber)
    {
        $email = $subscriber->email;
        $subscriber->delete();

        AdminLog::record('delete_subscriber', "Xóa người đăng ký nhận bản tin: {$email}", 'subscriber');

        return back()->with('success', 'Đã xóa người đăng ký bản tin thành công.');
    }
}
