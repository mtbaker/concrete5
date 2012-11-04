# Installation Instructions

1. Unzip this file in your site's packages/ directory.
2. Login to your site as an administrator.
3. Find the "Add Functionality" page in your dashboard.
4. Find this package in the list of packages awaiting installation.
5. Click the "install" button.

# Background

The block has two templates, full and small. If you'd like to edit them,
find them in `web/packages/snow_report/blocks/snow_report/templates/**/view.php`.

If you look in `web/packages/snow_report/blocks/snow_report/controller.php` in the
`fetchReport()` method, you'll see the list of variables available. Alternately,
you can add a `var_dump(get_defined_vars());` to the template to see the list of
local vars in the template.

# Error Handling

This block will connect to the previous Joomla database to fetch the report. If it
is unable to fetch a report, it will display an error message with the snow phone
and triggered a throttled email. It will email hourly.

 