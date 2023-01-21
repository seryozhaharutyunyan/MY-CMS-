<?php
namespace Rout;

use Rout\Router\Router;
use \App\Controllers\IndexController;

$rout=Router::getInstance();

$rout->get('/', new IndexController());
