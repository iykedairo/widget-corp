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
    
function patch_p($PDO_connection, $table, $fields, $clauses = []) {
    $_data = mapper($fields); //Remember $where_clause data is still among
//    $padded = $_data->pad_keys("=:", "start")->remove_item("clauses");
    $padded = $_data->pad_keys(" ", "start")->remove_item("clauses"); //keys will be padded internally on generating
    $key_val_set = $padded->generate_query(", ", "keys"); //name=:name, surname=:surname, sex=:sex
    $_clauses = mapper($_data->list["clauses"])->generate_query(" AND ", "keys");
    $Query = "UPDATE $table SET {$key_val_set} WHERE {$_clauses}";
    $PDO_connection->prepare($Query)->execute($fields);
    return true;
}
    function build_sql_insert($table, $data) {
        $key = array_keys($data);
        $val = array_values($data);
        $sql = "INSERT INTO $table (" . implode(', ', $key) . ") "
            . "VALUES ('" . implode("', '", $val) . "')";

        return($sql);
    }

//    retrieve('subjects', 'id, date, email, response', $con);


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
function mapper($array) {
//->keys, ->values, ->pad_keys(), ->pad_values(), ->show()
    if(!class_exists("MAPPER", false)){
        class MAPPER {
            protected $list = [];
            function __construct() {}
            public function __get($value) {
                static $ret;
                switch($value) {
                    case "length": $ret = count($this->list);
                        break;
                    case "keys": $ret = array_keys($this->list);
                        break;
                    case "values": $ret = array_values($this->list);
                        break;
                    case "names": $ret = array_values($this->list);
                        break;
                    default: $ret = null;
                }
                return $ret;
            }
            function replace_list($list) {
                if (is_array($list)) {
                    $this->list = $list;
                } else {
                    throw  new Exception("An array needed to replace list");
                }
                return $this;
            }
            function populate($array) {
                foreach ($array as $key => $value) {
                   $this->list[$key] = $value;
                }
                return $this;
            }
            function generate_query($separator = ", ", $_with = "keys", $fn = null) {
                if (!is_callable($fn)) {
                    $fn = function() {};
                }
//                Returns joined mapped string e.g  key=:key AND key2=:key2
                if($_with === "keys") {
                    $padded_instance = $this->pad_keys(":");
                    $mapped_keys = $this->merge_keys($padded_instance->old_keys, $padded_instance->keys, "=");
                } else if ($_with === "values") {
                    $padded_instance = $this->pad_values(":");
                    $mapped_keys = $this->merge_keys($padded_instance->old_values, $padded_instance->values, "=");
                }
                $joined_mapped_keys = join($separator, $mapped_keys);
                $fn($padded_instance, $this, $joined_mapped_keys);
                return $joined_mapped_keys;
            }

            function contains($key) {
                $ret = false;
                // This will work regardless of associative of not
                $this->for_each(function($value, $_key, $_whole) use($key, &$ret) {
                    if($this->is_associative()) {
                        if($_key === $key) {
                            return ($ret = true);
                        }
                    } else {
                        if($key === $value) {
                            return ($ret = true);
                        }
                    }
                });
                return $ret;
            }

            function remove_item($key) {
                if($this->contains($key)) {
                    unset($this->list[$key]);
                }
                return $this;
            }
            function mapper($array) {
                if (is_array($array)) {
                    $this->list = $array;
                } else {
                    if ($array != null) {
                        throw new Exception("A true array is required to create mapper");
                    }
                }
                return $this;
            }
            function deepClone($object) {
                return unserialize(serialize($object));
            }

            function pad_values($_chars, $position = "start") {
                $temp_instance = $this->deepClone($this);
                $temp_instance->old_keys = $temp_instance->keys;
                $temp_instance->old_values = $temp_instance->values;
                $temp_instance->for_each(function($value, $key, $dump_) use($position, $_chars, &$temp_instance) {
                    if(is_string($value) || is_numeric($value)) {
                        if($position === "start") {
                            $temp_instance->list[$key] = $_chars . $value;
                        } else {
                            $temp_instance->list[$key] = $value . $_chars;
                        }
                    }
                });
                return $temp_instance;
            }

            function pad_keys($_chars, $position = "start") {
                $temp = $this->deepClone($this);
                $temp->old_keys = $temp->keys;
                $temp->old_values = $temp->values;
                $temp->list = [];
                $this->for_each(function($value, $key, $dump_) use($position, $_chars, &$temp) {
                    if(is_string($key) || is_numeric($key)) {
                        if($position === "start") {
                            $temp->list[ $_chars . $key] = $value;
                        } else {
                            $temp->list[$key . $_chars] = $value;
                        }
                    }
                });
                return $temp;
            }
            /**
             * A summary informing the user what the associated element does.
             *
             * A *description*, that can span multiple lines, to go _in-depth_ into the details of this element
             * and to provide some background information or textual references.
             *
             * @param string $myArgument With a *description* of this argument, these may also
             *    span multiple lines.
             *
             * @return void
             */
            function for_each($fn) {
                foreach($this->list as $key => $val) {
                    if(($result = $fn($val, $key, $this->list, $this))) {
                        return $result;
                    }
                }
                return $this;
            }
            function is_associative($arr = null) {
                if ($arr == null) {
                    $arr = $this->list;
                }
                if(!is_array($arr)) {
                    throw new Exception($arr . " is not an array");
                }
                if (array() === $arr) {
                    return false;
                }
                return array_keys($arr) !== range(0, count($arr) - 1);
            }

            function push($item, $key = null) {
                if($key == null) { //->push("carousel")
                    if($this->is_associative()) { //Throw an error is it is an associative array
                        throw new Exception("Invalid format provided");
                        die();
                    }
                    array_push($this->list, $item); //Added successfully
                } else if(is_string($key) || is_int($key)) { //->push("both", "first")->push(7,0)
                    if($this->is_associative()) {
                        $this->list[$key] = $item; //Added successfully
                    } else {
                        throw new Exception("Invalid format provided");
                        die();
                    }
                }
                return $this;
            }

            function merge_keys($arr1, $arr2, $delimiter = " ") {
                if (count($arr1) !== count($arr2)) {
                    print_r($this);
                    throw new Exception("The two arrays are not compatible for merging");
                }
                $iterator = 0;
                $piece1 = $piece2 = $piece = null;
                $flat = [];
                $length = count($arr1); // Since they both have same length, we just count one of them
                for (; $iterator < $length; $iterator++) {
                    $piece1 = $arr1[$iterator];
                    $piece2 = $arr2[$iterator];
                    $piece = $piece1 . $delimiter . $piece2;
                    array_push($flat, $piece);
                }
                return $flat;
            }


            function show($stuff = null) {
                if (!$stuff) {
                    $stuff = $this->list;
                }
                if (is_string($stuff)) {
                    echo $stuff;
                } else if (is_iterable($stuff) || is_object($stuff) || is_array($stuff)) {
                    print_r($stuff);
                }
                return  $this;
            }
        }
    }



    $map = new MAPPER();
    return $map->mapper($array);
}
    
function retrieve ($PDO_connection, $table, $fields = "*", $clauses = [], $fn = null) {
    static $result;
    static $statement_str;
    static $fields_list = [];
    static $all_lists = [];
    static $flat = false;
    static $clauses_obj = [];
    $fields_padding = "keys";
    if ($clauses && !is_associative($clauses)) {
        echo json_encode($clauses);
        throw new Exception("clauses can only be an associative array for now and only WHERE clauses are supported currently");
    } else if ($clauses) {
        $str = "WHERE ";
        $it = 0;
        mapper($clauses)->for_each(
            function ($value, $key, $all, $_this) use(&$clauses, &$str, &$it, &$clauses_obj) {
                if ($it++ < (count($all) - 1)) {
                    $str .= "$key = ? AND ";
                } else {
                    $str .= "$key = ? ";
                }
                $clauses_obj = $_this->values;
            });
        $clauses = $str;
    }
    if (is_string($fields) || is_array($fields)) {
        if (is_string($fields)) {
            if (trim($fields) != "*") {
// turn  "menu_name, id, position" to ["menu_name", "id", "position"]
                $fields = explode(",", $fields);
                $fields_list = $fields;
                $flat = true;
            }
        } else if (!is_associative($fields)) {
            $fields_list = $fields;
            $flat = true; //No need to create. Just toggle and use
//  ["menu_name", "id", "position"] just create ?, ?, ? and use values
        }
    } else {
        throw new Exception("Fields must be string or array");
    }
    if (is_string($fields)) { //It has not changed to array so it is the string - "*"
        $statement_str = "SELECT * FROM $table ";
    } else { //It is an array
        if ($flat) {
            $question_marks = str_repeat("?, ", count($fields) - 1)." ? ";
            $fields = $question_marks;
        } else { //no one will ever come here
            mapper($fields)->generate_query(",", "keys",
                function($modified, $original, $generated) use(&$fields, &$fields_list) {
                    $fields = $generated; //Value of fields has changed here to string
                    $fields_list = $original;
//                $fields_list->populate($clauses_map->list);
                });
        }
        $all_lists = $flat ? array_values($fields_list) : $fields_list->list;
        $statement_str = "SELECT ". $fields ." FROM " . $table;
//        $statement_str = "SELECT ?, ? FROM subjects";
    }
//    echo $fields;
    if (!$clauses) {
        $clauses = "";
    }
    $statement = $PDO_connection->prepare($statement_str . $clauses);
    if($statement->execute(array_merge($all_lists, $clauses_obj))) {
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $result = new RecursiveArrayIterator($statement->fetchAll());

        foreach($result as $k => $v) {
            if ( ($current = (is_callable($fn) ? $fn: function(){})( $v, $k, $result ) )) {
                return $current;
            }
        }
    }
    return $result;
    }
function navigation() {
    global $connection;
    $output = "";
    if ($connection == false) {
        echo "<h1>There is no connections. We recline! </h1>";
        die();
    }
    $subjects = retrieve($connection,"subjects", "*", [],
        function ($subject) use(&$connection, &$output, &$subjects) {
            global $connection;
            global $selected_subject;
            $sub = urldecode($subject["id"]);
            $output .=  "<li ";
            if ($subject["id"] == $selected_subject["id"]) {
                $output .=  "class='selected'";
            }
            $output .=  "> <a href='content.php?subj=$sub'> {$subject['menu_name']}</a></li>";
            $output .=  "<ul class='pages'>";

            $pages = retrieve($connection,"pages", " * ", ["subject_id" => $subject["id"]],
                function ($page) use(&$output) {
                    global $selected_page;
                    $pg = urldecode($page["id"]);
                    $output .=  "<li ";
                    if ($page["id"] == $selected_page["id"]) { $output .=  "class='selected'"; }
                    $output .=  "> <a href='content.php?page=$pg'> {$page['menu_name']}</a></li>";
                }, " ORDER BY position ASC");
            $output .=  "</ul>";
        }, " ORDER BY position ASC" );

if (!$subjects) {
    die("No subjects returned");
}
    return $output;
}

function is_associative($arr) {
    if(!is_iterable($arr)) {
        throw new Exception( " is not an array");
    }
    if (array() === $arr) {
        return false;
    }
    return array_keys($arr) !== range(0, count($arr) - 1);
}
function show($var = "Some interesting stuff here!", $verbose = false) {
    if (is_string($var)) {
        echo $var;
    } else if ($verbose) {
        if($verbose === "json" || is_string($verbose)) {
            echo json_encode($var);
        } else {
            var_dump($var);
        }
    } else {
        print_r($var);
    }
}


function get_selected_id($table, $row_id) {
    global $connection;
    if ($row_id) {
        $clauses = "WHERE id = $row_id";
        $row = retrieve(  $connection, $table, "*", ["id" => $row_id], function($r) {
            return $r;
        });
        return $row;
    }
}
function build_sql_insert_p($table, $data) {
    $key = array_keys($data);
    $val = array_values($data);
    $sql = "INSERT INTO $table (" . implode(', ', $key) . ") "
        . "VALUES ('" . implode("', '", $val) . "')";

    /*

    $q = "INSERT INTO $table VALUES(?, ?, ?, ?)";
    $stmt = $db->prepare($q);
    $stmt->bind_params("sssd", $isbn, $author, $title, $price);
    $stmt->execute();
    echo $stmt->affected_rows . " books inserted into database.";
    $stmt->close();
    */
    $params_values = "";
    for ($iota = 1; $iota < count($key); $iota++) {
        $params_values .= $iota !== count($key) ? "?," : "?";
    }

    global $connection;
    $Q = "INSERT INTO $table VALUES ( $params_values )";
    $stmt = $connection->prepare($Q);
    $stmt->bind_params("ssss", implode(", ", $val));
    $stmt->execute();


    return($sql);
}

function get_all_subjects() {}
function get_subject_pages() {}