<?php
require_once "./includes/connection.php";
require_once "./includes/operatons.php";

if (($errors = screen_for_empty("menu_name", $_REQUEST))) {
//    echo($errors);
    redirect_to("new_subject.php");
    echo $errors;
    die();
}

$menu_name = $_POST["menu_name"];
$position = $_POST["position"];
$visible = $_POST["visible"];


// menu_name is quotes because it's string but postion and visible aren't
$query = "INSERT INTO subjects (menu_name, position, visible) 
          VALUES('{$menu_name}', {$position}, {$visible})";
$array = ["menu_name" => $menu_name, "position" => $position, "visible" => $visible];

$request = store($connection, "subjects", $array, "");
if ($request["success"] === true) {
//    Success
    header("Location: content.php");
    exit;
} else {
    echo $request["message"];
}

?>









<?php

if (isset($connection)) {
    $connection = null;
}
?>
