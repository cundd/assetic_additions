<?php

namespace AsseticAdditions\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Factory\AssetFactory;
use Assetic\Filter\BaseProcessFilter;
use Assetic\Filter\DependencyExtractorInterface;
use Assetic\Util\CssUtils;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Loads SCSS files using a wrapper of [LibSass](http://sass-lang.com/libsass)
 */
abstract class AbstractLibSassFilter extends BaseProcessFilter implements DependencyExtractorInterface, LibSassFilterInterface
{
    protected $binaryPath;
    protected $style;
    protected $lineNumbers;
    protected $emitSourceMap;
    protected $loadPaths = [];

    /**
     * @param string $binaryPath Path to the binary
     */
    public function __construct($binaryPath)
    {
        $this->binaryPath = (string)$binaryPath;
    }

    /**
     * Configure the process
     *
     * @param AssetInterface $asset
     * @param ProcessBuilder $processBuilder
     */
    abstract protected function configureProcess(AssetInterface $asset, ProcessBuilder $processBuilder);

    public function filterLoad(AssetInterface $asset)
    {
        $sassProcessArgs = [$this->binaryPath];
        $processBuilder = $this->createProcessBuilder($sassProcessArgs);

        $this->configureProcess($asset, $processBuilder);

        $process = $processBuilder->getProcess();
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

    public function addImportPath($path)
    {
        $this->loadPaths[] = $path;
    }

    public function setStyle($style)
    {
        $this->style = $style;
    }

    public function setEmitSourceMap($emitSourceMap)
    {
        $this->emitSourceMap = $emitSourceMap;
    }

    public function setLineNumbers($lineNumbers)
    {
        $this->lineNumbers = $lineNumbers;
    }

    public function filterDump(AssetInterface $asset)
    {
    }

    /**
     * @TODO: Check if this could be removed
     * @inheritdoc
     */
    public function getChildren(AssetFactory $factory, $content, $loadPath = null)
    {
        $loadPaths = $this->loadPaths;
        if ($loadPath) {
            array_unshift($loadPaths, $loadPath);
        }

        if (!$loadPaths) {
            return [];
        }

        $children = [];
        foreach (CssUtils::extractImports($content) as $reference) {
            if ('.css' === substr($reference, -4)) {
                // skip normal css imports
                // todo: skip imports with media queries
                continue;
            }

            // the reference may or may not have an extension or be a partial
            if (pathinfo($reference, PATHINFO_EXTENSION)) {
                $needles = [
                    $reference,
                    self::partialize($reference),
                ];
            } else {
                $needles = [
                    $reference . '.scss',
                    $reference . '.sass',
                    self::partialize($reference) . '.scss',
                    self::partialize($reference) . '.sass',
                ];
            }

            foreach ($loadPaths as $loadPath) {
                foreach ($needles as $needle) {
                    if (file_exists($file = $loadPath . '/' . $needle)) {
                        $coll = $factory->createAsset($file, [], ['root' => $loadPath]);
                        foreach ($coll as $leaf) {
                            /** @var AssetInterface $leaf */
                            $leaf->ensureFilter($this);
                            $children[] = $leaf;
                            break 3;
                        }
                    }
                }
            }
        }

        return $children;
    }

    private static function partialize($reference)
    {
        $parts = pathinfo($reference);

        if ('.' === $parts['dirname']) {
            $partial = '_' . $parts['filename'];
        } else {
            $partial = $parts['dirname'] . DIRECTORY_SEPARATOR . '_' . $parts['filename'];
        }

        if (isset($parts['extension'])) {
            $partial .= '.' . $parts['extension'];
        }

        return $partial;
    }

    /**
     * @see setImportPaths()
     * @param array $loadPaths
     * @deprecated
     */
    public function setLoadPaths(array $loadPaths)
    {
        $this->setImportPaths($loadPaths);
    }

    /**
     * @see addImportPath()
     * @param $loadPath
     * @deprecated
     */
    public function addLoadPath($loadPath)
    {
        $this->addImportPath($loadPath);
    }

    /**
     * Returns the include paths
     *
     * @param AssetInterface $asset
     * @return array
     */
    protected function getIncludePaths(AssetInterface $asset)
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
