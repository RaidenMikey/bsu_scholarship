# How to Update Your Word Template Placeholders

## Quick Method (Recommended)

1. **Open your Word template**: `resources/forms/BatStateU-FO-SFA-01_Application Form_Template_Final.docx`

2. **Use Find & Replace in Word:**
   - Press `Ctrl+H` (or `Cmd+H` on Mac)
   - **Find what:** `{{`
   - **Replace with:** `${`
   - Click "Replace All"
   
   - **Find what:** `}}`
   - **Replace with:** `}`
   - Click "Replace All"

3. **Save the document**

## Manual Method (If Find & Replace doesn't work)

If Find & Replace doesn't work (sometimes Word treats these as special characters), you can:

1. **Select each placeholder manually**
2. **Replace `{{variable}}` with `${variable}`**

For example:
- `{{last_name}}` → `${last_name}`
- `{{first_name}}` → `${first_name}`
- `{{birthdate}}` → `${birthdate}`

## Verify Your Placeholders

After updating, make sure all placeholders use this format:
- ✅ `${last_name}` (correct)
- ❌ `{{last_name}}` (won't work)
- ❌ `[[last_name]]` (won't work)

## Test the Print Function

After updating your template:
1. Fill out the application form
2. Click "Print Application"
3. The generated DOCX should have all fields filled in correctly

## Current Placeholders Supported

All these placeholders are already handled in the code:
- Personal Data: `${last_name}`, `${first_name}`, `${age}`, `${birthdate}`, etc.
- Academic Data: `${sr_code}`, `${education_level}`, `${previous_gwa}`, etc.
- Family Data: `${father_name}`, `${mother_name}`, `${estimated_gross_annual_income}`, etc.
- Essay: `${reason_for_applying}`
- Certification: `${student_signature}`, `${date_signed}`

See `resources/forms/TEMPLATE_INSTRUCTIONS.md` for the complete list.

