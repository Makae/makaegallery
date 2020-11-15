<?php
function load_test_dependencies($baseDir = "")
{
    require_once($baseDir . 'loader.php');
    require_once($baseDir . 'config-test.php');

    load_dependencies($baseDir);

    $dependencyLoader = new DependencyLoader();
    $dependencyLoader->loadMultipleDependencies([
        $baseDir . "tests/classes/",
        $baseDir . "tests/mocks/"
    ]);
}

load_test_dependencies("../");
