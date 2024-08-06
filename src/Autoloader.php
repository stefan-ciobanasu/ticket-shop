<?php

spl_autoload_register( 'psr4_autoloader' );

/**
 * @param string $class The fully-qualified class name.
 * @return void
 */
function psr4_autoloader(string $class): void
{
    // replace namespace separators with directory separators in the relative
    // class name, append with .php
    $class_path = str_replace('\\', '/', $class);

    $file =  __DIR__ . '/' . $class_path . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
}
