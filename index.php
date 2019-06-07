
    <?php
    include_once "./includes/connection.php";
    require_once "./includes/operatons.php";
    include_once "./includes/header.php";

    selection(true); //Pulls in page and subject selection procedures
    ?>

    <table id="structure">
        <tr>
            <td id="navigation">
                <ul class="subjects">
                    <?php
                    echo navigation("index.php", true);
                    ?>
                </ul>
            </td>
            <td id="page">

                <?php

                if (!is_null($selected_subject) && isset($selected_subject["menu_name"])) {
                    echo "<h2>{$selected_subject["menu_name"]}</h2>";
                } else {
                    echo "<h2>Welcome to Widget Corp</h2>";
                }
                 if (!is_null($selected_page) && isset($selected_page["subject_id"])) {
                    $subject = get_selected_by_id("subjects", $selected_page["subject_id"], true);
                    $encodedSubjectId = urlencode($subject["id"]);
                    $encodedPageId = urlencode($selected_page["id"]);
                    $subject_name = $subject["menu_name"];
                    $page_name = $selected_page["menu_name"];
            echo "<div>
            <p>{$selected_page['content']}</p>
            </div>";
                }
                ?>
            </td>
        </tr>
    </table>
    <?php require "./includes/footer.php"; ?>