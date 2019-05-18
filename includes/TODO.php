<?php




//    retrieve('subjects', 'id, date, email, response', $con);
function retrieve($table, $fields, $clauses, $PDO_connection, $fn){
    static $result;
    if (!isset($fn)) {
        $fn = function (){};
    }
 if (!isset($clauses)) {
        $clauses = "";
    }

    try {
        $stmt = $PDO_connection->prepare("SELECT $fields FROM $table $clauses");
        if($stmt->execute()){
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = new RecursiveArrayIterator($stmt->fetchAll());

            foreach($result as $k => $v) {
                $fn( $v );
            }
        }

    }
    catch(PDOException $error) {
        echo "Database query faild: " . $error->getMessage();
    }

    return $result;
}

