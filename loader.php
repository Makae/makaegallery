<?php

function load_test_dependencies($base_dir="") {
    include_once('config-test.php');

    foreach (glob($base_dir . "tests/classes/class.*.php") as $filename)
        require $filename;

    load_dependencies($base_dir);
}
function load_dependencies($base_dir="")
{
    foreach (glob($base_dir . "classes/trait.*.php") as $filename)
        require $filename;

    foreach (glob($base_dir . "interfaces/interface.*.php") as $filename)
        require $filename;

    foreach (glob($base_dir . "classes/class.*.php") as $filename)
        require $filename;
}
