<?php
/**
 * Created by PhpStorm.
 * User: cod
 * Date: 23.3.17
 * Time: 10:09
 */
namespace AsseticAdditions\Filter;


/**
 * Loads SCSS files using a wrapper of [LibSass](http://sass-lang.com/libsass)
 */
interface LibSassFilterInterface
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
    public function addImportPath($path);

    /**
     * Sets the style to use for the generated CSS
     *
     * @param string $style One of the STYLE constants
     */
    public function setStyle($style);

    /**
     * Sets if a source map should be created
     *
     * @param boolean $emitSourceMap
     * @internal This is not implemented yet
     */
    public function setEmitSourceMap($emitSourceMap);

    /**
     * Sets if line numbers should be added
     *
     * The filter may ignore this setting
     *
     * @param boolean $lineNumbers
     */
    public function setLineNumbers($lineNumbers);
}
