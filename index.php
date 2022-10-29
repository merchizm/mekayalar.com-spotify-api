<?php
require 'vendor/autoload.php';
use Rocks\app;

$app = new app();

echo $app->run();