<a href=/reader>reader</a></br>
<a href=/writer>writer</a></br>
<?php
$loader = require 'vendor/autoload.php';
$loader->add('App\\', __DIR__.'/src/');
use App\Reader;
use App\Writer;

$class = ucfirst(ltrim($_SERVER["REQUEST_URI"], "/"));
$class = "App\\".$class;

if (class_exists($class)) {
    $factory = new $class;
    $factory->render();
}