<?php

class User {

    function __construct($id, $fname, $lname, $email) {
        $this->id = $id;
        $this->fname = $fname;
        $this->lname = $lname;
        $this->email = $email;
    }

    private function courses($table) {
        $mysql = getDB();
        $stmt = $mysql->prepare("SELECT `class` FROM " . $table . " WHERE `user` = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();

        $class = NULL;
        $stmt->bind_result($class);

        $classes = array();
        while($stmt->fetch()) {
            array_push($classes, getCourse($class));
        }
        return $classes;
    }

    function enrolled() {
        return $this->courses('enrollment');
    }

    function hosting() {
        return $this->courses('hosting');
    }

}

class Course {

    function __construct($id, $name, $owner, $start, $end) {
        $this->id = $id;
        $this->name = $name;
        $this->owner = $owner;
        $this->start = $start;
        $this->end = $end;
    }

    function enrolled() {
        $mysql = getDB();
        $stmt = $mysql->prepare("SELECT `user` FROM `enrollment` WHERE `class` = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();

        $user = NULL;
        $stmt->bind_result($user);

        $users = array();
        while($stmt->fetch()) {
            array_push($users, getUserByID($user));
        }
        return $users;
    }

}

function getDB() {
    $mysql = new mysqli('127.0.0.1', 'root', 'codium7a', 'codium');
    if($mysql->connect_error) {
        return null;
    }
    return $mysql;
}

function getUser($email, $pass) {
    $mysql = getDB();

    $stmt = $mysql->prepare("SELECT `id`,`fname`,`lname` FROM `users` WHERE `email` = LOWER(?) AND `pass` = AES_ENCRYPT(LOWER(?), 'codiumisbest')");
    $stmt->bind_param("ss", $email, $pass);
    $stmt->execute();

    $id = NULL;
    $fname = NULL;
    $lname = NULL;
    $stmt->bind_result($id, $fname, $lname);
    if(!$stmt->fetch()) {
        $stmt->close();
        return null;
    }
    $stmt->close();
    return new User($id, $fname, $lname, $email);
}

function getUserByID($id) {
    $mysql = getDB();

    $stmt = $mysql->prepare("SELECT `fname`,`lname`,`email` FROM `users` WHERE `id` = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $fname = NULL;
    $lname = NULL;
    $email = NULL;
    $stmt->bind_result($fname, $lname, $email);
    if(!$stmt->fetch()) {
        $stmt->close();
        return null;
    }
    $stmt->close();
    return new User($id, $fname, $lname, $email);
}

function getCourse($id) {
    $mysql = getDB();

    $stmt = $mysql->prepare("SELECT `name`,`owner`,`start`,`end` FROM `classes` WHERE `id` = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $name = NULL;
    $owner = NULL;
    $start = NULL;
    $end = NULL;
    $stmt->bind_result($name, $owner, $start, $end);
    if(!$stmt->fetch()) {
        $stmt->close();
        return null;
    }
    $stmt->close();
    return new Course($id, $name, $owner, $start, $end);
}

?>