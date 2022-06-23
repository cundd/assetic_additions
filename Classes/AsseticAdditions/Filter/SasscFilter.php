<?php
declare(strict_types=1);

namespace AsseticAdditions\Filter;

use Assetic\Asset\AssetInterface;
use function implode;

/**
 * Load SCSS files using the C implementation sassc and libsass.
 */
class SasscFilter extends AbstractSassFilter
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

    protected function collectProcessArguments(AssetInterface $asset): array
    {
        $arguments[] = '-I';
        $arguments[] = implode(':', $this->getIncludePaths($asset));

        if ($this->style) {
            $arguments[] = '-t';
            $arguments[] = $this->style;
        }
        if ($this->lineNumbers) {
            $arguments[] = '-l';
        }
        if ($this->emitSourceMap) {
            $arguments[] = $this->getEmitSourceMapOption();
        }

        $arguments[] = $asset->getSourceRoot() . '/' . $asset->getSourcePath();

        return $arguments;
    }

    /**
     * Return either "-m" or "-g"
     *
     * @return string
     */
    protected function getEmitSourceMapOption(): string
    {
        $process = $this->createProcess([$this->binaryPath, '-h']);
        $process->run();

        $output = $process->getOutput();
        if (strpos($output, '--sourcemap[=TYPE]') !== false) {
            return '--sourcemap=inline';
        }

        return strpos($output, '-m, --sourcemap') !== false ? '-m' : '-g';
    }
}
