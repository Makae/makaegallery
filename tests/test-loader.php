<?php
function load_test_dependencies($baseDir = "")
{
    require_once($baseDir . 'loader.php');
    require_once($baseDir . 'config-test.php');

    load_dependencies($baseDir . 'classes/');

    $dependencyLoader = new DependencyLoader();
    $dependencyLoader->loadMultipleDependencies([
        $baseDir . "tests/classes/class.*.php",
        $baseDir . "tests/classes/mocks/mock.*.php"
    ]);
}
load_test_dependencies( dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
