# Debug Form Print Issues

## Common Issues:

1. **Form Instance Mismatch**: The form being displayed might be different from the form being saved/printed
2. **Placeholder Format**: PHPWord expects `${variable}` but template might use `{{variable}}` or `[[variable]]`
3. **Data Not Saved**: Some fields might not be getting saved to the database
4. **Cached Data**: The form model might be using cached/stale data

## Debugging Steps:

1. Check which form is being retrieved:
   - Log the form ID, scholarship_id, and updated_at timestamp
   - Compare with what's displayed in the form

2. Check placeholder format in template:
   - Open the DOCX template
   - Check if placeholders use `${variable}`, `{{variable}}`, or `[[variable]]`

3. Check database values:
   - Query the forms table directly
   - Compare saved values with what's in the form

4. Check replacement logic:
   - The code extracts variable names from placeholders
   - But PHPWord expects just the variable name without delimiters

