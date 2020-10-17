<?php

function load_dependencies($base_dir="")
{
    foreach (glob($base_dir . "classes/trait.*.php") as $filename)
        require $filename;

    foreach (glob($base_dir . "interfaces/interface.*.php") as $filename)
        require $filename;

    foreach (glob($base_dir . "classes/class.*.php") as $filename)
        require $filename;
}
