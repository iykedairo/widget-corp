<?php


//    store('subjects', 'id, date, email, response', $con);
function delete_record($connection, $table, $clauses, $limit = 1) {
    static $Q;
    mapper($clauses)->generate_query(" AND ", "keys",
        function ($pad, $list, $generated) use ($clauses, $connection, $table, $limit, &$Q) {
            $Q = "DELETE FROM $table WHERE $generated LIMIT $limit";
        });
    echo $Q;
    $statement = $connection->prepare($Q);
    if($statement->execute($clauses)) {
        return true;
    } else {
        return false;
    }

}

/**
 * @param $PDO_connection
 * @param string $table
 * @param array $fields
 * @param array $clauses
 */
function store($PDO_connection, $table, $fields, $clauses = []) {
    $Q = "";
    $returnValue = [];
    try {
        $field_placeholders = str_repeat("?, ", count($fields) - 1) . "?";
        $Q = "INSERT INTO $table(". join(", ", array_keys($fields)) . ") VALUES( $field_placeholders ) ";
        // $Q = "INSERT INTO $table (". $field_placeholders .") VALUES( ". $field_placeholders .") ";
        if (count($clauses) > 0) {
            $clause_placeholders = build_clauses($clauses);
            $Q .= " WHERE ". $clause_placeholders; //insert with where clause is less often
        }
// echo $Q;
        $array = array_merge(array_values($fields), array_values($clauses));
        // $array = array_merge(array_keys($fields), array_values($fields), array_values($clauses));
        $statement = $PDO_connection->prepare($Q);
        $statement->execute($array);
        $returnValue["success"] = true;
        $returnValue["message"] = "SUCCESS!";
    }catch (PDOException $error) {
        $returnValue["success"] = false;
        $returnValue["message"] = "<h1>Record could not be created at this time</h1>" .
            "<p style='color: red;'>$Q</p>" . $error->getMessage() . "<br />";
    }
    return $returnValue;
}
function build_clauses($array) {
    $x = "";
    $len = count($array) - 1;
    $counter = 0;
    foreach($array as $k => $v) {
        if($counter++ < $len) {
            $x .=  "$k = ? AND  ";
        } else {
            $x .=  "$k = ?  ";
        }
    }
    return $x;
}
/**
 * @param PDO $connection.
 * @param string $table.
 * @param string $fields.
 * @param array $clauses.
 */
function patch($connection, $table, $fields, $clauses) {
    static $Q;
    static $object;
    mapper($clauses)->generate_query(" AND ", "keys",
        function($padded, $list, $clauses_map) use(&$table, &$fields, &$Q, &$object) {
            return mapper($fields)->generate_query(", ", "keys",
                function ($padded, $list1, $fields_map) use($clauses_map, &$table, &$Q, &$list, &$object) {
                    $Q = "UPDATE $table SET $fields_map WHERE $clauses_map";
                    $object = mapper($list1->list)->populate($list->list);
                    return true;
                });
        });
    // $object // $Q
    //echo $Q; //UPDATE subjects SET menu_name=:menu_name, position=:position, visible=:visible WHERE id=:id
    $statement = $connection->prepare($Q);
    if($statement->execute($object->list)) {
        return true;
    }

    // show($Q);
    // show($object);
    return false;
}



function for_each($list, $fn) {
    $countr = 0;
    foreach($list as $k => $v) {
        if ($fn( $v, $k, $list, $countr++ )) {
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

function selection($public = false) {
    global $selected_subject;
    $selected_subject = null;
    global  $selected_page;
    $selected_page = null;

    if (isset($_GET["subj"])) {
        $selected_subject = get_selected_by_id("subjects", $_GET["subj"]);
        if($public && $selected_subject && isset($selected_subject["id"])) {
            $selected_page = @get_pages_by_subject_id($selected_subject["id"], $public)[0];
        }
    } else if (isset($_GET["page"])) {
        $selected_page = get_selected_by_id("pages", $_GET["page"]);
        if ($public && $selected_page && $selected_page["subject_id"]) {
            $selected_subject = get_selected_by_id("subjects", $selected_page["subject_id"]);
        }
    }
}
function mapper($array) {
//->keys, ->values, ->pad_keys(), ->pad_values(), ->show()
    if(!class_exists("MAPPER", false)){
        class MAPPER {
            public $list = [];
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
                if (is_array($array)) {
                    if($this->is_associative()) {
                        if(!$this->is_associative($array)) {
                            throw  new Exception("The provided array is incompatible with MAPPER object list");
                        }
                    }
                    foreach ($array as $key => $value) {
                        $this->list[$key] = $value;
                    }
                } else {
                    throw  new Exception("A true array is required to polulate list");
                }
                return $this;
            }

            /**
             * @param string $separator
             * @param string $_with
             * @param callable $fn
             * @return string
             * @throws Exception
             */
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
                $countr = 0;
                foreach($this->list as $key => $val) {
                    if(($result = $fn($val, $key, $this->list, $this, $countr++))) {
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


function redirect_to($file_path = null) {
    if ($file_path) {
        header("Location: {$file_path}");
    }
    exit;
}

/**
 * @param string $inputs comma separated strings --- to look up on the provided supperglobal
 * @param $_REQUEST | $_POST | $_GET | $_COOKIE $supper
 * @var $errors_bucket error bucket
 * @return sring | false it returns error string or false
 */
function screen_for_empty($inputs, $supper) {
    $errors_bucket = "";
    for_each(explode(",", $inputs), function ($input) use (&$errors_bucket, &$supper) {
        $input = trim($input);
        if (!isset($supper[$input]) || empty($supper[$input])) {
            if (!is_numeric($supper[$input])) {
                $errors_bucket .= "<p>{$input}</p>";
            }
        }
    });
    return $errors_bucket ? $errors_bucket : false;
}

/**
 * @param PDO $PDO_connection The database connection object for the requests. Must be PDO type
 * @param string $table A table name on the database. MAke sure it is created
 * @param string $fields Default is "*". A string of comma separated values . Example "email, password, location"
 * @param array $clauses associative array map of WHERE clauses ["id" => 2, "firstname" => "iyke", "age" => 31]
 * @param null $fn A handy function to run on each and every row returned upon retrieval
 * @return RecursiveArrayIterator
 * @throws Exception
 */
function retrieve ($PDO_connection, $table, $fields = "*", $clauses = [], $fn = null, $flags = "") {
    static $result;
    static $all_lists = [];
    static $clauses_obj = [];
    if (!$fields) {
        return false;
    }
    if ($clauses && !is_associative($clauses)) {
        echo json_encode($clauses);
        throw new Exception("clauses can only be an associative array for now and only WHERE clauses are supported currently");
    } else if ($clauses) {
        $str = " WHERE ";
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
    if (!is_string($fields) && !is_array($fields)) {
        throw new Exception("Fields must be string of comma separated values or a simple array");
    } 
    if (is_array($fields)) { //It has not changed to array so it is the string - "*"
        if (is_associative($fields)) {
            throw new Exception("A simple array required but associative array provided");
        }
        $fields = join(", ", $fields);
    }
        $statement_str = "SELECT $fields FROM $table ";
//    echo $fields;
    if (!$clauses) {
        $clauses = "";
    }
    $statement_str .= $clauses . " " . clean_sql_flags($flags);
    $statement = $PDO_connection->prepare($statement_str);
//    show($statement);
    if(($results = $statement->execute(array_merge($all_lists, $clauses_obj)))) {
//        show($results);
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        $result = $statement->fetchAll();

        foreach($result as $k => $v) {
            if ( ($current = (is_callable($fn) ? $fn: function(){})( $v, $k, $result ) )) {
                return $current;
            }
        }
    }
    return $result;
}
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
function navigation($url = "content.php", $public = false) {
    global $connection;
    $clause = [];
    $output = "";

    if ($public) {
        $clause["visible"] = 1;
    }

    if ($connection == false) {
        die("There is no connections. Application will then stop execution right here!");
    }

    $subjects = retrieve($connection,"subjects", "*", $clause,
        function ($subject) use(&$output, $url, $public) {
            global $connection;
            global $selected_subject;

            $sub = urldecode($subject["id"]);
            $output .=  "<li ";
            if ($subject["id"] == $selected_subject["id"]) {
                $output .=  "class='selected'";
            }
            if ($url == "content.php") {
                $output .=  "> <a href='edit_subject.php?subj=$sub'> {$subject['menu_name']}</a></li>";
            } else {
                $output .=  "> <a href='{$url}?subj=$sub'> {$subject['menu_name']}</a></li>";
            }
            if ($url != "content.php") {
                if($subject["id"] != $selected_subject["id"]){
                    return false;
                }
            }
            $output .=  "<ul class='pages'>";
            $clause = ["subject_id" => $subject["id"]];
            if ($public) {
                $clause["visible"] = 1;
            }
            $pages = retrieve($connection,"pages", " * ", $clause,
                function ($page) use(&$output, $url) {
                    global $selected_page;
                    $pg = urldecode($page["id"]);
                    $output .=  "<li ";
                    if ($page["id"] == $selected_page["id"]) { $output .=  "class='selected'"; }
                    $output .=  "> <a href='{$url}?page=$pg'> {$page['menu_name']}</a></li>";
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
    echo "<h1></h1>";
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
    echo "<h1></h1>";
}


function get_selected_by_id($table, $row_id) {
    global $connection;
    if ($row_id) {
        $clauses = "WHERE id = $row_id";
        $row = retrieve(  $connection, $table, "*", ["id" => $row_id], function($r) {
            return $r;
        });
        return $row;
    }
}

function get_pages_by_subject_id($subject_id, $public = false) {
    global $connection;
    if (!$subject_id) {
        return null;
    }
    $records = retrieve($connection, "pages", "*", ["subject_id" => $subject_id]);
    if ($public) {
        $recs = $records;
        $records = [];
        foreach($recs as $record) {
            if ($record["visible"] == 1) {
                $records[] = $record;
            }
        }
    }
    return $records;
}


function get_all_subjects() {}
function get_subject_pages() {}
