<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Finder\Finder;

class PharBuilder
{
    private $pathToPhar;
    private $finder;

    const CONSOLE_FILE = 'console';

    public function __construct(string $pathToPhar, Finder $finder = null)
    {
        $this->pathToPhar = $pathToPhar;
        $this->finder = $finder;

        if (!$finder) {
            $this->finder = self::getFilesToIncludeInPharAsFinder();
        }
    }

    public function build()
    {
        // To build a .phar on a computer the following line should be added to your php.ini
        // $ echo "phar.readonly=0" >> /usr/local/etc/php/7.3/conf.d/enable-phar.ini"
        $phar = new \Phar($this->pathToPhar);
        $baseDirToRemoveFromFiles = __DIR__ . '/../';
        $phar->buildFromIterator($this->finder->getIterator(), $baseDirToRemoveFromFiles);
        // The parameter 'console' should be the related path inside the .phar file to the initScript
        $bootstrapScript = \Phar::createDefaultStub(self::CONSOLE_FILE);
        $phar->setStub("#!/usr/bin/env php\n" . $bootstrapScript);
    }

    public static function getFilesToIncludeInPharAsFinder()
    {
        $finder = new Finder();
        $finder
            ->in(__DIR__ . '/../')
            ->notPath('build/')
            ->files()
            ->name('*.php') // only PHP files will be included
            ->name(self::CONSOLE_FILE)
        ;

        return $finder;
    }
}

