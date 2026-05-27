<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        /* Reset & Base */
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            color: #374151;
            line-height: 1.6;
        }

        /* Layout */
        .wrapper {
            width: 100%;
            background-color: #f3f4f6;
            padding: 40px 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 40px 30px;
            text-align: center;
        }

        .header-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.025em;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Content */
        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 18px;
            color: #111827;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .message {
            color: #6b7280;
            margin-bottom: 30px;
            font-size: 15px;
        }

        /* Button */
        .button-container {
            text-align: center;
            margin: 35px 0;
        }

        .button {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
            transition: all 0.3s ease;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
        }

        /* Info Box */
        .info-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 30px 0;
            border-radius: 8px;
        }

        .info-box p {
            margin: 0;
            color: #92400e;
            font-size: 14px;
        }

        /* Alternative Link */
        .alternative-link {
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
        }

        .alternative-link p {
            margin: 0 0 10px 0;
            color: #6b7280;
            font-size: 13px;
        }

        .link-text {
            word-break: break-all;
            color: #4f46e5;
            font-size: 13px;
        }

        /* Footer */
        .footer {
            padding: 30px;
            text-align: center;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }

        .footer p {
            color: #9ca3af;
            font-size: 13px;
            margin: 5px 0;
        }

        .footer a {
            color: #4f46e5;
            text-decoration: none;
        }

        /* Divider */
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 30px 0;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .wrapper {
                padding: 20px 0;
            }

            .container {
                border-radius: 0;
            }

            .content {
                padding: 30px 20px;
            }

            .header {
                padding: 30px 20px;
            }

            .button {
                padding: 14px 30px;
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <!-- Header -->
            <div class="header">
                <div class="header-icon">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C9.243 2 7 4.243 7 7v3H6c-1.103 0-2 .897-2 2v8c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2v-8c0-1.103-.897-2-2-2h-1V7c0-2.757-2.243-5-5-5zM9 7c0-1.654 1.346-3 3-3s3 1.346 3 3v3H9V7z" fill="white"/>
                    </svg>
                </div>
                <h1>Reset Password</h1>
            </div>

            <!-- Content -->
            <div class="content">
                <p class="greeting">Halo!</p>

                <p class="message">
                    Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda. 
                    Klik tombol di bawah ini untuk mereset password Anda:
                </p>

                <div class="button-container">
                    <a href="{{ $resetUrl }}" class="button">Reset Password</a>
                </div>

                <div class="info-box">
                    <p>
                        <strong>⚠️ Penting:</strong> Link reset password ini akan kedaluwarsa dalam <strong>60 menit</strong>. 
                        Pastikan Anda mereset password sebelum link kedaluwarsa.
                    </p>
                </div>

                <p class="message">
                    Jika Anda tidak meminta reset password, abaikan email ini. Password Anda tidak akan berubah.
                </p>

                <div class="divider"></div>

                <div class="alternative-link">
                    <p><strong>Mengalami masalah dengan tombol di atas?</strong></p>
                    <p>Salin dan tempel URL berikut ke browser Anda:</p>
                    <p class="link-text">{{ $resetUrl }}</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>Email ini dikirim oleh <strong>{{ config('app.name') }}</strong></p>
                <p>Jika Anda memiliki pertanyaan, silakan hubungi kami.</p>
                <p style="margin-top: 15px;">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>

</html>
