<?php

namespace AsseticAdditions\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\DependencyExtractorInterface;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Loads SCSS files using Wellington (https://getwt.io/)
 */
class WtFilter extends AbstractLibSassFilter implements DependencyExtractorInterface, LibSassFilterInterface
{
    /**
     * WtFilter constructor
     *
     * @param string $binaryPath
     */
    public function __construct($binaryPath = '/usr/bin/wt')
    {
        parent::__construct($binaryPath);
    }

    protected function configureProcess(AssetInterface $asset, ProcessBuilder $processBuilder)
    {
        $processBuilder->add('-I')->add(implode(':', $this->getIncludePaths($asset)));

        if ($this->style) {
            $processBuilder->add('-s')->add($this->style);
        }
        if ($this->emitSourceMap) {
            $processBuilder->add('--source-map');
        }

        $processBuilder->add('compile');
        $processBuilder->add($asset->getSourceRoot() . '/' . $asset->getSourcePath());
    }
}
