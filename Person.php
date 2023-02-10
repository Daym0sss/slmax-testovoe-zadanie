<?php

const HOST = 'localhost';
const USER = 'root';
const PASSWORD = 'mynewpassword';
const DBNAME = 'local';

class Person
{
    private $id;
    private $name;
    private $surname;
    private $birthday;
    private $sex;
    private $birth_city;

    public function __construct()
    {
        $args = func_get_args();
        $args_num = func_num_args();
        switch ($args_num)
        {
            case 1:
                $this->findPerson($args[0]);
                break;
            case 5:
                $this->storePerson($args);
                break;
            default:
                echo "<b>Incorrect input pass id or name, surname, birthday, sex and city</b><br>";
                break;
        }
    }

    protected function storePerson($args)
    {
        if ($this->validateString($args[0]) && $this->validateString($args[1]) && $this->validateBirthday($args[2]) && $this->validateSex($args[3]) && $this->validateString($args[4]))
        {
            $this->name = $args[0];
            $this->surname = $args[1];
            $this->birthday = $args[2];
            $this->sex = $args[3];
            $this->birth_city = $args[4];

            $connect = new mysqli(HOST, USER, PASSWORD, DBNAME);
            $sql = "INSERT INTO person(name, surname, birthday, sex, city) VALUES(\"$this->name\", \"$this->surname\", \"$this->birthday\", $this->sex, \"$this->birth_city\")";
            $connect->query($sql);

            $sql = "SELECT * FROM person";
            $result = $connect->query($sql);
            foreach ($result as $row)
            {
                $this->id = $row['id'];
            }

            echo "User created : ";
            var_dump($this);

            $connect->close();
        }
        else
        {
            echo "<b>Incorrect input you must pass alphabetic name of type string, alphabetic surname of type string, birthday of format YYYY-MM-DD string, sex as 0 or 1 value, city name as string</b><br>";
        }

    }

    public function deletePerson($id)
    {
        if ($this->validateId($id))
        {
            $connect = new mysqli(HOST, USER, PASSWORD, DBNAME);
            $sql= "SELECT * FROM person WHERE id=$id";
            $result = $connect->query($sql);
            $deleted = false;
            foreach ($result as $row)
            {
                $sql = "DELETE FROM person WHERE id=$id";
                $connect->query($sql);
                echo "User with id $id deleted<br>";
                $deleted = true;
            }
            if (!$deleted)
            {
                echo "No user can be deleted with id $id<br>";
            }
            $connect->close();
        }
        else
        {
            echo "<b>Incorrect input you must pass id value as int and this id must exist</b><br>";
        }

    }

    protected static function convertSex($number)
    {
        $sex = "";
        if ($number)
        {
            $sex = "female";
        }
        else
        {
            $sex = "male";
        }

        return $sex;
    }

    protected static function convertBirthday($birthday)
    {
        $today = strtotime(date("Y-m-d"));
        $birthday = strtotime($birthday);

        return (int)(($today - $birthday) / 31536000);
    }

    public function transformPerson()
    {
        $args_num = func_num_args();
        $args = func_get_args();
        switch ($args_num)
        {
            case 1:
            {
                if ($args[0] == "sex" && isset($this->sex))
                {
                    $obj = new stdClass();
                    $obj->id = $this->id;
                    $obj->name = $this->name;
                    $obj->surname= $this->surname;
                    $obj->birthday = $this->birthday;
                    $obj->sex = Person::convertSex($this->sex);
                    $obj->city = $this->birth_city;

                    return $obj;
                }
                else if ($args[0] == "birthday" && isset($this->birthday))
                {
                    $obj = new stdClass();
                    $obj->id = $this->id;
                    $obj->name = $this->name;
                    $obj->surname= $this->surname;
                    $obj->birthday = Person::convertBirthday($this->birthday);
                    $obj->sex = $this->sex;
                    $obj->city = $this->birth_city;

                    return $obj;
                }
                else
                {
                    echo "<b>Incorrect input you must pass string 'sex' or 'birthday' or two strings 'sex' and  'birthday' and fields must be filled</b><br>";

                    return null;
                }
            }
            case 2:
            {
                if ((($args[0] == "sex" && $args[1] == "birthday") || ($args[1] == "sex" && $args[0] == "birthday")) && isset($this->birthday) && isset($this->sex))
                {
                    $obj = new stdClass();
                    $obj->id = $this->id;
                    $obj->name = $this->name;
                    $obj->surname= $this->surname;
                    $obj->birthday = Person::convertBirthday($this->birthday);
                    $obj->sex = Person::convertSex($this->sex);
                    $obj->city = $this->birth_city;

                    return $obj;
                }
                else
                {
                    echo "<b>Incorrect input you must pass string 'sex' or 'birthday' or two strings 'sex' and  'birthday' and fields must be filled</b><br>";
                    return null;
                }
            }
            default:
                echo "<b>Incorrect input you must pass string 'sex' or 'birthday' or two strings 'sex' and  'birthday' and fields must be filled</b><br>";
                return null;
        }
    }

    protected function findPerson($id)
    {
        if ($this->validateId($id))
        {
            $connection = new mysqli(HOST, USER, PASSWORD, DBNAME);
            $sql = "SELECT * FROM person WHERE id=$id";
            $result = $connection->query($sql);
            $found = false;
            foreach ($result as $row)
            {
                $this->id = $id;
                $this->name = $row['name'];
                $this->surname = $row['surname'];
                $this->birthday = $row['birthday'];
                $this->sex = $row['sex'];
                $this->birth_city = $row['city'];

                $found =true;
                echo "User found: ";
                var_dump($this);
                break;
            }
            if (!$found)
            {
                echo "No user found with such id<br>";
            }
            $connection->close();
        }
        else
        {
            echo "<b>Incorrect input: you must pass id of type int value to the constructor</b><br>";
        }
    }

    protected function validateId($id)
    {
        if (is_numeric($id))
        {
            return true;
        }
        else return false;
    }

    protected function validateString($str)
    {
        if (preg_match("/^[a-zA-Z]+$/", $str))
        {
            return true;
        }
        else return false;
    }

    protected function validateBirthday($date)
    {
        if(DateTime::createFromFormat('Y-m-d', $date)->format('Y-m-d') == $date)
        {
            return true;
        }
        else return false;
    }

    protected function validateSex($num)
    {
        if ($num == 1 || $num == 0)
        {
            return true;
        }
        else return false;
    }

    public function __get($propertyName)
    {
        return $this->$propertyName;
    }
}