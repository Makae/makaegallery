<?php

class DependencyLoader
{
    public function loadDependencies($baseDir)
    {
        $this->loadMultipleDependencies([
            $baseDir . "classes/**trait.*.php",
            $baseDir . "interfaces/interface.*.php",
            $baseDir . "classes/class.*.php",
            $baseDir . "classes/**/class.*.php"
        ]);
    }

    private function loadMultipleDependencies(array $array)
    {
        foreach ($array as $pattern) {
            foreach (glob($pattern) as $filename) {
                require $filename;
            }
        }
    }
}

function load_test_dependencies($baseDir = "")
{
    include_once('config-test.php');

    $dependencyLoader = new DependencyLoader();
    $dependencyLoader->loadDependencies($baseDir);
    $dependencyLoader->loadDependencies($baseDir . "tests");
}

function load_dependencies($baseDir = "")
{
    $dependencyLoader = new DependencyLoader();
    $dependencyLoader->loadDependencies($baseDir);
}

