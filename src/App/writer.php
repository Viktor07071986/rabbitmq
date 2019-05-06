<?php
namespace App;

class Writer implements Base {
    function render() {
        echo "Hello, I'am render from WRITER";
    }
    function processData() {
        echo "Hello, I'am processData from WRITER";
    }
}