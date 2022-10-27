<?php
// Heading
$_['heading_title'] = 'MAIB';
$_['text_maib'] = '<img src="/extension/maib/admin/view/image/payment/maib.png" alt="MAIB" title="MAIB" />';
// Text
$_['text_extensions'] = 'Extensions';
$_['text_success'] = 'Succes: setările MAIB au fost modificate!';
$_['text_edit'] = 'Modificare setări pentru plățile MAIB';

// Intro help
$_['obtain_certificate'] = 'Înregistrați-vă site-ul cu MAIB';
$_['obtain_certificate_help'] = 'Trebuie să expediați către bancă (ecom@maib.md) IP-ul serverului și adresa URL de returnare. <br>După confirmare, veți putea testa gateway-ul de plată, după care veți obține certificatul individual pfx. <br>Adresa URL de returnare';
$_['extract_certificate_help'] = 'Extrageți cheia pem și certificatul din fișierul pfx oferit de bancă';

// Payment method
$_['entry_payment_method'] = 'Preferred payment method';
$_['entry_payment_method_sms'] = 'Capture - Transfer money instantly (SMS).';
$_['entry_payment_method_dms'] = 'Authorize - Amount is blocked, further confirmation is required to end transaction (DMS).';

// Entry
$_['entry_private_key_file'] = 'Calea către cheia privată';
$_['entry_private_key_file_help'] = 'Absolută sau relativă la directorul de sistem al site-ului (DIR_SYSTEM).';
$_['entry_private_key_password'] = 'Parola pentru cheia privată (dacă există):';
$_['entry_public_key_file'] = 'Calea către certificat';
$_['entry_public_key_file_help'] = 'Absolută sau relativă la directorul de sistem al site-ului (DIR_SYSTEM).';

// Urls
$_['entry_mode'] = 'Mod / Ce adrese URL să utilizați';
$_['entry_redirect_url'] = 'Adresa URL pentru redirecționare a clientului';
$_['entry_merchant_url'] = 'URL API a comerciantului';

// Debug
$_['entry_debug'] = 'Depanați tranzacțiile';
$_['entry_debug_help'] = 'Înregistrați informații detaliate a tranzacțiilor în fișierul DIR_LOGS/maib_requests.log.';

// Common entries
$_['entry_total'] = 'Total';
$_['entry_total_help'] = 'Totalul de plată pe care trebuie să-l atingă comanda înainte ca această metodă de plată să devină activă';
$_['entry_order_status'] = 'Starea comenzii plătite';
$_['entry_order_pending_status'] = 'Starea comenzii pentru tranzacțiile maib în așteptare-neconfirmate';
$_['entry_geo_zone'] = 'Zona geografică';
$_['entry_status'] = 'Statut';
$_['entry_sort_order'] = 'Ordinea sortare';
$_['entry_last_closed_day'] = 'Ultima dată a fost apelată închiderea zilei lucrătoare';

// Errors
$_['error_permission'] = 'Nu aveți permisiunea de a modifica setările MAIB!';
$_['error_empty_field'] = 'Acest câmp nu trebuie să fie gol!';
$_['error_key_file_not_found'] = 'Fișierul nu a fost găsit!';
$_['error_key_file_not_match'] = 'Cheia privată nu corespunde certificatului!';

// Cron
$_['enable_cron'] = 'Activați CRON';
$_['enable_cron_help'] = 'Asigurați-vă că cron job-urile OpenCart sunt configurate corect și că vor declanșa închiderea zilei lucrătoare undeva pe la miezului nopții.<br>Pentru mai multe informații accesați <i>Extensii &raquo; Cron Jobs</i>.';

// SameSite Cookies
$_['entry_fix_cookies_label'] = 'Sesiune pierdută/soluție pentru cookie-uri';
$_['entry_fix_cookies'] = 'Dacă atributul SameSite al unui cookie este Lax sau Strict, acestea nu sunt trimise într-o solicitare între site-uri.<br>Acest comportament protejează datele utilizatorului împotriva scurgerii accidentale către terți și falsificarea cererilor pe mai multe site-uri.<br>Din cauza acestui comportament, la întoarcerea de pe site-ul băncii, după o plată cu succes, sesiunea utilizatorului este pierdută.<br>Pentru a remedia această problemă, setați <i>SameSite</i> la <b>Niciuna</b> (<b>Sistem &raquo; Setări &raquo; Editați magazinul dvs. &raquo; Server &raquo; Session Samesite Cookie</b>).';
