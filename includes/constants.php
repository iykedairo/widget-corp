<?php
  define('DB_SERVER', 'sql311.unaux.com');
  define('DB_USER', 'unaux_22253760');
  define('DB_PASS_KEY', 'DEMIKADO!');
  define('DB_NAME', 'unaux_22253760_widget');
//    $DB_SERVER = DB_SERVER;
    $DB_USER = DB_USER;
    $DB_PASS_KEY = DB_PASS_KEY;
    $DB_NAME = DB_NAME;

/*$constants: associative array*/
    function modifyConstants($constants) {
        foreach ($constants as $constant => $value) {
            $GLOBALS[$constant] = $value;
//            echo $GLOBALS[$constant].'<br>';
        }
    }
?>