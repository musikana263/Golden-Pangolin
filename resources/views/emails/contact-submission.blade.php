<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>New contact form submission</title>
</head>
<body style="margin:0;padding:24px;background:#f6f1e6;font-family:-apple-system,'Segoe UI',Arial,sans-serif;color:#1a1208;">
    <div style="max-width:560px;margin:0 auto;background:#ffffff;border:1px solid #e5d8b8;border-radius:12px;padding:28px 30px;">
        <h1 style="margin:0 0 8px;font-size:1.25rem;color:#b8872e;font-family:Georgia,serif;">
            New contact form submission
        </h1>
        <p style="margin:0 0 20px;color:#6b5a3a;font-size:.9rem;">
            Someone just used the contact form on goldenpangolin.com.
        </p>

        <table style="width:100%;border-collapse:collapse;font-size:.92rem;">
            <tr>
                <td style="padding:8px 0;color:#8a7549;width:90px;vertical-align:top;">From</td>
                <td style="padding:8px 0;">
                    <strong>{{ $contact->name }}</strong><br>
                    <a href="mailto:{{ $contact->email }}" style="color:#b8872e;">{{ $contact->email }}</a>
                </td>
            </tr>
            <tr>
                <td style="padding:8px 0;color:#8a7549;vertical-align:top;">Subject</td>
                <td style="padding:8px 0;"><strong>{{ $contact->subject }}</strong></td>
            </tr>
            <tr>
                <td style="padding:8px 0;color:#8a7549;vertical-align:top;">Sent</td>
                <td style="padding:8px 0;">{{ $contact->created_at?->format('j M Y, H:i') ?? now()->format('j M Y, H:i') }}</td>
            </tr>
        </table>

        <div style="margin-top:18px;padding:16px 18px;background:#faf5e8;border-left:3px solid #d4a03c;border-radius:6px;white-space:pre-wrap;line-height:1.55;">{{ $contact->message }}</div>

        <p style="margin:22px 0 0;font-size:.8rem;color:#8a7549;">
            Hit Reply to respond directly to {{ $contact->name }}.
        </p>
    </div>
</body>
</html>
