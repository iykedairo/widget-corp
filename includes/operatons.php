<?php


//    store('subjects', 'id, date, email, response', $con);
    function store($PDO_connection, $table, Array $fields, $clauses = ""){
        $returnValue = [];
        try {
            $Query = build_sql_insert($table, $fields);
            $stmt = $PDO_connection->prepare($Query);
            if ($stmt->execute()){
                $returnValue["success"] = true;
                $returnValue["message"] = "SUCCESS!";
            }
        }
        catch(PDOException $error) {
            $returnValue["success"] = false;
            $returnValue["message"] = "<h1>Record could not be created at this time</h1>" .
                "<p style='color: red;'>$Query</p>" . $error->getMessage() . "<br />";
        }
        return $returnValue;
    }

    function build_sql_insert($table, $data) {
        $key = array_keys($data);
        $val = array_values($data);
        $sql = "INSERT INTO $table (" . implode(', ', $key) . ") "
            . "VALUES ('" . implode("', '", $val) . "')";

        return($sql);
    }

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

function selection() {
    global $selected_subject;
    global  $selected_page;

    if (isset($_GET["subj"])) {
        $selected_subject = get_selected_id("subjects", $_GET["subj"]);
        $selected_page = null;
    } else if (isset($_GET["page"])) {
        $selected_page = get_selected_id("pages", $_GET["page"]);
        $selected_subject = null;
    } else {
        $selected_page = null;
        $selected_subject = null;
    }
}

function navigation() {
    global $connection;
    $output = "";

    $subjects = retrieve("subjects", "*", " ORDER BY POSITION ASC", $connection);

    for_each( $subjects , function ($subject) use (&$output) {
        global $selected_subject;
        $sub = urldecode($subject["id"]);
        $output .=  "<li ";
        if ($subject["id"] == $selected_subject["id"]) { $output .=  "class='selected'"; }
        $output .=  "> <a href='content.php?subj=$sub'> {$subject['menu_name']}</a></li>";
        global $connection;
        $output .=  "<ul class='pages'>";
        $pages = retrieve("pages", " * ", "WHERE subject_id = {$subject['id']} ORDER BY POSITION ASC", $connection);

        for_each($pages, function ($page) use (&$output) {
            global $selected_page;
            $pg = urldecode($page["id"]);
            $output .=  "<li ";
            if ($page["id"] == $selected_page["id"]) { $output .=  "class='selected'"; }
            $output .=  "> <a href='content.php?page=$pg'> {$page['menu_name']}</a></li>";
        });
        $output .=  "</ul>";
    });
    return $output;
}

function get_selected_id($table, $row_id) {
    global $connection;
    if ($row_id) {
        $clauses = "WHERE id = $row_id";
        $row = retrieve($table, "*", "$clauses", $connection, function($r) {
            return $r;
        });
        return $row;
    }
}

function get_all_subjects() {}
function get_subject_pages() {}