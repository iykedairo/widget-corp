
<?php
include_once './includes/connection.php';
require_once './includes/functions.php';
require_once './includes/TODO.php';
include_once './includes/header.php';
?>

    <table id="structure">
      <tr>
      <td id="navigation">
      <?php
//        if(!($result = mysql_query("Select * from subjects", $connection))) {
      retrieve('subjects', '*', $connection, function ($value) {
          echo '<p>';
//          var_dump($value);
           echo  $value['menu_name'] . ' ' . $value['POSITION'];
          echo '</p>';
      });

      ?>
      </td>
      <td id="page">
      <h2>Content area</h2>
      </td>
      </tr>
    </table>
   <?php require './includes/footer.php'; ?>

  