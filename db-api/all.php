<?php

class User {

    function __construct($id, $fname, $lname, $email) {
        $this->id = $id;
        $this->fname = $fname;
        $this->lname = $lname;
        $this->email = $email;
    }

    function enrolled() {
        $mysql = getDB();
        $stmt = $mysql->prepare("SELECT `class` FROM `enrollment` WHERE `user` = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();

        $class = NULL;
        $stmt->bind_result($class);

        $classes = array();
        while($stmt->fetch()) {
            array_push($classes, getCourse($class));
        }
        $stmt->close();
        return $classes;
    }

    function hosting() {
        $mysql = getDB();
        $stmt = $mysql->prepare("SELECT `id`,`name`,`start`,`end`,`open`,`page` FROM `classes` WHERE `owner` = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();

        $id = NULL;
        $name = NULL;
        $start = NULL;
        $end = NULL;
        $open = NULL;
        $page = NULL;
        $stmt->bind_result($id, $name, $start, $end, $open, $page);

        $classes = array();
        while($stmt->fetch()) {
            array_push($classes, new Course($id, $name, $this, $start, $end, $open, $page));
        }
        $stmt->close();
        return $classes;
    }

    function hostCourse($name, $start, $end, $open) {
        $mysql = getDB();

        $stmt = $mysql->prepare("INSERT INTO `classes` VALUES(NULL, ?, ?, FROM_UNIXTIME(?), FROM_UNIXTIME(?), ?, ?)");
        $o = $open ? 1 : 0;
        $hash = bin2hex(openssl_random_pseudo_bytes(5));
        $stmt->bind_param("sissis", $name, $this->id, $start, $end, $o, $hash);
        $stmt->execute();
        $stmt->close();

        $stmt = $mysql->prepare("SELECT `id` FROM `classes` WHERE `name` = ? AND `owner` = ? AND `start` = FROM_UNIXTIME(?) AND `end` = FROM_UNIXTIME(?) AND `open` = ? AND `page` = ?");
        $stmt->bind_param("sissis", $name, $this->id, $start, $end, $o, $hash);
        $stmt->execute();
        $id = NULL;
        $stmt->bind_result($id);
        if(!$stmt->fetch()) {
            // wat
            die();
        }
        $stmt->close();

        return new Course($id, $name, $this, $start, $end, $open, $hash);
    }

    function startSession() {
        $mysql = getDB();
        $stmt = $mysql->prepare("INSERT INTO `sessions` VALUES(?, ?)");
        $hash = bin2hex(openssl_random_pseudo_bytes(23));
        $stmt->bind_param("is", $this->id, $hash);
        $stmt->execute();
        $stmt->close;
        return $hash;
    }

    function getName() {
        return $this->fname . " " . $this->lname;
    }

}

class Course {

    function __construct($id, $name, $owner, $start, $end, $open, $page) {
        $this->id = $id;
        $this->name = $name;
        $this->owner = $owner;
        date_default_timezone_set("EST");
        $this->start = strtotime($start);
        $this->end = strtotime($end);
        $this->open = $open;
        $this->page = $page;
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
        $stmt->close();
        return $users;
    }

    function enroll($user) {
        $mysql = getDB();
        $stmt = $mysql->prepare("INSERT INTO `enrollment` VALUES (?, ?)");
        $stmt->bind_param("ii", $user->id, $this->id);
        $stmt->execute();
        $stmt->close();
    }

    function invite($email) {
        $mysql = getDB();
        $stmt = $mysql->prepare("INSERT INTO `invited` VALUES(?, ?)");
        $stmt->bind_param("is", $this->id, $email);
        $stmt->execute();
        $stmt->close();

        require_once "../sendgrid/SendGrid.php";
        require_once "../sendgrid/SendGrid/Email.php";
        require_once "../sendgrid/info.php";
        $sendgrid = new SendGrid($user, $pass);
        $mail = new SendGrid\Email();
        $mail->setFrom("no-reply@codium.io");
        $mail->setFromName("Codium");
        $mail->addTo($email);
        $mail->setSubject("You have been invited to a class!");

        $url = $this->getURL();
        $urlz = "http://<z>codium</z>.io/" . $page;
        $index = strrpos($email, '@');
        $emailz = substr_replace($email, "<z></z>", $index < 0 ? 1 : $index, 0);
        $index2 = strrpos($email, '.', $index);
        $emailz = substr_replace($email, "<z></z>", $index2 < 0 ? 1 : $index2, 0);
        $msg = "";
        $msg .= '<font style="font-size: 18px">';
        $msg .= '<font style="font-weight: bold; color: #5cb85c">' . $this->owner->getName() . '</font> has invited you to their class <font style="font-weight: bold; color: #5cb85c">' . $this->name . "</font>!<br />";
        $msg .= 'Click <a href="' . $url . '" style="font-weight: bold; color: #5cb85c; text-decoration: none">here</a> to accept their invitation. Make sure you sign in or register with the email <font style="font-weight: bold; color: #5cb85c">' . $emailz . '</font>.<br />';
        $msg .= "If the above link does not work, please copy and paste the following into your browser's address bar: " . $urlz;
        $msg .= '</font>';

        $mail->setHtml($msg);
        $sendgrid->send($mail);

    }

    function isInvited($user) {
        $mysql = getDB();
        $stmt = $mysql->prepare("SELECT * FROM `invited` WHERE `class` = ? AND `email` = ?");
        $stmt->bind_param("is", $this->id, $user->email);
        $stmt->execute();
        $result = $stmt->fetch();
        $stmt->close();
        return $result;
    }

    function isEnrolled($user) {
        $mysql = getDB();
        $stmt = $mysql->prepare("SELECT * FROM `enrollment` WHERE `user` = ? AND `class` = ?");
        $stmt->bind_param("ii", $user->id, $this->id);
        $stmt->execute();
        $result = $stmt->fetch();
        $stmt->close();
        return $result;
    }

    function getFirebaseIDFor($user) {
        return "doc-" . $this->id . "-" . $user->id;
    }

    function invited() {
        $mysql = getDB();

        $stmt = $mysql->prepare("SELECT `email` FROM `invited` WHERE `class` = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();

        $email = NULL;
        $emails = array();
        $stmt->bind_result($email);
        while($stmt->fetch()) {
            array_push($emails, $email);
        }
        $stmt->close();

        return $emails;
    }

    function getURL() {
        return "http://codium.io/" . $this->page;
    }

}

function getDB() {
    require_once "db-info.php";
    $data = new DB();
    $mysql = new mysqli($data->ip, $data->user, $data->pass, $data->db);
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

function getUserByHash($hash) {
    $mysql = getDB();

    $stmt = $mysql->prepare("SELECT `user` FROM `sessions` WHERE `hash` = ?");
    $stmt->bind_param("s", $hash);
    $stmt->execute();

    $id = NULL;
    $stmt->bind_result($id);
    if(!$stmt->fetch()) {
        $stmt->close();
        return null;
    }
    $stmt->close();
    return getUserByID($id);
}

function getUserByEmail($email) {
    $mysql = getDB();

    $stmt = $mysql->prepare("SELECT `id`,`fname`,`lname` FROM `users` WHERE `email` = LOWER(?)");
    $stmt->bind_param("s", $email);
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

function doesUserExist($email) {
    $mysql = getDB();
    $stmt = $mysql->prepare("SELECT * FROM `users` WHERE `email` = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->fetch();
    $stmt->close();
    return $result;
}

function getCourse($id) {
    $mysql = getDB();

    $stmt = $mysql->prepare("SELECT `name`,`owner`,`start`,`end`,`open`,`page` FROM `classes` WHERE `id` = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $name = NULL;
    $owner = NULL;
    $start = NULL;
    $end = NULL;
    $open = NULL;
    $page = NULL;
    $stmt->bind_result($name, $owner, $start, $end, $open, $page);
    if(!$stmt->fetch()) {
        $stmt->close();
        return null;
    }
    $stmt->close();
    return new Course($id, $name, getUserByID($owner), $start, $end, $open, $page);
}

function addUser($fname, $lname, $email, $pass) {
    $mysql = getDB();

    $stmt = $mysql->prepare("INSERT INTO `users` VALUES(NULL, ?, ?, LOWER(?), AES_ENCRYPT(LOWER(?), 'codiumisbest'))");
    $stmt->bind_param("ssss", $fname, $lname, $email, $pass);
    $stmt->execute();
    $stmt->close();

    $stmt = $mysql->prepare("SELECT `id` FROM `users` WHERE `fname` = ? AND `lname` = ? AND `email` = LOWER(?) AND `pass` = AES_ENCRYPT(LOWER(?), 'codiumisbest')");
    $stmt->bind_param("ssss", $fname, $lname, $email, $pass);
    $stmt->execute();
    $id = NULL;
    $stmt->bind_result($id);
    if(!$stmt->fetch()) {
        // wat
        die();
    }
    $stmt->close();

    return new User($id, $fname, $lname, $email);
}

?>