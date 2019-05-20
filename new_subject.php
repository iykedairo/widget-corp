
<?php
include_once "./includes/connection.php";
require_once "./includes/operatons.php";
include_once "./includes/header.php";

selection(); //Pulls in page and subject selection procedures
?>

<table id="structure">
    <tr>
        <td id="navigation">
            <ul class="subjects">
                <?php
               echo navigation();
                ?>
            </ul>
        </td>
        <td id="page">

        </td>
    </tr>
</table>
<?php require "./includes/footer.php"; ?>

  