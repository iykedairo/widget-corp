
<?php
include_once "./includes/connection.php";
require_once "./includes/operatons.php";
include_once "./includes/header.php";



if (intval($_GET["page"]) == 0) { //If we didn't get a valid page id we decline further exec
    redirect_to("content.php");
}

if (isset($_POST["submit"])) {
    if ( $check = screen_for_empty("menu_name, position, subject_id, visible, content", $_POST)) {
        echo $check;
    } else {
        $fields = [
        "menu_name" => $_POST["menu_name"],
        "position" => $_POST["position"],
        "subject_id" => $_POST["subject_id"],
        "visible" => $_POST["visible"],
        "content" => $_POST["content"]
    ];
        
        $clauses = ["id" => $_GET["page"]];
        if (patch($connection, "pages", $fields, $clauses)) {
            $message = "Records inserted successfully!";
               redirect_to("content.php");
        } else {
            $message = "Some issues were encountered";
        }
    }
}





selection(); //Pulls in page and page selection procedures



?>

<table id="structure">
    <tr>
        <td id="navigation">
            <ul class="pages">
                <?php
                echo navigation();
                ?>
            </ul>
        </td>
        <td id="page">
            <h2>Edit page: <?php echo $selected_page["menu_name"]; ?></h2>
            <?php
            if (isset($message) && !empty($message)) {
                echo "<p style='font-size: larger'>{$message}</p>";
            }
            ?>
            <form action="edit_page.php?page=<?php echo urlencode($selected_page["id"]); ?>" method="post">

                <p>Page name: <input type="text" name="menu_name" id="menu_name" value="<?php echo $selected_page["menu_name"]; ?>"></p>

                <p>Position:
                    <select name="position">
                        <?php
                        $count = 0;
                        retrieve($connection,"pages", "*", [],
                            function($v, $k, $r) use (&$count, $selected_page) {
                                $count++;
                                if ($count == $selected_page["position"]) {
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

                <p>Subject id
                <select name="subject_id">
                    <?php
                        $count = 0;
                        retrieve($connection,"subjects", "*", [],
                            function($v, $k, $r) use (&$count, $selected_page) {
                                $count++;
                                if ($count == $selected_page["subject_id"]) {
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
                    <input type="radio" <?php if ($selected_page["visible"] == 0) {echo " checked ";} ?> name="visible" value="0"> No&nbsp;
                    <input type="radio" <?php if ($selected_page["visible"] == 1) {echo " checked ";} ?> name="visible" value="1"> Yes
                </p>

                <p>
                    <textarea name="content"><?php echo $selected_page["content"]; ?></textarea>
                </p>

                <input type="submit" value="Update Page" name="submit">

                &nbsp; &nbsp;
                <a href="delete_page.php?page=<?php echo urlencode($selected_page['id']) ?>"
                   onclick=" return confirm('Are you sure you want to delete <?php echo $selected_page["menu_name"] ?>');">
                    Delete page</a>

            </form>
            <br />
            <a href="content.php">Cancel</a>
        </td>
    </tr>
</table>
<?php require "./includes/footer.php"; ?>

