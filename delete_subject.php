<?php 
require_once "./includes/connection.php";
require_once "./includes/operatons.php";

if (intval($_GET["subj"]) == 0) { //If we didn't get a valid subject id we decline further exec
        redirect_to("content.php");
}
$id = $_GET["subj"];
    if (get_selected_id("subjects", $id)) {
    if (delete_record($connection, "subjects", ["id" => $id])) {
    redirect_to("content.php");
    } else {
    echo "<p>Subject delete failed</p>";
    echo "<a href='content.php'>Return to main page</a>";
    }
} else {
//        Subject doesn't exist in the database
        redirect_to("content.php");
    }

    if (isset($connection)) {
    $connection = null;
}
?>