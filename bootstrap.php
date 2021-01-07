<?php
require_once __DIR__ . '/vendor/autoload.php';

define('BASE_PATH', __DIR__);

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$capsule->addConnection([
    "driver" => "mysql",
    "host" => "mysql",
    "database" => "ms_encoder_video",
    "username" => "root",
    "password" => "root"
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();
