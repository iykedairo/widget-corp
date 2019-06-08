<?php
class Database {
    var $server = "sql311.unaux.com";
    var $database = "unaux_22253760_Response";
    var $username = "unaux_22253760";
    var $dataset = "DEMIKADO!";

    var $connection = null;
    var $data = [];




    function clean_sql_flags($flags) {
        if (!$flags) {
            return " ";
        }
        $temp = $flags;
        $flags = "";
        foreach (preg_split("/\s+/", $temp, 0, PREG_SPLIT_NO_EMPTY) as $flag) {
            if (!preg_match("/^[a-zA-Z0-9]+$/", $flag)) {
                throw new Exception($flag . " is not a valid MySQL flag");
            }
            $flags .= " " . $flag . " ";
        }
        return $flags;
    }

function fetch_page() {} //Just give me a page
function fetch_pages() {} //Give me all named pages
function fetch_subject() {} //Just give me a subject
function fetch_subjects() {} //Give me all named subjects




    function __construct() {
        try{
            echo "starting ... <br>";
            $this->connection = new PDO("mysql:host=$this->server;port=3306;dbname=$this->database", $this->username, $this->dataset);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            echo "connection probabbly succeeded ...";
        } catch(PDOException $exception) {
            echo "Connection failed.." . $exception->getMessage();
            die();
        }
        echo "<h1>SUCCESS!</h1>";
        //$this->display($this->getRecords("usability"));
    }

    function retrieve($table, $fields = " * "){
        try {
            $stmt = $this->connection->prepare("SELECT $fields FROM $table");
            if($stmt->execute()){
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                $result = new RecursiveArrayIterator($stmt->fetchAll());
                foreach($result as $k=>$v) {
                    $this->data[$k] = $v;
                }
                echo json_encode($this->data);
            }

        }
        catch(PDOException $error) {
            echo "Error: " . $error->getMessage();
        }
        return $this;
    }

    protected function build_sql_insert($table, $data) {
        $key = array_keys($data);
        $val = array_values($data);
        $sql = "INSERT INTO $table (" . implode(', ', $key) . ") "
            . "VALUES ('" . implode("', '", $val) . "')";

        return($sql);
    }

    function record($table, Array $fields){
        try {
            $Query = $this->build_sql_insert($table, $fields);
            $stmt = $this->connection->prepare($Query);
            if ($stmt->execute()){
                echo "<h1>New record created successfully</h1>";
            }
        }
        catch(PDOException $error) {
            echo "<h1>Record could not be created at this time</h1>" . "<p style='color: red;'>$sql</p>" . $error->getMessage() . "<br />";
        }

        return $this;
    }

    function display($data){
       return  (isset($data) ) ? json_encode($data): null;
    }
}

class TableRows extends RecursiveIteratorIterator {
    function __construct($it) {
        parent::__construct($it, self::LEAVES_ONLY);
    }

}

$db = new Database();
echo "<h1> POSTED DATA COMING ALONG";
$db->record("usability", ["email" => $_POST["email_address"], "response" => $_POST["message"]]);
echo "</h1>";
$db->retrieve("usability", " id, date, email, response ");



?>