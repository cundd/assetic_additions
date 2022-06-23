<?php
declare(strict_types=1);

namespace AsseticAdditions\Filter;

use Assetic\Asset\AssetInterface;

/**
 * Loads SCSS files using [Dart Sass](https://sass-lang.com/dart-sass)
 */
class DartSassFilter extends AbstractSassFilter
{
    /**
     * Dart Sass constructor
     *
     * @param string $binaryPath
     */
    public function __construct($binaryPath = '/usr/bin/sass')
    {
        parent::__construct($binaryPath);
    }

    protected function collectProcessArguments(AssetInterface $asset): array
    {
        $arguments = [];
        foreach ($this->getIncludePaths($asset) as $includePath) {
            $arguments[] = '-I';
            $arguments[] = $includePath;
        }

        if ($this->style) {
            $arguments[] = '--style';
            $arguments[] = $this->style;
        }
        if ($this->emitSourceMap) {
            $arguments[] = '--source-map';
            $arguments[] = '--embed-source-map';
        }

        $arguments[] = $asset->getSourceRoot() . '/' . $asset->getSourcePath();

        return $arguments;
    }
}
