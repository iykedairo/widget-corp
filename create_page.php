<?php
require_once "./includes/connection.php";
require_once "./includes/operatons.php";

if (($errors = screen_for_empty("menu_name, visible, position, content, subject_id", $_REQUEST))) {
    $path = "new_page.php?err=".urlencode($errors)."&subj=".urlencode($_GET["subj"]);
    redirect_to($path);
}
$array = [
"menu_name" => $_POST["menu_name"],
"position" => $_POST["position"],
"visible" => $_POST["visible"],
"subject_id" => $_POST["subject_id"],
"content" => $_POST["content"]
];

// store($connection, "pages", $array, []);
$request = store($connection, "pages", $array, []);
if ($request["success"] === true) {
//    Success
     redirect_to("content.php?page=3}");
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
