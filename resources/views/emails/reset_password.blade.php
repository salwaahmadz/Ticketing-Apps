<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f6f8; font-family: Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding:30px 0;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:600px; background:#ffffff; border-radius:6px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.05);">

                    <!-- Header -->
                    <tr>
                        <td style="padding:20px 30px; background:#0d6efd; color:#ffffff;">
                            <h2 style="margin:0; font-size:20px; font-weight:600;">
                                Reset Password
                            </h2>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px; color:#333333; font-size:14px; line-height:1.6;">
                            <p style="margin-top:0;">
                                Hello,
                            </p>

                            <p>
                                We have received a password reset request for your account.
                                Please click the button below to proceed.
                            </p>

                            <p style="text-align:center; margin:30px 0;">
                                <a href="{{ route(@$data['route'], ['token' => $data['token'], 'email' => $data['email']]) }}"
                                    style="background:#0d6efd; color:#ffffff; text-decoration:none; padding:12px 24px; border-radius:4px; display:inline-block; font-weight:600;">
                                    Reset Password
                                </a>
                            </p>

                            <p>
                                If you did not make this request, please ignore this email.
                            </p>

                            <p style="margin-bottom:0;">
                                Respectfully,<br>
                                <strong>{{ config('app.name') }}</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="padding:15px 30px; background:#f8f9fa; color:#6c757d; font-size:12px; text-align:center;">
                            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>