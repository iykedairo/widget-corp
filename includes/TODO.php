<?php




//    retrieve('subjects', 'id, date, email, response', $con);
function retrieve($table, $fields, $PDO_connection, $fn){
    if (!isset($fn)) {
        $fn = function (){};
    }
    try {
        $stmt = $PDO_connection->prepare("SELECT $fields FROM $table");
        if($stmt->execute()){
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = new RecursiveArrayIterator($stmt->fetchAll());
            foreach($result as $k => $v) {
                $fn( $v );
            }
        }

    }
    catch(PDOException $error) {
        echo "Error: " . $error->getMessage();
    }

    return $result;
}

