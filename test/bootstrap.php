<?php

$autoloader = require __DIR__.'/../vendor/autoload.php';
$autoloader->add('Eloquent\Equality\TestFixture', __DIR__.'/src');

Eloquent\Asplode\Asplode::instance()->install();
