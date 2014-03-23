<?php

require_once "all.php";

//echo getUser('gregthegeek@optonline.net', 'password')->lname;
//echo getCourse(1)->id . '<br />';
//echo getUser('gregthegeek@optonline.net', 'password')->hosting()[0]->name;
//echo getUser('gregthegeek@optonline.net', 'passwords') == NULL ? 'true' : 'false';
//echo getCourse(1)->enrolled()[0]->fname;
//addUser("Hello", "There", "em@mail.com", "password");
//getUser("em@mail.com", "password")->hostCourse("MyCourse", time(), time());
//getCourse(2)->enroll(getUserByID(2));
//echo getUser('gregthegeek@optonline.net', 'password')->startSession();
//echo getUserByHash('e2ab7201f97d93fa4ddbd0e19412e736a219901aa2b643')->lname;
//getCourse(1)->invite("gregthegeek2@optonline.net");
//echo getCourse(1)->isEnrolled(getUserByID(1)) ? 'yes' : 'no';
// echo getCourse(1)->getFirebaseIDFor(getUserByID(1));
//echo var_dump(getUserByHash($_COOKIE['hash'])->enrolled());
//echo var_dump(getUserById(1)->enrolled());
//echo var_dump(getCourse(1));
// getCourse(2)->invite("gregthegeek@optonline.net");


$ch = curl_init("https://codium.firebaseio.com/doc-1-1/lang.json");

curl_setopt($ch, CURLOPT_HEADER, 0);

echo json_decode(curl_exec($ch));
curl_close($ch);

/*$data = array("lang" => "javascript");
$ch = curl_init("https://codium.firebaseio.com/doc-1-1.json");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

echo curl_exec($ch);*/

?>