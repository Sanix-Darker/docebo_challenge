<?php

// We set some header informations
// For the return
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Cache-Control: private");
header("Pragma: no-cache");
header('Content-Type:text/json; Charset=UTF-8');


// We include the core
include_once("./utils/core.php");

// The main function
main();
