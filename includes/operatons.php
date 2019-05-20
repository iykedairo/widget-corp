<?php




//    retrieve('subjects', 'id, date, email, response', $con);
function retrieve($table, $fields, $clauses, $PDO_connection, $fn = null){
    static $result;
    if (!isset($fn)) {
        $fn = function (){};
    }
 if (!isset($clauses)) {
        $clauses = "";
    }

    try {
//        echo "<h2>SELECT $fields FROM $table $clauses</h2>";
        $stmt = $PDO_connection->prepare("SELECT $fields FROM $table $clauses");
        if($stmt->execute()){
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = new RecursiveArrayIterator($stmt->fetchAll());

            foreach($result as $k => $v) {
                if ( ($current = $fn( $v, $k, $result ) )) {
                    return $current;
                }
            }
        }

    }
    catch(PDOException $error) {
        echo "Database query faild: " . $error->getMessage();
    }

    return $result;
}

function for_each($list, $fn) {
    foreach($list as $k => $v) {
        if ($fn( $v, $k, $list )) {
            break;
        }
    }
}


function map($list, $fn) {
    $new_list = [];
    foreach($list as $k => $v) {
        array_push($new_list, $fn( $v, $k, $list ));
    }
    return $new_list;
}



function get_all_subjects() {}
function get_subject_pages() {}