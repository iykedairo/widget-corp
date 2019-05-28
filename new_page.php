
<?php
include_once "./includes/connection.php";
require_once "./includes/operatons.php";
include_once "./includes/header.php";




if (intval($_GET["subj"]) == 0) { //If we didn't get a valid page id we decline further exec
    // redirect_to("content.php");
    echo "DONT'T RUN!";
}



selection(); //Pulls in page and page selection procedures
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
            <h2>Add page</h2>
            <div style="color: rgba(242,29,217, 1);">
                Kindly supply the following fields
            <?php
                if (isset($_GET["err"])) {
                    echo $_GET["err"];
                }
            ?>
            </div>
            <?php $id = $_GET['subj']; ?>
            <form action="create_page.php?subj=<?php
            echo urlencode($_GET['subj']);
            ?>" method="post">

                <p>Page name: <input type="text" name="menu_name" id="menu_name" value=""></p>

                <p>Position:
                    <select name="position">
                        <?php
                        $count = 0;
                        retrieve($connection,"pages", "*", [],
                            function($v, $k, $r) use (&$count) {
                                $count++;
                                echo "<option value='{$count}'>$count</option>";
                            }, " ORDER BY POSITION ASC");
                        $count++;
                        echo "<option value='{$count}'>$count</option>"; //Simulating next postion
                        ?>

                    </select>
                </p>

                <p>Subject id:
                    <select name="subject_id">
                        <?php
                        $count = 0;
                        $subject = null;
                        retrieve($connection,"subjects", "*", [],
                            function($v, $k, $r) use (&$count, &$subject) {
                                $count++;
                                if ($count == $_GET["subj"]) {
                                    $subject = $v;
                                        echo "<option selected value='{$count}'>$count</option>";
                                } else {
                                    echo "<option value='{$count}'>$count</option>";
                                }
                            }, " ORDER BY POSITION ASC");
                        $count++;
                        //echo "<option value='{$count}'>$count</option>"; //Simulating next postion
                        ?>

                    </select>
                </p>

                <p>Visible:
                    <input type="radio" name="visible" value="0"> No&nbsp;
                    <input type="radio" name="visible" value="1"> Yes
                </p>
                <p>
                    <textarea name="content" rows="7" cols="40"></textarea>
                </p>
                <p><input type="submit" value="Create new page for <?php echo $subject['menu_name']; ?>"></p>

            </form>
            <br />
            <a href="content.php">Cancel</a>
        </td>
    </tr>
</table>
<?php require "./includes/footer.php"; ?>

  