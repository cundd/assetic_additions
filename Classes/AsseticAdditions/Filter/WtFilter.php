<?php
declare(strict_types=1);

namespace AsseticAdditions\Filter;

use Assetic\Asset\AssetInterface;
use function implode;

/**
 * Loads SCSS files using [Wellington](https://github.com/wellington/wellington)
 */
class WtFilter extends AbstractSassFilter
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

    protected function collectProcessArguments(AssetInterface $asset): array
    {
        $arguments[] = '-I';
        $arguments[] = implode(':', $this->getIncludePaths($asset));

        if ($this->style) {
            $arguments[] = '-s';
            $arguments[] = $this->style;
        }
        if ($this->emitSourceMap) {
            $arguments[] = '--source-map';
        }

        $arguments[] = 'compile';
        $arguments[] = $asset->getSourceRoot() . '/' . $asset->getSourcePath();

        return $arguments;
    }
}
