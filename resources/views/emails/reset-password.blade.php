<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khôi phục mật khẩu</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 30px 15px;
            color: #1e293b;
        }
        .email-container {
            max-width: 580px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 28px 24px;
            text-align: center;
        }
        .brand-text {
            color: #ffffff;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 1px;
            margin: 0;
        }
        .content {
            padding: 32px 28px;
            line-height: 1.7;
        }
        .title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 16px;
        }
        .button-wrap {
            text-align: center;
            margin: 32px 0;
        }
        .btn-reset {
            display: inline-block;
            background: #4f46e5;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 15px;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }
        .info-box {
            background-color: #f8fafc;
            border-left: 4px solid #4f46e5;
            padding: 14px 16px;
            border-radius: 0 8px 8px 0;
            font-size: 13px;
            color: #475569;
            margin-bottom: 24px;
        }
        .footer {
            background: #f8fafc;
            padding: 20px 24px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
        .url-text {
            word-break: break-all;
            font-size: 12px;
            color: #6366f1;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1 class="brand-text">📰 NEWS & BLOG</h1>
        </div>

        <div class="content">
            <h2 class="title">Xin chào {{ $userName }},</h2>
            <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản liên kết với địa chỉ email <strong>{{ $userEmail }}</strong>.</p>
            <p>Vui lòng bấm vào nút bên dưới để tiến hành thiết lập mật khẩu mới an toàn:</p>

            <div class="button-wrap">
                <a href="{{ $resetUrl }}" target="_blank" class="btn-reset">
                    Đặt lại mật khẩu của bạn
                </a>
            </div>

            <div class="info-box">
                ⏱️ <strong>Lưu ý bảo mật:</strong> Liên kết này chỉ có hiệu lực trong vòng <strong>60 phút</strong> kể từ thời điểm gửi. Nếu bạn không gửi yêu cầu này, vui lòng bỏ qua email và tài khoản của bạn vẫn được an toàn.
            </div>

            <p style="font-size: 13px; color: #64748b; margin-bottom: 6px;">Nếu nút bấm trên không hoạt động, bạn có thể sao chép và dán trực tiếp đường link sau vào trình duyệt:</p>
            <a href="{{ $resetUrl }}" class="url-text">{{ $resetUrl }}</a>
        </div>

        <div class="footer">
            <p style="margin: 0 0 6px 0;">© {{ date('Y') }} NEWS & BLOG. Tất cả quyền được bảo lưu.</p>
            <p style="margin: 0;">Email tự động, vui lòng không trả lời trực tiếp email này.</p>
        </div>
    </div>
</body>
</html>
