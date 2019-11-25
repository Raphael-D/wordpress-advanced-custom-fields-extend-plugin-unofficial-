# CUSTOME FIELDS AUTO EXPORTER
1. Create custom fields by Adv Custom Fields.
- You need Wordpress plugin "Advanced Custom Fields".
- The display condition is "equal for post categories".
- Exist naming rule of field name. Example, "$category-slug_XXXXX".

2. Create function the "Check exist custom fields to display post" on child theme's functions.php.
- You're need write this function is behavior the "which exist to display post?", to child theme's functions.php.
- Return the "true" of case is exist custom fields, return "false" case does not exist.

3. The function of "create_custom_fields()" is for display side custom field element.
- This function is generate exort custom field at post display side.
- Apply the CSS class name by category slug at the CSS class of part "custom-field-table-'. $category_state .'".

