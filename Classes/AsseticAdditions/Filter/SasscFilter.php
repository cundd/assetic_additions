<?php

namespace AsseticAdditions\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\DependencyExtractorInterface;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Loads SCSS files using the C implementation sassc and libsass.
 */
class SasscFilter extends AbstractLibSassFilter implements DependencyExtractorInterface, LibSassFilterInterface
{
    /**
     * SasscFilter constructor.
     *
     * @param string $binaryPath
     */
    public function __construct($binaryPath = '/usr/bin/sassc')
    {
        parent::__construct($binaryPath);
    }

    protected function configureProcess(AssetInterface $asset, ProcessBuilder $processBuilder)
    {
        $processBuilder->add('-I')->add(implode(':', $this->getIncludePaths($asset)));

        if ($this->style) {
            $processBuilder->add('-t')->add($this->style);
        }
        if ($this->lineNumbers) {
            $processBuilder->add('-l');
        }
        if ($this->emitSourceMap) {
            $processBuilder->add($this->getEmitSourceMapOption());
        }

        $processBuilder->add($asset->getSourceRoot() . '/' . $asset->getSourcePath());
    }


    /**
     * Returns either "-m" or "-g"
     *
     * @return string
     */
    protected function getEmitSourceMapOption()
    {
        $sassProcessArgs = [$this->binaryPath];
        $processBuilder = $this->createProcessBuilder($sassProcessArgs);
        $processBuilder->add('-h');

        $process = $processBuilder->getProcess();
        $process->run();

        return strpos($process->getOutput(), '-m, --sourcemap') !== false ? '-m' : '-g';
    }
}
