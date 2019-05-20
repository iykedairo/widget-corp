
<?php
include_once "./includes/connection.php";
require_once "./includes/functions.php";
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
          <br />
          <a href="new_subject.php">+ Add a new subject</a>
      </td>
      <td id="page">
      <h2>Content area</h2>
          <?php
          if (!is_null($selected_subject)) {
              echo "<h2>{$selected_subject["menu_name"]}</h2>";
          }

          else if (!is_null($selected_page)) {
              echo "<div>{$selected_page['content']}</div>";
          } else {
              echo  "<h2>Select a page to edit</h2>";
          }
          ?>
      </td>
      </tr>
    </table>
   <?php require "./includes/footer.php"; ?>

  