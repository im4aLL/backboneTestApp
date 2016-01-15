<?php
header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");

require_once __DIR__.'/class.localdb.php';
$db = new LocalDatabase('db/');
$table = 'todo';


function retrievePostData() {
    if (count($_POST)) {
        return $_POST;
    }
    elseif ($post_data = file_get_contents('php://input')) {
        if ($post_json = json_decode($post_data, TRUE)) {
            return $post_json;
        }
        else {
            parse_str($post_data, $post_variables);
            if (count($post_variables)) {
                return $post_variables;
            }
        }
    }
    return FALSE;
}


$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET')
{
    if(isset($_GET['fakerestapi'])) {
        $array = $db->loadSingleArray($table, $_GET['fakerestapi']);
    }
    else {
        $array = $db->loadArray($table);
    }

    echo json_encode($array);
}
elseif($method == 'POST')
{
    $post_data = retrievePostData();
    $post_data['id'] = uniqid();
    $db->insertArray($table, $post_data);
    echo json_encode(['success' => true]);
}
elseif($method == 'PUT')
{
    $post_data = retrievePostData();
    $db->updateArray($table, $post_data, $post_data['id']);
    echo json_encode(['success' => true]);
}
elseif($method == 'DELETE')
{
    $db->deleteArray($table, $_GET['fakerestapi']);
    echo json_encode(['success' => true]);
}
