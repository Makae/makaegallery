<?php

class DependencyLoader
{
    public function loadDependencies($baseDir)
    {
        $this->loadMultipleDependencies([
            $baseDir . "**trait.*.php",
            $baseDir . "**/interface.*.php",
            $baseDir . "**/**/interface.*.php",
            $baseDir . "class.*.php",
            $baseDir . "**/class.*.php",
            $baseDir . "**/**/class.*.php",
        ]);
        //print_r("=======\n");
    }

    public function loadMultipleDependencies(array $array)
    {
        foreach ($array as $pattern) {
            // print_r("Loading glob: " . $pattern . "\n");
            foreach (glob($pattern) as $filename) {
                if(is_dir($filename)) {
                    continue;
                }
                // print_r($filename . "\n");
                require $filename;
            }
        }
    }
}


function load_dependencies($baseDir = "")
{
    $dependencyLoader = new DependencyLoader();
    $dependencyLoader->loadDependencies($baseDir);
}

