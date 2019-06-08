<?php
include_once "./includes/session.php";
include_once "./includes/connection.php";
require_once "./includes/operatons.php";

if (logged_in()) {
    redirect_to("staff.php");
}
include_once "./includes/header.php";

    if (isset($_POST["submit"])) {
        if ( ($check = screen_for_empty("username, password", $_POST))) {
            $message = "Please supply the following input(s) in your form " . $check;
        } else {

            $username = trim($_POST["username"]);
            $password = trim($_POST["password"]);
            $password = md5($password); //30 character long
            //Hashing algorithm that performs RSA Data Security Message Digest algorithm
            $password = sha1($password); //40 hexadecimal character
            $go = false;
            $clause = ["username" => $username, "hashed_password" => "{$password}"];
            retrieve($connection, "users", ["username", "id"], $clause,
                function ($row) use(&$go) {
                    $go = true;
                    $_SESSION["user_id"] = $row["id"];
                    $_SESSION["username"] = $row["username"];
                    return true;
                }, "LIMIT 1");

        }
        if (isset($go) && $go) {
            $message = "SUCCESS! User are logged in successfuly.";
            $password = "";
            redirect_to("staff.php");
        } else {
            $mes =  "Sorry! We could not log you in at this time! Please try again." ;
            $message = $message ? $message . "<br /> " . $mes : $mes;
            $username = "";
            $password = "";
        }
    } else {
        if (isset($_GET["logout"]) && $_GET["logout"] == 1) {
            $message = "You are now logged out!";
        }
        $username = "";
        $password = "";
    }
    if (isset($message)) {
        show($message);
    }
?>

<table id="structure">
    <tr>
        <td id="navigation">
            <ul class="subjects">
            <a href="#">Return to public area</a>
            </ul>
        </td>
        <td id="page">
            
            <form action="login.php" method="post">
            <legend><h2>Staff Login </h2></legend>

                <p>
                    <label for="username">Username</label> <input type="text" name="username" maxlength="30"
                        value="<?php echo htmlentities($username); ?>">
                </p>
                <p>
                    <label for="password">Password</label> <input type="password" name="password" maxlength="30"
                      value="<?php echo htmlentities($password); ?>">
                </p>

                <p><input type="submit" value="Login" name="submit"></p>
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

