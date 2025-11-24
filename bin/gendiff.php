<?php

$autoloadPath1 = __DIR__ . '/../vendor/autoload.php';
$autoloadPath2 = __DIR__ . '/../../autoload.php';

if(file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} elseif (file_exists($autoloadPath2)) {
    require_once $autoloadPath2;
} else {
    fwrite(STDERR, "Composer autoload not found. Run 'composer install'.\n");
    exit(1);
}

$doc = <<<DOC
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)

Options:
  -h --help                     Show this screen
  -v --version                  Show version
DOC;

$options = ['version' => 'gendiff 1.0.0'];
$result = Docopt::handle($doc, $options);
