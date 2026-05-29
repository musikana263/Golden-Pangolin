# Email setup — Postmark for Golden Pangolin

Postmark is wired as the production mailer. Local dev uses Laravel's `log` driver so test emails land in `storage/logs/laravel.log` and never reach a real inbox.

## 1. Sign up + sender signature

1. Create an account at https://postmarkapp.com.
2. Create a server called `goldenpangolin-prod`. Use the **Transactional** stream for signups / password resets / booking confirmations, and a separate **Broadcast** stream if you ever do marketing email.
3. Add `goldenpangolin.com` as a **Sender Signature**.

## 2. DNS records (critical for deliverability)

Postmark generates exact values for these — paste them into DirectAdmin > **DNS Management** > `goldenpangolin.com`.

| Type | Host | Value (Postmark gives you the exact string) |
|---|---|---|
| TXT | `@` | `v=spf1 a mx include:spf.mtasv.net ~all` |
| TXT | `20240101pm._domainkey` | (long DKIM public key Postmark generates) |
| CNAME | `pm-bounces` | `pm.mtasv.net` (Return-Path / DMARC alignment) |
| TXT | `_dmarc` | `v=DMARC1; p=quarantine; rua=mailto:dmarc@goldenpangolin.com; ruf=mailto:dmarc@goldenpangolin.com; fo=1` |

Start DMARC at `p=none` for the first 2 weeks while you watch the aggregate reports, then move to `p=quarantine`, then `p=reject`.

After adding, click **Verify** in Postmark. SPF + DKIM both need to be green before the first send.

## 3. Add the token to `.env`

```env
MAIL_MAILER=postmark
POSTMARK_TOKEN=<server-api-token-from-postmark>
MAIL_FROM_ADDRESS="hello@goldenpangolin.com"
MAIL_FROM_NAME="Golden Pangolin"
```

Then on the server:

```bash
php artisan config:cache
```

## 4. Receiving mail to hello@goldenpangolin.com

Postmark only sends. Set up the inbound side in DirectAdmin > **E-Mail Accounts** — create `hello@goldenpangolin.com` (real mailbox) **or** a forwarder pointing at your existing inbox.

If you create a real mailbox, also add the MX record from DirectAdmin > **MX Records**.

## 5. Verify end-to-end

From the project root on the server:

```bash
php artisan tinker
>>> Mail::raw('Postmark wiring works.', fn($m) => $m->to('your-real-inbox@example.com')->subject('GP test'));
```

The Postmark **Activity** tab should show the message within seconds. If you get `401 Unauthorized`, the token is wrong; if `422`, the sender signature isn't verified yet.

## 6. Things to watch

- **Bounces:** Postmark suppresses bouncing addresses automatically. Don't manually retry against the suppression list — it'll trash your sender reputation.
- **Rate:** Free tier is 100 emails/month; the next tier is $15 for 10k. Move up before the first marketing send.
- **Subject lines:** No ALL-CAPS, no "FREE", no exclamation pile-ups — they shove transactional mail into spam.
- **Unsubscribe header:** Postmark adds `List-Unsubscribe` automatically on broadcast streams. Don't disable it.

## 7. Local dev

`.env` ships with `MAIL_MAILER=log`. Triggered emails write to `storage/logs/laravel.log`. To preview HTML rendering instead, switch to `MAIL_MAILER=array` and use the `MailFake` in tests, or pop in a Mailtrap inbox.
