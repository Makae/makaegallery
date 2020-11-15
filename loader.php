<?php

class DependencyLoader
{
    public function loadDependencies($baseDir)
    {
        $this->loadMultipleDependencies([
            $baseDir . "**trait.*.php",
            $baseDir . "interface.*.php",
            $baseDir . "**/interface.*.php",
            $baseDir . "class.*.php",
            $baseDir . "**/class.*.php"
        ]);
    }

    public function loadMultipleDependencies(array $array)
    {
        foreach ($array as $pattern) {
            foreach (glob($pattern) as $filename) {
                if(is_dir($filename)) {
                    continue;
                }
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

