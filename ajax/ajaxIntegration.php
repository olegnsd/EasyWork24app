<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/startup.php';
ini_set('display_errors', 0);
// Класс авторизации
$auth = new CAuth($site_db);

$mode = $_REQUEST['mode'];

$current_user_id = $auth->get_current_user_id();

if(!$current_user_id)
{
    exit();
}

switch($mode)
{
    // миелофон
    case 'mielofon_save':

        $active = value_proc($_POST['active']);

        $sql = "SELECT * FROM tasks_integration WHERE `type`='mielofon'";

        $r=$site_db->query_firstrow($sql);


        if($active) {
            $key = generate_rand_string(35);
        }


        if(1) {

            $data = ['active' => $active, 'key' => $key];
        }

        $data = json_encode($data);

        if($r['id']) {

            $sql = "UPDATE tasks_integration SET data='$data' WHERE `type` = 'mielofon'";

            $site_db->query($sql);

        }
        else {

            $sql = "INSERT INTO tasks_integration SET `type` = 'mielofon', data='$data'";

            $site_db->query($sql);

        }


        // Возвращаем результат
        echo json_encode(array('key' => $key));

        break;

}

?>