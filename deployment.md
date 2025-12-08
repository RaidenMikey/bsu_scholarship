# Deployment Guide: BSU Scholarship System (Hostinger)

This guide details the steps to deploy the BSU Scholarship Laravel application to a shared hosting environment like Hostinger.


**Note: If you do not see a "Terminal" or "SSH Access" option in your hosting dashboard, do not worry. This guide includes alternative steps (marked as "No Terminal Method") to deploy successfully using only the File Manager.**

## 1. Local Preparation

Before uploading your project, you need to prepare the codebase.

### Build Assets
Since shared hosting typically doesn't run Node.js/Vite, you must build your frontend assets locally.

```bash
npm run build
```

This will generate the `public/build` directory containing your compiled CSS and JS.

### Clean & Zip
1.  Remove the `node_modules` folder (do not upload this; it's huge and unnecessary).
2.  Remove the `vendor` folder (it's better to install dependencies on the server, or upload it if you can't run composer on the server - **Recommended for beginners: Upload `vendor` if checked, but best practice is to run composer install on server**).
    *   *Note: If you have SSH access (Hostinger Premium/Business plans), do NOT upload `vendor`. If you don't use SSH, you MUST upload the `vendor` folder (run `composer install --optimize-autoloader --no-dev` locally first).*
3.  Zip the entire project folder.

## 2. Database Setup (Hostinger)

1.  Log in to your Hostinger hPanel.
2.  Go to **Databases** -> **Management**.
3.  Create a new MySQL Database.
    *   **Database Name**: (e.g., `u123456789_bsu_db`)
    *   **Username**: (e.g., `u123456789_admin`)
    *   **Password**: (Make sure to save this)
4.  Enter phpMyAdmin and **Import** your local database dump (export your local database to a `.sql` file and import it here).

## 3. Uploading Files

1.  Go to **File Manager** in hPanel.
2.  Navigate to `public_html`.
3.  **Ideally**, for security, you should place your application files *outside* `public_html`.
    *   Create a folder named `bsu_app` at the same level as `public_html` (go up one level).
    *   Upload and Extract your zip file into `bsu_app`.
4.  **Public Folder**:
    *   Move the *contents* of `bsu_app/public` into `public_html`.
    *   (Or simpler: keep everything in `public_html` but protect dotfiles. The "Folder Structure" depends on your preference. **Standard Secure Method Below**):

### Recommended Folder Structure for Shared Hosting
*   `/home/u123456789/domains/yourdomain.com/bsu_app` (Contains app, bootstrap, config, vendor, etc.)
*   `/home/u123456789/domains/yourdomain.com/public_html` (Contains contents of your `public` folder: index.php, build, images)

### modifying index.php
If you separated the core files from public files (as recommended):
Edit `public_html/index.php`:

```php
// Change these lines to point to your bsu_app folder
require __DIR__.'/../bsu_app/vendor/autoload.php';
$app = require __DIR__.'/../bsu_app/bootstrap/app.php';
```

## 4. Environment Configuration

1.  In `bsu_app` (or wherever your root is), rename `.env.example` to `.env`.
2.  Edit `.env` with your production details:

```env
APP_NAME="BSU Scholarship"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE (Copy from local .env)
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u123456789_bsu_db
DB_USERNAME=u123456789_admin
DB_PASSWORD=your_password
```

## 5. Storage Linking

Images uploaded to `storage/app/public` are not accessible unless linked.
In Hostinger (via SSH or Cron Job if SSH is unavailable):

**Via SSH (Terminal):**
```bash
cd bsu_app
php artisan storage:link
```
*Note: This creates a symlink from `public/storage` to `storage/app/public`.*




**Method B: The "Helper Route" (No Terminal)**
If you cannot use SSH, you can essentially "run" commands by creating a temporary web route.

1.  Open `routes/web.php` in your File Manager.
2.  Add the following code at the very bottom:

```php
// --- TEMPORARY DEPLOYMENT HELPERS ---
Route::get('/maintenance/link-storage', function () {
    Artisan::call('storage:link');
    return 'Storage Linked successfully. <br> <a href="/">Go Home</a>';
});

Route::get('/maintenance/clear-cache', function () {
    Artisan::call('optimize:clear');
    return 'Cache cleared successfully. <br> <a href="/">Go Home</a>';
});

Route::get('/maintenance/migrate', function () {
    // WARNING: Only use this if you cannot import the SQL file
    // Artisan::call('migrate --force'); 
    // return 'Migrations run successfully.';
    return 'Migration via route is disabled for safety. Please import SQL via phpMyAdmin.';
});
// ------------------------------------
```
3.  Visit `your-domain.com/maintenance/link-storage` in your browser.
4.  Visit `your-domain.com/maintenance/clear-cache` to ensure config is refreshed.
5.  **IMPORTANT:** Once done, remove these lines from `routes/web.php` for security.

**Troubeshooting: "Call to undefined function symlink()"**

If you effectively see this error when visiting the helper route, it means your hosting provider (Hostinger) has disabled the `symlink` function for security.

**Solution: The "Direct Save" Method (Modify Config)**
Instead of symlinking, tell Laravel to save uploaded files directly to the public folder.

1.  Open `config/filesystems.php` in your File Manager.
2.  Find the `'public'` disk configuration (around line 39).
3.  Change the `'root'` path from `storage_path('app/public')` to `public_path('storage')`.

   *Change this:*
   ```php
   'public' => [
       'driver' => 'local',
       'root' => storage_path('app/public'), // <--- OLD
       'url' => env('APP_URL').'/storage',
       'visibility' => 'public',
       'throw' => false,
   ],
   ```

   *To this:*
   ```php
   'public' => [
       'driver' => 'local',
       'root' => public_path('storage'), // <--- NEW: Saves directly to public/storage
       'url' => env('APP_URL').'/storage',
       'visibility' => 'public',
       'throw' => false,
   ],
   ```
4.  **Important**: You must manually create the folder `storage` inside your `public_html` (or `public`) folder if it doesn't exist. Ensure it has write permissions (755 or 777).

**Summary of "No Terminal" Workarounds:**
1.  **Try the Helper Route first.**
2.  **If it fails with `symlink()` error**, edit `config/filesystems.php` as shown above.
3.  **Ensure `public/storage` folder exists.**


## 6. Permissions

Ensure the `storage` and `bootstrap/cache` directories are writable by the web server.

**Standard Settings (Try these first):**
*   **Permissions**: `755`
*   **Owner**: Read, Write, Execute
*   **Group**: Read, Execute
*   **Others**: Read, Execute

**If you still get "Permission Denied" errors:**
On some shared hosting (like Hostinger), you might need to set these specific folders (`storage` and `bootstrap/cache`) to `777` (Read/Write/Execute for ALL).
*   **Permissions**: `777`
*   **Owner**: Read, Write, Execute
*   **Group**: Read, Write, Execute
*   **Others**: Read, Write, Execute

*Security Note: Only apply 777 to `storage` and `bootstrap/cache`, never to the whole project.*

## 7. Mail Configuration (SMTP)

Update the Mail settings in `.env` to send emails (using Hostinger email or Gmail SMTP).

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=no-reply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="no-reply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## 8. Troubleshooting: "Images Not Showing" or "Wrong Public Folder"

If your system is creating a `public` folder inside `BSU_scholarship` instead of using `public_html`, or if uploaded images aren't appearing, it's because Laravel believes the public directory is still the default one.

### The Fix: register() Method

You **MUST** tell Laravel where your public folder is.

1.  Open `app/Providers/AppServiceProvider.php` in your Hostinger File Manager.
2.  Add this code to the `register` method:

```php
public function register(): void
{
    // Fix for Hostinger/Shared Hosting
    $this->app->bind('path.public', function() {
        return base_path('../public_html');
    });
}
```

*   **Why this works:** `base_path` is your `BSU_scholarship` folder. `../public_html` points to the folder one level up, which is your actual public directory.
*   **Result:** `public_path()` will now correctly point to `public_html`.
*   **Verify:** After adding this, re-run the "Storage Link" helper route (Deployment Step 5). It will now create the symlink in `public_html` correctly.

### 1. The filesystem.php Config
Ensure your `config/filesystems.php` uses the `local` driver as default (or remove `FILESYSTEM_DISK` from .env so it defaults to local).

**Do NOT set `FILESYSTEM_DISK=public` in your .env.**
The code specifically saves to `public/profile_pictures`.
- If you use `local` disk (root: `storage/app`), it saves to `storage/app/public/profile_pictures`. (CORRECT, exposed via symlink)
- If you use `public` disk (root: `storage/app/public`), it saves to `storage/app/public/public/profile_pictures`. (WRONG, nested folder)

### 2. Clearing Cache
After modifying `AppServiceProvider.php` or `.env`, you **MUST** clear the cache.
Use the helper route `yourdomain.com/maintenance/clear-cache`.

### 3. Re-link Storage
Once the cache is cleared and `AppServiceProvider` is fixed:
1. Delete the `storage` folder inside `public_html` (if it exists).
2. Visit `yourdomain.com/maintenance/link-storage`.
This ensures the symlink is created in the correct place (`public_html/storage`) pointing to the correct source (`BSU_scholarship/storage/app/public`).

This ensures the symlink is created in the correct place (`public_html/storage`) pointing to the correct source (`BSU_scholarship/storage/app/public`).

### 4. CRITICAL: If you get "Call to undefined function symlink()"
This means Hostinger has disabled symlinks. You **MUST** use the "Direct Save" method.

1.  **Edit `config/filesystems.php`**:
    Change the `public` disk configuration to save directly to your public folder using `base_path`.
    *Find:*
    ```php
    'root' => storage_path('app/public'),
    ```
    *Change to:*
    ```php
    'root' => base_path('../public_html/storage'), 
    ```
    *Why?* `public_path()` in config files runs *before* the AppServiceProvider fix takes effect. Using `base_path('../public_html/storage')` ensures the correct path is used immediately.

2.  **Create Folder Manually**:
    Go to `public_html` and create a folder named `storage` if it doesn't exist.
    Inside it, create a folder named `profile_pictures`.

3.  **Clear Cache**:
    Visit `yourdomain.com/maintenance/clear-cache`.

This bypasses the need for a symlink entirely.

### 5. Why are my images still going to BSU_scholarship/storage?

If your images are still appearing in the hidden folder, it means **Step 1 (Edit `config/filesystems.php`) was skipped or reverted.**

- Accessing `storage/app/public/...` is the **default behavior**.
- You must **force** Laravel to use your public folder by changing that specific line in `config/filesystems.php`.
- Ensure you actually saved the file on the server.

### 6. Debugging: Check Your Config
If you are sure you changed the file but it's not working, run this test.

1.  Add this to `routes/web.php` on the server:
    ```php
    Route::get('/debug-config', function() {
        return [
            'config_root' => config('filesystems.disks.public.root'),
            'public_path' => public_path(),
            'base_path' => base_path(),
        ];
    });
    ```
2.  Visit `yourdomain.com/debug-config`.
3.  **Check `config_root`**:
    - If it says `.../storage/app/public`, your config is **NOT UPDATED** (cache issue or file not saved).
    - If it says `.../public_html/storage`, it **IS UPDATED**.

4. Remove the route after checking.

## 9. Updating Your Live Site

When you make changes locally, you do **not** need to re-upload the entire project. Only upload the modified files.

### Scenario A: You changed "Look and Feel" (Blade, CSS, JS)
1.  **If you changed `.blade.php` files:**
    *   Upload the specific file from `resources/views/...` to `BSU_scholarship/resources/views/...` on the server.
2.  **If you changed CSS or JS (Tailwind, Alpine):**
    *   Run `npm run build` locally.
    *   Delete the old `build` folder on the server (`public_html/build`).
    *   Upload the new local `public/build` folder to `public_html/build`.
    *   *Note: You might need to clear your browser cache to see changes.*

### Scenario B: You changed Logic (Controllers, Models, Routes)
1.  **If you changed a Controller:**
    *   Upload `app/Http/Controllers/YourController.php` to `BSU_scholarship/app/Http/Controllers/`.
2.  **If you changed a Route:**
    *   Upload `routes/web.php` to `BSU_scholarship/routes/`.
3.  **If you added a new Package (Composer):**
    *   This is tricky without SSH. You usually need to upload the entire `vendor` folder again (which is slow).

### Scenario C: You changed the Database
1.  If you created a new migration, you cannot easily run `php artisan migrate` without SSH.
2.  **Best Practice (No Terminal):**
    *   Export your *local* database structure.
    *   Import it into the live database (be careful not to lose live data!).
    *   OR: Write the raw SQL query for the new column/table and run it in the live phpMyAdmin.

### Quick Checklist for "Resources" Updates
If you are just tweaking the UI:
1.  Edit code -> Save.
2.  `npm run build` (if CSS changed).
3.  Upload `resources/views` (modified files).
4.  Upload `public/build` (if CSS changed).
