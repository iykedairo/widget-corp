
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
            <h2>Add subject</h2>
            <form action="create_subject.php" method="post">

                <p>Subject name: <input type="text" name="menu_name" id="menu_name" value=""></p>

                <p>Position:
                    <select name="position">
                        <?php
                        $count = 0;
                        retrieve($connection,"subjects", "*", [],
                            function($v, $k, $r) use (&$count) {
                                $count++;
                                echo "<option value='{$count}'>$count</option>";
                            }, " ORDER BY POSITION ASC");
                        $count++;
                        echo "<option value='{$count}'>$count</option>"; //Simulating next postion
                        ?>

                    </select>
                </p>

                <p>Visible:
                    <input type="radio" name="visible" value="0"> No&nbsp;
                    <input type="radio" name="visible" value="1"> Yes
                </p>

                <input type="submit" value="Add Subject">

            </form>
            <br />
            <a href="content.php">Cancel</a>
        </td>
    </tr>
</table>
<?php require "./includes/footer.php"; ?>

  