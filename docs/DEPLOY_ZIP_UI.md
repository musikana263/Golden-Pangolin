# Deploying Golden Pangolin to DirectAdmin

Three things you do, in order:

1. **Create a database** in DirectAdmin.
2. **Upload + extract two zips** in File Manager.
3. **Edit `.env`** then visit `/install.php` once.

That's it.

---

## 1. Build the two zips on your laptop

Open **PowerShell** in `C:\Users\Malaika\Tours-Travel`:

```powershell
.\scripts\build-deploy-zip.ps1
```

When it finishes you'll have two files in `.\deploy\`:

| File | Where it goes on the server |
|---|---|
| `golden-pangolin-app.zip` | extracted at the **root** of File Manager (`~/`) → creates `~/goldenpangolin/` |
| `golden-pangolin-public.zip` | extracted **inside** `~/domains/goldenpangolin.com/public_html/` |

---

## 2. Create the database in DirectAdmin

DirectAdmin → **MySQL Management** → **Create new Database**:

- Database name: `gp_site` (DirectAdmin will prefix it: `<account>_gp_site`)
- Username: `gp_user` (becomes `<account>_gp_user`)
- Password: click **Random**, copy it somewhere

Save the three values — you'll paste them into `.env` in Step 4.

---

## 3. Upload + extract the two zips

DirectAdmin → **File Manager**.

**App zip** (at the root):

1. Make sure you're at your home directory (the breadcrumb shows just `/` or your username).
2. **Upload** → pick `deploy\golden-pangolin-app.zip` → wait for upload.
3. Right-click the zip → **Extract** → into the current folder.
4. Delete the zip.

You should now see a folder `goldenpangolin/` at the root.

**Public zip** (inside public_html):

1. Navigate to `domains/goldenpangolin.com/public_html/`.
2. Delete the default placeholder file if there is one (`index.html` or similar).
3. **Upload** → pick `deploy\golden-pangolin-public.zip`.
4. Right-click → **Extract** into the current folder.
5. Delete the zip.

`public_html/` should now contain `index.php`, `install.php`, `.htaccess`, `css/`, `js/`, `images/`, etc.

---

## 4. Edit `.env` with your DB credentials

In File Manager, open `~/goldenpangolin/.env` (right-click → **Edit**).

Find these three lines and replace the placeholders with the values from Step 2:

```
DB_DATABASE=<your_db_name>      →   DB_DATABASE=<account>_gp_site
DB_USERNAME=<your_db_user>      →   DB_USERNAME=<account>_gp_user
DB_PASSWORD=<your_db_password>  →   DB_PASSWORD=<the password you saved>
```

Save the file.

(You can also change `SEED_ADMIN_PASSWORD=GoldP@2026` to a stronger password — that becomes your admin login password.)

---

## 5. Run the installer in your browser

Visit:

```
https://goldenpangolin.com/install.php
```

The page will run migrations, seed the admin user, link storage, cache configs, then **delete itself**. You'll see "Install complete" with a button to the login page.

---

## 6. Enable HTTPS

DirectAdmin → **SSL Certificates** → **Let's Encrypt**:

- Tick `goldenpangolin.com` + `www.goldenpangolin.com`
- Untick the wildcard option
- Tick **Force SSL with HTTPS Redirect**
- **Save**

---

## 7. Sign in

<https://goldenpangolin.com/login>

- Email: `info@goldenpangolin.com`
- Password: whatever you set as `SEED_ADMIN_PASSWORD` in `.env` (default `GoldP@2026`)

Login emails you a 6-digit OTP code. If you didn't set up Postmark yet, the code goes to `~/goldenpangolin/storage/logs/laravel.log` — open it in File Manager.

---

## If something goes wrong

**`install.php` says "Edit your .env first"** — open `~/goldenpangolin/.env` in File Manager and replace the `<your_db_*>` placeholders.

**500 error on homepage** — temporarily set `APP_DEBUG=true` in `.env`, reload to see the real error, fix it, set back to `false`.

**Can't connect to database** — your DB name/user need the account prefix (e.g. `myaccount_gp_site`, not just `gp_site`).

**Updating later** — re-run `.\scripts\build-deploy-zip.ps1`, back up `~/goldenpangolin/.env`, replace `~/goldenpangolin/` with the new app zip, replace `public_html/` contents with the new public zip, restore `.env`. The `install.php` is gone after first use — to re-run migrations after an update, copy `install.php` back from the public zip (it'll just run the new migrations and delete itself again).
