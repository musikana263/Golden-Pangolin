<?php
// One-shot admin creator. Upload to public_html/, visit in browser,
// fill the form, submit. Self-deletes after success. DELETE if not used.
ini_set('display_errors', '1');
error_reporting(E_ALL);

$root = __DIR__ . '/../../../goldenpangolin';
require $root . '/vendor/autoload.php';
$app    = require $root . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$result = null;
$error  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = (string) ($_POST['password'] ?? '');
    $name  = trim($_POST['name'] ?? 'Administration');

    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email.';
    } elseif (strlen($pass) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        try {
            $user = \App\User::where('email', $email)->first();
            if ($user) {
                $user->forceFill([
                    'name'              => $name,
                    'password'          => \Illuminate\Support\Facades\Hash::make($pass),
                    'role'              => 'admin',
                    'email_verified_at' => now(),
                ])->save();
                $result = "Updated existing user '{$email}' → role=admin, password reset, email marked verified.";
            } else {
                $user = new \App\User();
                $user->forceFill([
                    'name'              => $name,
                    'email'             => $email,
                    'password'          => \Illuminate\Support\Facades\Hash::make($pass),
                    'role'              => 'admin',
                    'email_verified_at' => now(),
                ])->save();
                $result = "Created new admin user '{$email}'.";
            }
            // Self-destruct on success.
            @unlink(__FILE__);
        } catch (\Throwable $e) {
            $error = 'DB error: ' . $e->getMessage();
        }
    }
}
?><!doctype html>
<html><head><meta charset="utf-8"><title>Create Admin</title>
<style>
  body { font-family: -apple-system,'Segoe UI',sans-serif; background:#1a1208; color:#fbf3e1;
         margin:0; padding:48px 16px; min-height:100vh; }
  .card { max-width:480px; margin:0 auto; background:#2a1c10;
          border:1px solid rgba(212,160,60,.35); border-radius:14px; padding:28px; }
  h1 { color:#f3c779; margin:0 0 16px; font-size:1.3rem; }
  label { display:block; margin:14px 0 4px; font-size:.85rem; color:#f3c779; }
  input { width:100%; padding:10px 12px; border-radius:8px; border:1px solid #5a3e1f;
          background:#0c0c10; color:#fbf3e1; font-size:1rem; box-sizing:border-box; }
  button { margin-top:18px; width:100%; padding:12px; border:0; border-radius:8px;
           background:linear-gradient(135deg,#d4a03c,#b8872e); color:#1a1208;
           font-weight:700; font-size:1rem; cursor:pointer; }
  .ok { background:#1f3a1f; border:1px solid #4a8a4a; padding:14px; border-radius:8px; margin-bottom:14px; }
  .err { background:#3a1f1f; border:1px solid #8a4a4a; padding:14px; border-radius:8px; margin-bottom:14px; }
  a { color:#f3c779; }
  small { color:#888; }
</style></head><body>
<div class="card">
  <h1>Create / reset admin user</h1>
  <?php if ($result): ?>
    <div class="ok"><?= htmlspecialchars($result) ?></div>
    <p><a href="/login">Go to login →</a></p>
    <p><small>This file has been deleted from the server.</small></p>
  <?php else: ?>
    <?php if ($error): ?>
      <div class="err"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <label>Email</label>
      <input type="email" name="email" required value="info@goldenpangolin.com">
      <label>Name</label>
      <input type="text" name="name" required value="Administration">
      <label>Password <small>(min 8 chars)</small></label>
      <input type="text" name="password" required minlength="8">
      <button type="submit">Create / reset admin</button>
    </form>
    <p><small>Uses the existing DB connection from .env. Will create the user if missing, or update password + role if they already exist.</small></p>
  <?php endif; ?>
</div>
</body></html>
