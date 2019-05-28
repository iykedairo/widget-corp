<?php
$obj = [
    "good" => "9",
    "bad" => 4.5,
    "average" => 3.0,
    "yes" => "Blessed",
    "Oregun" => "Ghost",
    "norm" => 1
];





echo mapper($obj)->generate_query();









/*
 * function update_table() {}
 * function store() {}
 * function retrieve() {}
 *
 *
 *
 $arr = [1,2,3];
$in  = str_repeat('?,', count($arr) - 1) . '?';
$sql = "SELECT * FROM table WHERE column IN ($in)";
$stm = $db->prepare($sql);
$stm->execute($arr);
$data = $stm->fetchAll();

$arr = [1,2,3];
$in  = str_repeat('?,', count($arr) - 1) . '?';
$sql = "SELECT * FROM table WHERE foo=? AND column IN ($in) AND bar=? AND baz=?";
$stm = $db->prepare($sql);
$params = array_merge([$foo], $arr, [$bar, $baz]);
$stm->execute($params);
$data = $stm->fetchAll();



$data = ['name' => 'foo', 'submit' => 'submit']; // data for insert
$allowed = ["name", "surname", "email"]; // allowed fields
$values = [];
$set = "";
foreach ($allowed as $field) {
    if (isset($data[$field])) {
        $set .="`".str_replace("`", "``", $field)."`". "=:$field, ";
        $values[$field] = $data[$field];
    }
}
$set = substr($set, 0, -2);
$stmt = $pdo->prepare("INSERT INTO users SET $set");
$stmt->execute($values);



try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO users (name) VALUES (?)");
    foreach (['Joe','Ben'] as $name)
    {
        $stmt->execute([$name]);
    }
    $pdo->commit();
}catch (Exception $e){
    $pdo->rollback();
    throw $e;
}
#######################################################################################################

                NAMED PLACEHOLDERS USAGE :

######################################################################################################

~~~~~~~~~~~~~~~~~~SELECT COMMAND NAMED PLACEHOLDERS~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email AND status=:status');
$stmt->execute(['email' => $email, 'status' => $status]);
$user = $stmt->fetch();
_____________________________________________________________________________________________________

$data = ["email" => "me@iy.ke", "status" => "1"];
$mapped = mapper($data);
$clauses = $mapped->generate_query(" AND ", "keys");
$stmt = $pdo->prepare("SELECT * FROM $table WHERE {$clauses}");
$stmt->execute($data);
$result = $stmt->fetch();

_____________________________________________________________________________________________________
~~~~~~~~~~~~~~~~~~INSERT COMMAND NAMED PLACEHOLDERS~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$data = [
    'name' => $name,
    'surname' => $surname,
    'sex' => $sex,
];
$sql = "INSERT INTO users (name, surname, sex) VALUES (:name, :surname, :sex)";
$stmt= $pdo->prepare($sql);
$stmt->execute($data);

_______________________________________________________________________________________
function store() {
    $_data = mapper($data);
    $_column_names = join(",", $_data->keys);
    $_values = join(",", $_data->pad_keys(" :", "start")->keys);
    $sql = "INSERT INTO $table ($_column_names) VALUES ($_values)";
    $statement = $link->prepare($sql);
    $statement->execute($data);
}
______________________________________________________________________________________




~~~~~~~~~~~~~~~~~~~~~~~~~UPDATE COMMAND NAMED PLACEHOLDER~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//$where_clause is an associative array that we will treat the same way as before but join with AND
$data = [
    'name' => $name,
    'surname' => $surname,
    'sex' => $sex,
    'id' => $id,
];
$sql = "UPDATE users SET name=:name, surname=:surname, sex=:sex WHERE id=:id";
$stmt= $dpo->prepare($sql);
$stmt->execute($data);


__________________________________________________________________________________________________

$_data = mapper($data); //Remember $where_clause data is still among
$padded = $_data->pad_keys("=:", "start")->remove_item("where_clause");
$key_val_set = $paded->generate_query(); //name=:name, surname=:surname, sex=:sex
$_clauses = mapper($_data->list["clauses"])->generate_query(" AND ");
$sql = "UPDATE users SET {$key_val_set} WHERE {$_clauses}";
$dpo->prepare($sql)->execute($data);

function patch($connection, $table, $fields, $clauses) {
    $_data = mapper($data); //Remember $where_clause data is still among
    $padded = $_data->pad_keys("=:", "start")->remove_item("where_clause");
    $key_val_set = $paded->generate_query(); //name=:name, surname=:surname, sex=:sex
    $_clauses = mapper($_data->list["clauses"])->generate_query(" AND ");
    $sql = "UPDATE users SET {$key_val_set} WHERE {$_clauses}";
    $dpo->prepare($sql)->execute($data);
}
__________________________________________________________________________________________________


#############################################################################################

                POSITIONAL PLACEHOLDERS USAGE ?

##############################################################################################

~~~~~~~~~~~~~~~~~~INSERT COMMAND POSITIONAL PLACEHOLDERS~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$statement = $link->prepare('INSERT INTO testtable (name, lastname, age) VALUES (?, ?, ?)');
$statement->execute(['Bob', 'Desaunois', '18']);
_________________________________________________________________________________________________
$data = ["name" => 'Bob', "lastname" => 'Desaunois', "age" => '18'];
$mapped = mapper($data);
$_keys = join(",", $mapped->keys); //String
$values = $mapped->values; //Array
$_q_marks = str_repeat('?,', $mapped->length - 1) . '?';
$Q = $stmt->prepare("INSERT INTO $table ($_keys) VALUES ($_q_marks)");
$Q->execute($values);
_________________________________________________________________________________________________


~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~SELECT COMMAND POSITIONAL PLACEHOLDERS~~~~~~~~~~~~~~~~~~~~~~~~
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND status=?');
$stmt->execute([$email, $status]);
$user = $stmt->fetch();

_________________________________________________________________________________________________

$data = ["email" => "me@iy.ke", "status" => "1"];
$mapped = mapper($data);
$padded = $mapped->pad_keys(" = ?", "end");
$clauses = join(" AND ", $padded->keys);
$stmt = $pdo->prepare("SELECT * FROM $table WHERE {$clauses}");
$stmt->execute($mapped->values);

$result = $stmt->fetch();
_________________________________________________________________________________________________
*/





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
            function generate_query($separator = ", ", $_with = "keys") {
                if($_with === "keys") {
                    $padded_instance = $this->pad_keys(":");
                    $mapped_keys = $this->merge_keys($padded_instance->old_keys, $padded_instance->keys, "=");
                } else if ($_with === "values") {
                    $padded_instance = $this->pad_values(":");
                    $mapped_keys = $this->merge_keys($padded_instance->old_values, $padded_instance->values, "=");
                }
                $joined_mapped_keys = join($separator, $mapped_keys);
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
                        throw new Exception("Array required for mapper");
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

            function for_each($fn) {
                foreach($this->list as $key => $val) {
                    if(($result = $fn($val, $key, $this->list))) {
                        return $result;
                    }
                }
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
