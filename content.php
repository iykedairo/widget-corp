
<?php
include_once "./includes/connection.php";
require_once "./includes/functions.php";
require_once "./includes/operatons.php";
include_once "./includes/header.php";
?>

    <table id="structure">
      <tr>
      <td id="navigation">
          <ul class="subjects">
      <?php
      $subjects = retrieve("subjects", "*", " ORDER BY POSITION ASC", $connection);

      for_each( $subjects , function ($subject) {
          echo "<li>{$subject['menu_name']}</li>";
          global $connection;
          echo "<ul class='pages'>";
          $pages = retrieve("pages", " * ", "WHERE subject_id = {$subject['id']} ORDER BY POSITION ASC", $connection);

          for_each($pages, function ($page) {
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

  