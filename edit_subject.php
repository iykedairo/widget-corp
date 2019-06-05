
<?php
include_once "./includes/connection.php";
require_once "./includes/operatons.php";
include_once "./includes/header.php";



if (intval($_GET["subj"]) == 0) { //If we didn't get a valid subject id we decline further exec
        redirect_to("content.php");
    }

    if (isset($_POST["submit"])) {
        if ( $check = screen_for_empty("menu_name, position, visible", $_POST)) {
            echo $check;
        } else {
            $id = $_GET["subj"];
            $menu_name = $_POST["menu_name"];
            $position = $_POST["position"];
            $visible = $_POST["visible"];
            $fields = ["menu_name" => $menu_name, "position" => $position, "visible" => $visible];
            $clauses = ["id" => $id];
            if (patch($connection, "subjects", $fields, $clauses)) {
                $message = "Records inserted successfully!";
//                redirect_to("content.php");
            } else {
                $message = "Some issues were encountered";
            }
        }
    }





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
            <h2>Edit subject: <?php echo $selected_subject["menu_name"]; ?></h2>
            <?php
            if (isset($message) && !empty($message)) {
                echo "<p style='font-size: larger'>{$message}</p>";
            }
            ?>
            <form action="edit_subject.php?subj=<?php echo urlencode($selected_subject["id"]); ?>" method="post">

                <p>Subject name: <input type="text" name="menu_name" id="menu_name" value="<?php echo $selected_subject["menu_name"]; ?>"></p>

                <p>Position:
                    <select name="position">
                        <?php
                        $count = 0;
                        retrieve($connection,"subjects", "*", [],
                            function($v, $k, $r) use (&$count, $selected_subject) {
                                $count++;
                                if ($count == $selected_subject["position"]) {
                                    echo "<option selected value='{$count}'>$count</option>";
                                } else {
                                    echo "<option value='{$count}'>$count</option>";
                                }
                            }, " ORDER BY POSITION ASC");
                        $count++;
                        echo "<option value='{$count}'>$count</option>"; //Simulating next postion
                        ?>

                    </select>
                </p>

                <p>Visible:
                    <input type="radio" <?php if ($selected_subject["visible"] == 0) {echo " checked ";} ?> name="visible" value="0"> No&nbsp;
                    <input type="radio" <?php if ($selected_subject["visible"] == 1) {echo " checked ";} ?> name="visible" value="1"> Yes
                </p>

                <input type="submit" value="Update Subject" name="submit">

                &nbsp; &nbsp;
                <a href="delete_subject.php?subj=<?php echo urlencode($selected_subject['id']) ?>"
                   onclick=" return confirm('Are you sure you want to delete <?php echo $selected_subject["menu_name"] ?>');">

                    Delete <?php echo $selected_subject["menu_name"] ?>
                </a>
                    &nbsp;

            </form>
            <br />
            <p><a href="content.php">Cancel</a></p>
            <div>
                <hr/>
                <h3>Pages under <?php echo $selected_subject["menu_name"] ?></h3>
                <p>
                <a href="new_page.php?subj=<?php echo urlencode($selected_subject['id']) ?>">
                Add a new page to <?php echo $selected_subject["menu_name"] ?>
                </a>
                <ul>
                    <?php
                    $output = "";
                    retrieve($connection,"pages", " * ", ["subject_id" => $selected_subject["id"]],
                        function ($page) use(&$output) {
                            global $selected_page;
                            $pg = urldecode($page["id"]);
                            $output .=  "<li> <a href='content.php?page=$pg'> {$page['menu_name']}</a></li>";
                        }, " ORDER BY position ASC");
                    echo $output;
                    ?>
                </ul>
                </p>
            </div>
        </td>
    </tr>
</table>
<?php require "./includes/footer.php"; ?>

  