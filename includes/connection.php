<?php require './includes/constants.php'; ?>
<?php 
/*

if(!($connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS_KEY))) {
  die('Database connection failed '.mysql_error());
}

if(!($db_select = mysql_select_db(DB_NAME, $connection))) {
  die('Database selection failed '.mysql_error());
}
*/


modifyConstants([
    'DB_SERVER' => '127.0.0.1',
    'DB_SERVER' => 'iy.ke',
    'DB_NAME' => 'widget_corp',
    'DB_USER' => 'iyke',
    'DB_USER' => 'iyke',
    'DB_PASS_KEY' => '',
    'DB_PASS_KEY' => '#123@root.ROOT'
]);

$conParams = "<p>  <strong> Connection paramters:-</strong> <br /> server: $DB_SERVER, <br /> Database: $DB_NAME, <br /> User: $DB_USER, <br /> PMK: $DB_PASS_KEY";

    try {
        $connection = new PDO("mysql:host=$DB_SERVER;port=3306;dbname=$DB_NAME", $DB_USER, $DB_PASS_KEY);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        echo "connection has succeeded ...";
    } catch(PDOException $exception) {
        echo "<div style='width: 50%; margin: 0 auto; margin-top: 3em; color: #444;'><h1>Whoops! Database connection has failed.. </h1>
        <h2>See why below</h2>
        <p style='color:#DB2472;'><em>" . $exception->getMessage() . "</em></p>$conParams</div>";
        die();
    }

?>
