
<?php
include_once "./includes/connection.php";
require_once "./includes/functions.php";
require_once "./includes/operatons.php";
include_once "./includes/header.php";

if (isset($_GET["subj"])) {
    $selected_subject = get_selected_id("subjects", $_GET["subj"]);
    $selected_page = null;
} else if (isset($_GET["page"])) {
    $selected_page = get_selected_id("pages", $_GET["page"]);
    $selected_subj = null;
} else {
    $selected_page = null;
    $selected_subj = null;
}
?>

    <table id="structure">
      <tr>
      <td id="navigation">
          <ul class="subjects">
      <?php
      $subjects = retrieve("subjects", "*", " ORDER BY POSITION ASC", $connection);

      for_each( $subjects , function ($subject) {
          global $selected_subject;
          $sub = urldecode($subject["id"]);
          echo "<li ";
          if ($subject["id"] == $selected_subject["id"]) { echo "class='selected'"; }
          echo "> <a href='content.php?subj=$sub'> {$subject['menu_name']}</a></li>";
          global $connection;
          echo "<ul class='pages'>";
          $pages = retrieve("pages", " * ", "WHERE subject_id = {$subject['id']} ORDER BY POSITION ASC", $connection);

          for_each($pages, function ($page) {
              global $selected_page;
              $pg = urldecode($page["id"]);
              echo "<li ";
              if ($page["id"] == $selected_page["id"]) { echo "class='selected'"; }
              echo "> <a href='content.php?page=$pg'> {$page['menu_name']}</a></li>";
          });
          echo "</ul>";
      });

      function get_selected_id($table, $row_id) {
          global $connection;
          if ($row_id) {
              $clauses = "WHERE id = $row_id";
              $row = retrieve($table, "*", "$clauses", $connection, function($r) {
                  return $r;
              });
              return $row;
          }
      }
      ?>
          </ul>
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

  