<?php

setcookie("hash", "foo", time() - 1000, "/");
header("Location: index.php");

?>