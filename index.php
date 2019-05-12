<div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 15px;">
    <a href="reader" style="margin-right: 50px;">reader</a>
    <a href="writer">writer</a>
</div>

<?php

$loader = require 'vendor/autoload.php';
$loader->add('App\\', __DIR__.'/src/');
$class = ucfirst(ltrim($_SERVER["REQUEST_URI"], "/"));
$class = "App\\".$class;

if (class_exists($class)) {
    $factory = new $class();
    echo $factory->render();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        echo $factory->processData();
    }
}

?>