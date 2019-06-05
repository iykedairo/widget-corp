
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
                    echo navigation("index.php");
                    ?>
                </ul>
                <br />
                <a href="new_subject.php">+ Add a new subject</a>
            </td>
            <td id="page">
                <h2>Welcome to Widget Corp</h2>
                <?php
                if (!is_null($selected_subject)) {
                    echo "<h2>{$selected_subject["menu_name"]}</h2>";
                }

                else if (!is_null($selected_page)) {
                    $subject = get_selected_id("subjects", $selected_page["subject_id"]);
                    $encodedSubjectId = urlencode($subject["id"]);
                    $encodedPageId = urlencode($selected_page["id"]);
                    $subject_name = $subject["menu_name"];
                    $page_name = $selected_page["menu_name"];
                    echo "

            <div>
            <p>{$selected_page['content']}</p>
            
</div>
                
            ";

                } else {
//                    echo  "<h2>Select a page to edit</h2>";
                }
                ?>
            </td>
        </tr>
    </table>
    <?php require "./includes/footer.php"; ?>