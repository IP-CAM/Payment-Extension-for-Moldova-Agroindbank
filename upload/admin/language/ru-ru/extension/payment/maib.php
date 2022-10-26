<?php
// Heading
$_['heading_title'] = 'MAIB';
$_['text_maib'] = '<img src="view/image/payment/maib.png" alt="MAIB" title="MAIB" />';
// Text
$_['text_extensions'] = 'Расширения';
$_['text_success'] = 'Успех: настройки MAIB изменены!';
$_['text_edit'] = 'Изменить настройки оплаты MAIB';

// Intro help
$_['obtain_certificate'] = 'Зарегистрируйте свой сайт в MAIB';
$_['obtain_certificate_help'] = 'Вы должны отправить в банк (ecom@maib.md) IP вашего сервера и обратный URL. <br>После подтверждения вы сможете протестировать платежный шлюз и, в случае успеха, получить индивидуальный сертификат pfx. <br>URL возврата';
$_['extract_certificate_help'] = 'Извлечь pem-ключ и сертификат из pfx-файла, отправленного банком';

// Payment method
$_['entry_payment_method'] = 'Предпочтительный способ оплаты';
$_['entry_payment_method_sms'] = 'Capture - Мгновенный перевод денег (SMS).';
$_['entry_payment_method_dms'] = 'Authorize - сумма заблокирована, для завершения транзакции требуется дополнительное подтверждение (DMS).';

// Entry
$_['entry_private_key_file'] = 'Путь к файлу ключа';
$_['entry_private_key_file_help'] = 'Абсолютный или относительный системный каталог сайта (DIR_SYSTEM).';
$_['entry_private_key_password'] = 'Пароль ключа (если есть):';
$_['entry_public_key_file'] = 'Путь к файлу сертификата';
$_['entry_public_key_file_help'] = 'Абсолютный или относительный системный каталог сайта (DIR_SYSTEM).';

// Urls
$_['entry_mode'] = 'Режим / Какие URL использовать';
$_['entry_redirect_url'] = 'URL клиента перенаправления';
$_['entry_merchant_url'] = 'URL обработчика продавца';

// Debug
$_['entry_debug'] = 'Отладка транзакций';
$_['entry_debug_help'] = 'Записывать подробную информацию о запросах транзакций в файл DIR_LOGS/maib_requests.log..';

// Common entries
$_['entry_total'] = 'Сумма';
$_['entry_total_help'] = 'Общая сумма заказа должна быть достигнута, прежде чем этот способ оплаты станет активным.';
$_['entry_order_status'] = 'Статус оплаченного заказа';
$_['entry_order_pending_status'] = 'Статус заказа для незавершенных неподтвержденных транзакций maib';
$_['entry_geo_zone'] = 'Геозона';
$_['entry_status'] = 'Статус';
$_['entry_sort_order'] = 'Порядок сортировки';
$_['entry_last_closed_day'] = 'Последний день рабочий день закрыт';

// Errors
$_['error_permission'] = 'У вас нет разрешения на изменение платежа MAIB!';
$_['error_empty_field'] = 'Это поле не должно быть пустым!';
$_['error_key_file_not_found'] = 'Файл не найден!';
$_['error_key_file_not_match'] = 'Ключ не соответствует сертификату!';

// Cron
$_['enable_cron'] = 'Активировать КРОН';
$_['enable_cron_help'] = 'Убедитесь, что задание cron вызовет закрытие рабочего дня где-то около полуночи. Можно использовать wget, curl и т.п.<br>Пример строки unix crontab(5)';

// SameSite Cookies
$_['entry_fix_cookies_label'] = 'Временное решение для потери сеанса/cookie';
$_['entry_fix_cookies'] = 'Если атрибут SameSite cookie не установлен, по умолчанию используется значение SameSite=Lax, что предотвращает отправку cookie в межсайтовом запросе.<br>Такое поведение защищает пользовательские данные от случайной утечки третьим лицам.<br>Из-за такого поведения при возврате с сайта банка после успешной оплаты сеанс пользователя, выбранный язык и валюта теряются.<br>Если у вас нет других решений для этой проблемы (расширение contrib, определенные настройки php или веб-сервера), вы можете отметить какие cookie должны быть перезаписаны с помощью <i>SameSite=None;Secure</i>, чтобы сохранить сеанс, язык или валюту в зависимости от ваших настроек.<br>Обратите внимание, что это будет работать, только если используется <i>https</i>.';
$_['entry_fix_session_cookie'] = 'Добавить SameSite=None в cookie сеанса';
$_['entry_fix_language_cookie'] = 'Добавить SameSite=None в языковой cookie';
$_['entry_fix_currency_cookie'] = 'Добавить SameSite=None в cookie валюты';
?>
