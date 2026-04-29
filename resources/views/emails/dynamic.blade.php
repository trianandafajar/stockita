<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
</head>

<body style="margin:0; padding:0; background:#f1f5f9; font-family: Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:14px; overflow:hidden; box-shadow:0 8px 24px rgba(0,0,0,0.08);">

                    <tr>
                        <td
                            style="background:linear-gradient(135deg, #10b981, #059669); padding:25px; text-align:center; color:white;">
                            <h2 style="margin:0; font-size:20px; font-weight:600; letter-spacing:0.5px;">
                                {{ $store_name }}
                            </h2>
                        </td>
                    </tr>

                    <tr>
                        <td style="height:4px; background:#10b981;"></td>
                    </tr>

                    <tr>
                        <td style="padding:35px 30px; color:#334155; font-size:14px; line-height:1.8;">
                            <div style="margin-bottom:20px;">
                                {!! $content !!}
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding-bottom:30px;">
                            <a href="{{ config('app.url') }}"
                                style="display:inline-block; background:#10b981; color:#ffffff; text-decoration:none; padding:12px 26px; border-radius:8px; font-size:14px; font-weight:600;">
                                Kunjungi Aplikasi
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="background:#f8fafc; padding:25px; text-align:center; font-size:12px; color:#64748b;">
                            <div style="margin-bottom:6px;">
                                © {{ date('Y') }} {{ $store_name }}
                            </div>
                            <div>
                                Email ini dikirim secara otomatis, mohon tidak membalas.
                            </div>
                        </td>
                    </tr>

                </table>

                <div style="height:20px;"></div>

            </td>
        </tr>
    </table>

</body>

</html>