<?php
include_once "./includes/session.php";
include_once "./includes/connection.php";
require_once "./includes/operatons.php";
include_once "./includes/header.php";

$q = "SELECT hashed_password, username, id FROM users WHERE username = ?";
$arr = ["Michael"];
$stmt = $connection->prepare($q);
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$result = $stmt->execute($arr);
$result = $stmt->fetchAll();
show($result);

?>

<h1>Checks are done here!</h1>
<?php require "./includes/footer.php"; ?>

