<?php
spl_autoload_register(function ($className) {
    $ds = DIRECTORY_SEPARATOR;
    $className = str_replace('\\', $ds, $className);
    $filename = __DIR__ . "{$ds}..{$ds}src{$ds}$className.php";
    require($filename);
});