<?php
chdir(__DIR__);

define("PHP_PATH", '$(which php)');
define("PHPUNIT_PATH", '$(which phpunit)');

$extensions = array(
    'gd',
    'gmagick',
    'imagick'
);

$default_groups = array('unit');

foreach(array_merge($default_groups, $extensions) as $lib) {
    executeGroup($lib);
}


function executeGroup($lib) {
    $php_path = PHP_PATH;
    $phpunit_path = PHPUNIT_PATH;

    if (doesConflict($lib)) {
        showConflictMessageFor($lib);
        return;
    }

    $load_extension = constructExtensionParam($lib);

    $additional_params = parseAdditionalParams();

    $command = "$php_path $load_extension $phpunit_path --group $lib $additional_params .";

    echo "Running [$lib]:\n";
    passthru($command);
    echo "\n";
}

function getExtensionLibrary($extension) {
    $pattern = ini_get('extension_dir') . DIRECTORY_SEPARATOR . "$extension.{so,dll}";
    $files = glob($pattern, GLOB_BRACE);

    return !empty($files) ? basename(reset($files)) : false;
}

function constructExtensionParam($lib) {
    if (extension_loaded($lib)) {
        return '';
    }

    if ($extension_file = getExtensionLibrary($lib)) {
        return "-dextension=$extension_file";
    }

    return '';
}

function parseAdditionalParams() {
    if ($_SERVER['argc'] < 2) {
        return "";
    }

    $argv = $_SERVER['argv'];
    array_shift($argv);
    
    return implode(" ", $argv);
}

function doesConflict($lib) {
    // imagick and gmagick conflict with each other.
    // Surprisingly, if imagick is enabled in php.ini
    // gmagick can be loaded dynamically with php cli's -d option,
    // though vice versa variant doesn't work
    if ($lib == 'imagick' && extension_loaded('gmagick')) {
        return true;
    }

    return false;
}

function showConflictMessageFor($lib) {
    if ($lib == 'imagick') {
        echo "\n=================================================\n";
        echo "\nImagick and Gmagick conflict with each other.";
        echo "\nTry disabling Gmagick in your configuration file, ";
        echo "so our runner can load them separately.\n";
        echo "\n=================================================\n";
    }
}
