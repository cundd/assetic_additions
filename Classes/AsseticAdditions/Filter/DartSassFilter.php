<?php

namespace AsseticAdditions\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\DependencyExtractorInterface;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Loads SCSS files using [Dart Sass](https://sass-lang.com/dart-sass)
 */
class DartSassFilter extends AbstractLibSassFilter implements DependencyExtractorInterface, LibSassFilterInterface
{
    /**
     * WtFilter constructor
     *
     * @param string $binaryPath
     */
    public function __construct($binaryPath = '/usr/bin/sass')
    {
        parent::__construct($binaryPath);
    }

    protected function configureProcess(AssetInterface $asset, ProcessBuilder $processBuilder)
    {
        foreach ($this->getIncludePaths($asset) as $includePath) {
            $processBuilder->add('-I')->add($includePath);
        }

        if ($this->style) {
            $processBuilder->add('--style')->add($this->style);
        }
        if ($this->emitSourceMap) {
            $processBuilder->add('--source-map');
            $processBuilder->add('--embed-source-map');
        }

        $processBuilder->add($asset->getSourceRoot() . '/' . $asset->getSourcePath());
    }
}
