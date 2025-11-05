# LibreOffice Installation Guide

## Why LibreOffice is Needed

The application form "Save and Print" button converts the filled DOCX template to PDF format. This requires LibreOffice to be installed on your server.

## Installation Instructions

### Windows (XAMPP)

1. **Download LibreOffice:**
   - Visit: https://www.libreoffice.org/download/download/
   - Download the Windows 64-bit version (or 32-bit if your system is 32-bit)

2. **Install LibreOffice:**
   - Run the installer
   - Follow the installation wizard
   - **Default installation path:** `C:\Program Files\LibreOffice\`
   - The executable will be at: `C:\Program Files\LibreOffice\program\soffice.exe`

3. **Verify Installation:**
   - Open Command Prompt (cmd)
   - Run: `"C:\Program Files\LibreOffice\program\soffice.exe" --version`
   - You should see the version number

4. **Optional - Add to PATH (for easier access):**
   - Add `C:\Program Files\LibreOffice\program\` to your system PATH
   - This allows using `soffice` command from anywhere

### Linux

```bash
# Ubuntu/Debian
sudo apt-get update
sudo apt-get install libreoffice

# CentOS/RHEL
sudo yum install libreoffice

# Verify installation
libreoffice --version
```

### macOS

```bash
# Using Homebrew
brew install --cask libreoffice

# Or download from: https://www.libreoffice.org/download/download/
```

## Testing After Installation

After installing LibreOffice, test the conversion:

1. Go to the application form
2. Fill out and save the form
3. Click "Save and Print"
4. You should receive a PDF download instead of DOCX

## Troubleshooting

### If PDF conversion still fails:

1. **Check if LibreOffice is installed:**
   ```cmd
   where soffice
   ```

2. **Check Laravel logs:**
   - Check `storage/logs/laravel.log` for LibreOffice-related errors

3. **Manual test:**
   ```cmd
   "C:\Program Files\LibreOffice\program\soffice.exe" --headless --convert-to pdf --outdir "C:\temp" "path\to\your\file.docx"
   ```

4. **If LibreOffice is in a non-standard location:**
   - You can modify the `findLibreOffice()` method in `FormPrintController.php`
   - Or add the path to your `.env` file and create a config option

## Alternative Solutions

If you cannot install LibreOffice on the server, you have these options:

1. **Use a cloud conversion service** (e.g., CloudConvert API)
2. **Use a PHP library** (e.g., `setasign/fpdi` + `tecnickcom/tcpdf`)
3. **Keep DOCX format** (current fallback behavior)

## Notes

- LibreOffice must be installed on the **server** where Laravel is running
- For XAMPP on Windows, install LibreOffice on the same Windows machine
- The conversion happens server-side, not in the browser

