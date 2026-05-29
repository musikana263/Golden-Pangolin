<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Golden Pangolin sign-in code</title>
</head>
<body style="margin:0;padding:0;background:#f7f5f0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;color:#1a1208;line-height:1.5;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background:#f7f5f0;padding:48px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="440"
                       style="background:#ffffff;border-radius:16px;border:1px solid #ecd9a8;">
                    <tr>
                        <td style="padding:36px 36px 28px;text-align:center;">
                            <p style="margin:0 0 4px;font-family:'Playfair Display',Georgia,serif;font-size:1.3rem;font-weight:700;color:#1a1208;letter-spacing:-.01em;">
                                Golden <span style="color:#b8872e;">Pangolin</span>
                            </p>
                            <p style="margin:0;font-size:.78rem;color:#7a6952;text-transform:uppercase;letter-spacing:.12em;">
                                Sign-in code
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 36px 4px;">
                            <p style="margin:0 0 24px;font-size:.95rem;color:#3a2f1c;">
                                Hi {{ $name }} &mdash; use this code to finish signing in.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 36px 28px;text-align:center;">
                            <div style="display:inline-block;padding:16px 28px;background:#fdf6e3;border:1px solid #ecd9a8;border-radius:12px;font-family:'SF Mono','Courier New',monospace;font-size:2.1rem;font-weight:700;letter-spacing:.45rem;color:#1a1208;">
                                {{ $code }}
                            </div>
                            <p style="margin:14px 0 0;font-size:.78rem;color:#7a6952;">
                                Expires in {{ $minutes }} minutes
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 36px 36px;">
                            <hr style="border:0;border-top:1px solid #ecd9a8;margin:0 0 16px;">
                            <p style="margin:0;font-size:.78rem;color:#7a6952;line-height:1.55;">
                                Didn't try to sign in? You can ignore this email &mdash; your password
                                hasn't been used to log into anything.
                            </p>
                        </td>
                    </tr>
                </table>
                <p style="margin:18px 0 0;font-size:.72rem;color:#9a8a70;">
                    &copy; {{ date('Y') }} Golden Pangolin
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
