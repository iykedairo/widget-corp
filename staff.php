<?php
include_once "./includes/session.php";
include './includes/operatons.php';
confirm_logged_in();
include './includes/header.php';
?>


    <table id="structure">
      <tr>
      <td id="navigation">&nbsp;</td>
      <td id="page">
      <h2>Staff menu</h2>
      <p>Welcome to the staff area, <?php echo $_SESSION["username"];?> </p>
      <ul>
        <li><a href="content.php">Manage website content</a></li>
        <li><a href="new_user.php">Add staff user</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
      </td>
      </tr>
    </table>
   <?php include './includes/footer.php'; ?>