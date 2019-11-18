<?php
$db_servertype = "mysql";
$db_host = $_SERVER_MYSQL_DB_HOST;
$db_name = $_SERVER_MYSQL_DB_NAME;
$db_user = $_SERVER_MYSQL_DB_USER;
$db_password = $_SERVER_MYSQL_DB_PASSWORD;

$google_client_id = '1058514656051-tder88ho0hk6cjlafq8pcptltvlosdkn.apps.googleusercontent.com';
$google_secret = 'vGji-U27G8-viLNbSnBAeRjz';
$google_redirect_uri = 'http://www.holding.bz/services/gdocs.php';
 
if(preg_match('/local/',$_SERVER['HTTP_HOST']))
{
	define ('SMS', 0);
	define ('HOST', 'local.tasks');
}
else
{
	define ('SMS', 1);
	define ('HOST', $_SERVER['HTTP_HOST']);
}

$postApiLogin = 'oaoKI';
$postApiPass = 'XMqYOEXwbV';

define ('PER_PAGE',20);

define ('USE_G_SERVICES', 1);

define ('KEY_WORD', 'tasks');

define ('CNEWS_PER_PAGE', 5);

define ('HISTORY_PER_PAGE', 3);

define ('DIALOGS_PER_PAGE', 10);

define ('MSG_PER_PAGE', 20);

// Кол-во файлов на странице
define ('PS_PER_PAGE', 5);

// Кол-во файлов на странице
define ('FILES_PER_PAGE', 50);

// Кол-во файлов на странице
define ('FILES_GROUP_PER_PAGE', 10);

// Кол-во трекингов почты России на странице
define ('POSTTR_PER_PAGE', 20);

// Кол-во проектов на странице
define ('PROJECTS_PER_PAGE', 15);

// Кол-во контактов на странице
define ('CONTACTS_PER_PAGE', 5);

// Кол-во контактов на странице
define ('CLIENTS_PER_PAGE', 15);

// Кол-во сделок на странице
define ('DEALS_PER_PAGE', 15);

// Кол-во сделок на странице сделок подчиненных
define ('WORKERS_DEALS_PER_PAGE', 5);

// Кол-во имущества на странице
define ('GOODS_PER_PAGE', 5);

// Кол-во денег на странице
define('MONEY_PER_PAGE', 5);

// Кол-во финансов на странице
define('FINANCES_PER_PAGE', 5);

// Кол-во отзывов на странице
define('COMMENTS_ON_MAIN_PER_PAGE', 2);

// Кол-во выполненных работ за 30 дней на странице
define('TASKS_COMPLETED_ON_MAIN_PER_PAGE', 2);

// Кол-во планирования на странице
define('PLANNING_PER_PAGE', 10);

// Кол-во планирования на странице
define('OFDOCS_PER_PAGE', 10);

// Кол-во выговоров на странице
define('REPRIMANDS_PER_PAGE', 10);

// Кол-во заметок на странице
define('NOTES_PER_PAGE', 10);

// Кол-во отчетов круга обязанностей на странице
define('WORK_REPORTS_PER_PAGE', 5);

// Кол-во элементов в блоках на главной странице
define('LIST_PER_PAGE_ON_MAIN', 2);

define('UPLOAD_FOLDER', 'upload/'.$_SERVER_ID);

// путь до временных файлов
define('TEMP_PATH', $_SERVER['DOCUMENT_ROOT'].'/temp');

// путь до файлов
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'].'/'.UPLOAD_FOLDER);

// путь до файлов
define('FILES_PATH', $_SERVER['DOCUMENT_ROOT'].'/'.UPLOAD_FOLDER.'/uploads');

// папка с общими файлами
define('SHARING_PATH', UPLOAD_PATH.'/files/sh');

// папка с файлами пользователя
define('PRIVATE_PATH', UPLOAD_PATH.'/files/pr');

// папка с файлами клиента
define('CLIENTS_PATH', UPLOAD_PATH.'/files/clients');

// папка с файлами пользователя
define('USERS_PATH', UPLOAD_PATH.'/users');

// папка с файлами имущества
define('GOODS_PATH', UPLOAD_PATH.'/goods');

// папка с файлами имущества
define('CONTACTS_PATH', UPLOAD_PATH.'/contacts');

// папка с файлами записей звонков
define('REC_CALL_PATH', TEMP_PATH.'/rec_call/'.$_SERVER_ID);

// папка с файлами записей логов звонков
define('LOG_REC_PATH', TEMP_PATH.'/log_rec/'.$_SERVER_ID);

// папка с файлами отчетов по кругу обязанностей
define('WORK_REPORTS_PATH', UPLOAD_PATH.'/work_reports');

define('SMS_FROM', 'EasyWork');

// Максимальный размер загружаемого файла в мб
define('UPLOAD_SIZE_LIMIT', 100);
define('UPLOAD_SIZE_LIMIT_IN_BYTES', 104857600);


#####
// Макс. размеры изображений, до которых изображение будет сжиматься для вывода для ПЕРСОНАЛЬНОЙ страницы
$max_upload_preview_user_image_width = 450;
$max_upload_preview_user_image_height = 450;

// Максимальное разрешение загружаемого файла для ПЕРСОНАЛЬНОЙ страницы
$max_upload_user_image_resolution = 5000;
// Минимальное разрешение загружаемого файла для ПЕРСОНАЛЬНОЙ страницы
$min_upload_user_image_resolution = 200;


// Макс. размеры изображений, до которых изображение будет сжиматься для вывода для ПЕРСОНАЛЬНОЙ страницы
$max_upload_preview_goods_image_width = 250;
$max_upload_preview_goods_image_height = 250;

// Макс. размеры изображений, до которых изображение будет сжиматься для вывода для КОНТАКТОВ
$max_upload_preview_contact_image_width = 400;
$max_upload_preview_contact_image_height = 400;

// Максимальное разрешение загружаемого файла для страницы 
$max_upload_goods_image_resolution = 5000;
// Минимальное разрешение загружаемого файла для ПЕРСОНАЛЬНОЙ страницы
$min_upload_goods_image_resolution = 200;
?>
