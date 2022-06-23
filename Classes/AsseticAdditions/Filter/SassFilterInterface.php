<?php
declare(strict_types=1);

namespace AsseticAdditions\Filter;

interface SassFilterInterface
{
    const STYLE_NESTED = 'nested';
    const STYLE_EXPANDED = 'expanded';
    const STYLE_COMPACT = 'compact';
    const STYLE_COMPRESSED = 'compressed';

    /**
     * Sets the import paths for the compiler to use
     *
     * @param array $paths Array of directory paths
     */
    public function setImportPaths(array $paths);

    /**
     * Add an import path for the compiler to use
     *
     * @param string $path
     */
    public function addImportPath(string $path);

    /**
     * Sets the style to use for the generated CSS
     *
     * @param string $style One of the STYLE constants
     */
    public function setStyle(string $style);

    /**
     * Sets if a source map should be created
     *
     * @param boolean $emitSourceMap
     */
    public function setEmitSourceMap(bool $emitSourceMap);

    /**
     * Sets if line numbers should be added
     *
     * The filter may ignore this setting
     *
     * @param boolean $lineNumbers
     */
    public function setLineNumbers(bool $lineNumbers);
}
