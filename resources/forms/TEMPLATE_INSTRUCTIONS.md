# Application Form Template Instructions

## Template Location
Your template file: `resources/forms/BatStateU-FO-SFA-01_Application Form_Template_Final.docx`

## Important: Template Format

**PHPWord requires placeholders in `${variable}` format in your Word document.**

In your Word template, use: `${last_name}` not `{{last_name}}` or `[[last_name]]`

## How It Works

1. **Form Submission**: The FormController handles separate birthdate inputs (mm/dd/yyyy) and combines them server-side if JavaScript hasn't already done so.

2. **Print Function**: The printApplication method:
   - Loads your template from `resources/forms/`
   - Replaces all `${variable}` placeholders with form data
   - Formats dates automatically (mm/dd/yyyy)
   - Converts income brackets to readable text
   - Downloads the filled document

## Available Placeholders

Use these placeholders in your Word template (with `${}` format):

### Personal Data
- `${last_name}`, `${first_name}`, `${middle_name}`
- `${age}`, `${sex}`, `${civil_status}`
- `${birthdate}` (full date: mm/dd/yyyy)
- `${birthdate_month}`, `${birthdate_day}`, `${birthdate_year}` (separate parts)
- `${birthplace}`, `${email}`, `${contact_number}`
- `${street_barangay}`, `${town_city}`, `${province}`, `${zip_code}`
- `${citizenship}`, `${disability}`, `${tribe}`

### Academic Data
- `${sr_code}`, `${education_level}`, `${program}`
- `${college_department}`, `${year_level}`, `${campus}`
- `${previous_gwa}`, `${honors_received}`, `${units_enrolled}`
- `${scholarship_applied}`, `${semester}`, `${academic_year}`
- `${has_existing_scholarship}` (shows "Yes" or "No")
- `${existing_scholarship_details}`

### Family Data
- `${father_status}`, `${father_name}`, `${father_address}`
- `${father_contact}`, `${father_occupation}`
- `${mother_status}`, `${mother_name}`, `${mother_address}`
- `${mother_contact}`, `${mother_occupation}`
- `${estimated_gross_annual_income}` (full text like "Not over P 250,000.00")
- `${siblings_count}`

### Essay/Question
- `${reason_for_applying}`

### Certification
- `${student_signature}`, `${date_signed}` (mm/dd/yyyy)

## Notes

- Empty fields will be replaced with empty strings
- Dates are formatted as mm/dd/yyyy
- Income brackets are converted to full readable text
- Status fields show "Living" or "Deceased" (capitalized)

