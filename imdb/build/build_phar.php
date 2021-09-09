<?php

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/PharBuilder.php';

$finder = PharBuilder::getFilesToIncludeInPharAsFinder();
$pharBuilder = new PharBuilder(__DIR__.'/imdb.phar', $finder);

$pharBuilder->build();
