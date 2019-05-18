
<?php
include_once "./includes/connection.php";
require_once "./includes/functions.php";
require_once "./includes/TODO.php";
include_once "./includes/header.php";
?>

    <table id="structure">
      <tr>
      <td id="navigation">
          <ul class="subjects">
      <?php
      retrieve("subjects", "*", "", $connection, function ($subject) {
          echo "<li>{$subject['menu_name']}</li>";

          global $connection;
          echo "<ul class='pages'>";
          retrieve("pages", " * ", "WHERE subject_id = {$subject['id']}", $connection, function ($page) {
              echo "<li>{$page['menu_name']}</li>";
          });
          echo "</ul>";
      });

      ?>
          </ul>
      </td>
      <td id="page">
      <h2>Content area</h2>
      </td>
      </tr>
    </table>
   <?php require "./includes/footer.php"; ?>

  