<?php
declare(strict_types=1);

namespace AsseticAdditions\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Filter\BaseProcessFilter;
use function array_unshift;

/**
 * Compile SCSS files using a standalone Sass solution
 */
abstract class AbstractSassFilter extends BaseProcessFilter implements SassFilterInterface
{
    protected ?string $style;

    protected bool $lineNumbers = false;

    protected bool $emitSourceMap = false;

    protected array $loadPaths = [];

    /**
     * @param string $binaryPath Path to the binary
     */
    public function __construct(string $binaryPath)
    {
        parent::__construct($binaryPath);
        $this->style = null;
    }

    /**
     * Collect the arguments that should be passed to the process
     *
     * @param AssetInterface $asset
     * @return array
     */
    abstract protected function collectProcessArguments(AssetInterface $asset): array;

    public function filterLoad(AssetInterface $asset)
    {
        $processArguments = $this->collectProcessArguments($asset);
        array_unshift($processArguments, $this->binaryPath);
        $process = $this->createProcess($processArguments);

        try {
            if (0 !== $process->run()) {
                throw FilterException::fromProcess($process);
            }
        } catch (\Symfony\Component\Process\Exception\RuntimeException $exception) {
            throw FilterException::fromProcess($process);
        }
        $asset->setContent($process->getOutput());
    }

    public function setImportPaths(array $paths)
    {
        $this->loadPaths = $paths;
    }

    public function addImportPath(string $path)
    {
        $this->loadPaths[] = $path;
    }

    public function setStyle(string $style)
    {
        $this->style = $style;
    }

    public function setEmitSourceMap(bool $emitSourceMap)
    {
        $this->emitSourceMap = $emitSourceMap;
    }

    public function setLineNumbers(bool $lineNumbers)
    {
        $this->lineNumbers = $lineNumbers;
    }

    public function filterDump(AssetInterface $asset)
    {
    }

    /**
     * Return the include-paths
     *
     * @param AssetInterface $asset
     * @return array
     */
    protected function getIncludePaths(AssetInterface $asset): array
    {
        if (method_exists($asset, 'getSourceDirectory')) {
            $assetDirectory = $asset->getSourceDirectory();
        } else {
            $assetDirectory = dirname($asset->getSourceRoot() . '/' . $asset->getSourcePath());
        }

        $allLoadPaths = $this->loadPaths;
        array_unshift($allLoadPaths, $assetDirectory);

        return $allLoadPaths;
    }
}
