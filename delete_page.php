<?php
require_once "./includes/connection.php";
require_once "./includes/operatons.php";

if (intval($_GET["page"]) == 0) { //If we didn't get a valid page id we decline further exec
    redirect_to("content.php");
}
$id = $_GET["page"];
if (get_selected_id("pages", $id)) {
    if (delete_record($connection, "pages", ["id" => $id])) {
        redirect_to("content.php");
    } else {
        echo "<p>Page delete failed</p>";
        echo "<a href='content.php'>Return to main page</a>";
    }
} else {
//        page doesn't exist in the database
    redirect_to("content.php");
}

if (isset($connection)) {
    $connection = null;
}
?>