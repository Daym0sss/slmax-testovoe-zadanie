<?php

    require 'Person.php';
    require "PersonList.php";

    $personList = new PersonList(2);
    $personList->deletePeopleFromDB();
    /*
     * Testing functionality of Person class
     * $person2 = new Person(4);
    $transformedPerson2 = $person2->transformPerson("birthday", "sex");
    var_dump($transformedPerson2);

    $person->deletePerson(4);*/