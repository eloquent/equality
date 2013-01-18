<?php

use Composer\Autoload\ClassLoader;

$autoloader = new ClassLoader;
$autoloader->add('Eloquent\Equality\TestFixture', __DIR__.'/src');
$autoloader->register();
