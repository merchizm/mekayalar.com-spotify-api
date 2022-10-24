<?php
header("Access-Control-Allow-Origin: *"); // for dev mode, you should remove this line in production.
error_reporting(0); // disable warnings.

require 'vendor/autoload.php';
use Rocks\app;

$app = new app();

echo $app->run();