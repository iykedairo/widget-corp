
<?php
include_once "./includes/connection.php";
require_once "./includes/operatons.php";
include_once "./includes/header.php";
if (isset($_POST["submit"])) {
    if ( ($check = screen_for_empty("username, password", $_POST))) {
        die( "Please supply the following input(s) in your form " . $check );
    }
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $password = md5($password); //30 character long
    //Hashing algorithm that performs RSA Data Security Message Digest algorithm
    $password =sha1($password); //40 hexadecimal character
    $req = store($connection, "users", ["username" => $username, "hashed_password" => $password]);
    if ($req["message"] == "SUCCESS!") {
        $message = "SUCCESS! The user was successfully created!";
        //exit;
    } else {
        $message = "The user could not be created {$req['message']}" ;
    }
} else {
    $username = "";
    $password = "";
}
show($message);
?>

<table id="structure">
    <tr>
        <td id="navigation">
            <ul class="subjects">
            <a href="#">Return to public area</a>
                <?php
                // echo navigation();
                ?>
            </ul>
        </td>
        <td id="page">
            
            <form action="new_user.php" method="post">
            <legend><h2>Create New User </h2></legend>

                <p>
                    <label for="username">Username</label> <input type="text" name="username">
                </p>
                <p>
                    <label for="password">Password</label> <input type="password" name="password">
                </p>

                <p><input type="submit" value="Create user" name="submit"></p>
            </form>
            <br />
            <div>
                <p>
                
                <ul>
                 
                </ul>
                </p>
            </div>
        </td>
    </tr>
</table>
<?php require "./includes/footer.php"; ?>

