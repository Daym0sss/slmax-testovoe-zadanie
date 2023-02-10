<?php

if (class_exists("Person"))
{
    class PersonList
    {
        private $peopleIds;

        public function __construct()
        {
            $args = func_get_args();

            $this->peopleIds = [];

            $connection = new mysqli(HOST, USER, PASSWORD, DBNAME);
            $sql = "SELECT * FROM person";
            $result = $connection->query($sql);
            $connection->close();

            //check each argument
            foreach ($args as $arg)
            {
                //for each record in DB
                foreach ($result as $row)
                {
                    //for each field in the DB
                    foreach ($row as $field)
                    {
                        if (($field == $arg || $arg > $field || $arg < $field) && !in_array($row['id'], $this->peopleIds))
                        {
                            $this->peopleIds[] = $row['id'];
                        }
                    }
                }
            }

            echo "Users' ids found: ";
            var_dump($this->peopleIds);
        }

        public function getPeopleInstances()
        {
            if (count($this->peopleIds) == 0)
            {
                echo 'The array is empty, no people were found';
            }
            else
            {
                foreach ($this->peopleIds as $id)
                {
                    $person = new Person($id);
                }
            }
        }

        public function deletePeopleFromDB()
        {
            if (count($this->peopleIds) == 0)
            {
                echo 'The array is empty, no people were found';
            }
            else
            {
                $connection = new mysqli(HOST, USER, PASSWORD, DBNAME);
                $person = new Person($this->peopleIds[0]);
                foreach ($this->peopleIds as $id)
                {
                    $person->deletePerson($id);
                }
            }
        }
    }
}
else
{
    echo "Person class does not exist or not included in your 3rd file for testing";
}
