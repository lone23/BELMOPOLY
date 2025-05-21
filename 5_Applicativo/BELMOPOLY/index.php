<?php

require 'application/config/config.php';
require_once 'vendor/autoload.php';

ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);


$app = new libs\Application();

