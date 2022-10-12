<?php
// Heading
$_['heading_title'] = 'MAIB';
$_['text_maib'] = '<img src="/extension/maib/admin/view/image/payment/maib.png" alt="MAIB" title="MAIB" />';
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
$_['enable_cron_help'] = 'Make sure OpenCart cron jobs are setup properly and will trigger closing of business day somewhere around midnight.<br>Got to <i>Extensions &raquo; Cron Jobs</i> for more info.';
