<?php
const VG_ACCESS = true;
session_start();
include_once('../Settings/settings.php');
include_once('../Settings/print.php');
include_once('../Vendor/autoload.php');

use Rout\Router\Router;
include_once('../Rout/rout.php');

Router::getInstance()->start();
