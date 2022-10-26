<?php
// Heading
$_['heading_title'] = 'MAIB';
$_['text_maib'] = '<img src="view/image/payment/maib.png" alt="MAIB" title="MAIB" />';
// Text
$_['text_extensions'] = 'Extensions';
$_['text_success'] = 'Success: MAIB settnigs changed!';
$_['text_edit'] = 'Edit MAIB payment settings';

// Intro help
$_['obtain_certificate'] = 'Register your site with MAIB';
$_['obtain_certificate_help'] = 'You shoud send to the bank (ecom@maib.md) your server IP and return URL. <br>After confirmation you will be able to test the payment gateway and, if successfull, obtain individual pfx certificate. <br>Return URL';
$_['extract_certificate_help'] = 'Extract pem key and certificate from pfx file sent by bank';

// Payment method
$_['entry_payment_method'] = 'Preferred payment method';
$_['entry_payment_method_sms'] = 'Capture - Transfer money instantly (SMS).';
$_['entry_payment_method_dms'] = 'Authorize - Amount is blocked, further confirmation is required to end transaction (DMS).';

// Entry
$_['entry_private_key_file'] = 'Path for private key file';
$_['entry_private_key_file_help'] = 'Absolute or relative to site system directory (DIR_SYSTEM).';
$_['entry_private_key_password'] = 'Private key password (if any):';
$_['entry_public_key_file'] = 'Path for certificate file';
$_['entry_public_key_file_help'] = 'Absolute or relative to site system directory (DIR_SYSTEM).';

// Urls
$_['entry_mode'] = 'Mode / Which urls to use';
$_['entry_redirect_url'] = 'Redirect client Url';
$_['entry_merchant_url'] = 'Merchant handler Url';

// Debug
$_['entry_debug'] = 'Debug transactions';
$_['entry_debug_help'] = 'Log detailed info about transaction requests to the DIR_LOGS/maib_requests.log file.';

// Common entries
$_['entry_total'] = 'Total';
$_['entry_total_help'] = 'The checkout total the order must reach before this payment method becomes active';
$_['entry_order_status'] = 'Payed order status';
$_['entry_order_pending_status'] = 'Order status for pending-unconfirmed maib transactions';
$_['entry_geo_zone'] = 'Geo Zone';
$_['entry_status'] = 'Status';
$_['entry_sort_order'] = 'Sort Order';
$_['entry_last_closed_day'] = 'Last date business day closed';

// Errors
$_['error_permission'] = 'You do not have permission to modify payment MAIB!';
$_['error_empty_field'] = 'This field must not be empty!';
$_['error_key_file_not_found'] = 'File not found!';
$_['error_key_file_not_match'] = 'The private key does not corresponds to certificate!';

// Cron
$_['enable_cron'] = 'Enable CRON';
$_['enable_cron_help'] = 'Make sure a cron job will trigger closing of business day somewhere around midnight. Wget, curl or similar can be used.<br>Example of unix crontab(5) line';

// SameSite Cookies
$_['entry_fix_cookies_label'] = 'Lost session/cookies workaround';
$_['entry_fix_cookies'] = 'If a cookie’s SameSite attribute is not set, it defaults to SameSite=Lax, which prevents the cookie from being sent in a cross-site request.<br>This behavior protects user data from accidentally leaking to third parties and cross-site request forgery.<br>Because of this behavior, on return from bank site, after a successful payment, user session, selected language and currency are lost.<br>If you do not have other solutions for this issue (contrib extension, php or web server specific settings), you can check which cookies should be overwritten with <i>SameSite=None;Secure</i> in order to preserve session, language or currency based on your setup.<br>Please note, this will work only if <i>https</i> is used.';
$_['entry_fix_session_cookie'] = 'Add SameSite=None to session cookie';
$_['entry_fix_language_cookie'] = 'Add SameSite=None to language cookie';
$_['entry_fix_currency_cookie'] = 'Add SameSite=None to currency cookie';
?>
