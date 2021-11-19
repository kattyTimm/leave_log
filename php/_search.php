<?php
 include_once $_SERVER['DOCUMENT_ROOT'].'/orgcomm/php/_dbcomm.php';

$form_data = [];    //Pass back the data

$context = trim($_GET['srch_box']);

$db = new _dbcomm();
$result = $db->search_get_departCoincidence($context);
unset($db);

$form_data['query'] = $context;
$form_data['suggestions'] = $result;

//Return the data back
echo json_encode($form_data);
?>
