# Server Conversion / Override Log

This document tracks files that **MUST be modified** on the Hostinger server after being uploaded from local.
**Do not apply these changes to your local development environment.**

These "Server Conversions" are necessary because of the folder structure differences (e.g., `public_html` vs `public`) and security configurations.

---

## 1. `public/index.php`
*   **Location on Server**: `public_html/index.php`
*   **Purpose**: Points the web server to the correct application bootstrap files.
*   **The Change**:
    Update the `require` paths to point to your `bsu_app` folder (one level up).

    ```php
    // --- SERVER VERSION ---
    
    if (file_exists(__DIR__.'/../BSU_scholarship/storage/framework/maintenance.php')) {
        require __DIR__.'/../BSU_scholarship/storage/framework/maintenance.php';
    }

    require __DIR__.'/../BSU_scholarship/vendor/autoload.php';
    
    $app = require __DIR__.'/../BSU_scholarship/bootstrap/app.php';
    ```

---

## 2. `app/Providers/AppServiceProvider.php`
*   **Location on Server**: `BSU_scholarship/app/Providers/AppServiceProvider.php`
*   **Purpose**: Fixes "Vite manifest not found" by telling Laravel that `public` is actually `public_html`.
*   **The Change**:
    Add the binding code to the `register()` method.

    ```php
    // --- SERVER VERSION ---
    
    public function register()
    {
        // Bind public path to public_html
        $this->app->bind('path.public', function() {
            return base_path('../public_html');
        });
    }
    ```

---

## 3. `config/filesystems.php`
*   **Location on Server**: `BSU_scholarship/config/filesystems.php`
*   **Purpose**: Fixes "Call to undefined function symlink()" and "Storage not linked" errors.
*   **The Change**:
    Modify the `'public'` disk to save directly to the public folder.

    ```php
    // --- SERVER VERSION ---
    
    'public' => [
        'driver' => 'local',
        // Change from storage_path('app/public') to:
        'root' => public_path('storage'), 
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
        'throw' => false,
    ],
    ```

---

## 4. `.env`
*   **Location on Server**: `BSU_scholarship/..env`
*   **Purpose**: Production configuration.
*   **The Change**:
    Ensure these values are set for production. **NEVER upload your local .env.**

    ```env
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://bsu-scholarship-system.online
    
    # Live Database Credentials
    DB_DATABASE=u798335995_bsu_db
    DB_USERNAME=u798335995_admin
    DB_PASSWORD=...
    ```

---

## 5. Favicon & Assets (Troubleshooting)
*   **Issue**: Favicon or images load locally but break on server.
*   **Cause**: Relative paths or `public_path` mismatch.
*   **The Fix**:
    1.  Ensure you have applied the **AppServiceProvider** fix (Item #2 above). This makes `asset('images/logo.png')` generate the correct URL.
    2.  If strictly the **favicon** (browser tab icon) is missing:
        *   Ensure `favicon.ico` is uploaded to `public_html/favicon.ico`.
        *   HTML Check: In `resources/views/layouts/app.blade.php` (or home), ensure the link uses `asset()`:
            ```html
            <link rel="icon" type="image/png" href="{{ asset('images/Batangas_State_Logo.png') }}">
            ```
            *Avoid using hardcoded paths like `/images/logo.png`.*

---

### Workflow for Updates
1.  **Modify code locally.**
2.  **Upload file to server.**
3.  **CHECK:** Is this file one of the 4 files above?
    *   **NO**: You are done.
    *   **YES**: You must **Re-Apply** the "Server Conversion" changes to that file on the server immediately after uploading.
