
<?php
include_once "./includes/connection.php";
require_once "./includes/functions.php";
require_once "./includes/operatons.php";
include_once "./includes/header.php";

if (isset($_GET["subj"])) {
    $selected_subj = $_GET["subj"];
    $selected_page = "";
} else if (isset($_GET["page"])) {
    $selected_page = $_GET["page"];
    $selected_subj = "";
} else {
    $selected_page = "";
    $selected_subj = "";
}
?>

    <table id="structure">
      <tr>
      <td id="navigation">
          <ul class="subjects">
      <?php
      $subjects = retrieve("subjects", "*", " ORDER BY POSITION ASC", $connection);

      for_each( $subjects , function ($subject) {
          $sub = urldecode($subject["id"]);
          echo "<li> <a href='content.php?subj=$sub'> {$subject['menu_name']}</a></li>";
          global $connection;
          echo "<ul class='pages'>";
          $pages = retrieve("pages", " * ", "WHERE subject_id = {$subject['id']} ORDER BY POSITION ASC", $connection);

          for_each($pages, function ($page) {
              $pg = urldecode($page["id"]);
              echo "<li> <a href='content.php?page=$pg'> {$page['menu_name']}</a></li>";
          });
          echo "</ul>";
      });

      ?>
          </ul>
      </td>
      <td id="page">
      <h2>Content area</h2>
          <?php
          echo $selected_subj;
          echo "<br />";
          echo $selected_page;
          ?>
      </td>
      </tr>
    </table>
   <?php require "./includes/footer.php"; ?>

  