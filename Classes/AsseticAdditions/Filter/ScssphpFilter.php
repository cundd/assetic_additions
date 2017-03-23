<?php
namespace AsseticAdditions\Filter;

/*
 * Copyright (c) 2013 Daniel Corn
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use AsseticAdditions\CompilerInterface;

/**
 * Loads SCSS files using the PHP implementation of scss, scssphp.
 */
class ScssphpFilter extends AbstractFilter implements FilterInterface
{
    /**
     * Indicates if compass should be used
     *
     * @var boolean
     */
    protected $compass = false;

    /**
     * The class name of the formatter to use
     *
     * @var string
     */
    protected $formatter = '';

    /**
     * The import paths for the compiler to use
     *
     * @var array
     */
    protected $importPaths = array();

    /**
     * Compile/filter the asset
     *
     * @param  AssetInterface $asset
     */
    public function filterLoad(AssetInterface $asset)
    {
        $root = $asset->getSourceRoot();
        $path = $asset->getSourcePath();


        $lc = $this->getCompiler();
        if ($this->compass) {
            $this->configureCompass($lc);
        }

        // Enable strict file imports, if supported
        if (method_exists($lc, 'setThrowExceptionIfImportFileNotFound')) {
            $lc->setThrowExceptionIfImportFileNotFound(true);
        }

        // Set the formatter
        if ($this->formatter) {
            $lc->setFormatter($this->formatter);
        }

        if ($root && $path) {
            $lc->addImportPath(dirname($root . '/' . $path));
        }
        foreach ($this->importPaths as $path) {
            $lc->addImportPath($path);
        }

        try {
            $content = $lc->compile($asset->getContent());
        } catch (\Exception_ScssException $exception) {
            throw $exception;
        }
        $asset->setContent($content);
    }

    /**
     * Returns the class name of the formatter to use
     *
     * @return string
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * Sets the class name of the formatter to use
     *
     * @param string $formatter
     * @return $this
     */
    public function setFormatter($formatter)
    {
        var_dump($formatter);
        $this->formatter = $formatter;

        return $this;
    }

    /**
     * Enable/disable compass for the filter
     *
     * @param  boolean $enable
     * @return void
     */
    public function enableCompass($enable = true)
    {
        $this->compass = (bool)$enable;
    }

    /**
     * Returns if compass is enabled
     *
     * @return boolean
     */
    public function isCompassEnabled()
    {
        return $this->compass;
    }

    /**
     * Sets the import paths for the compiler to use
     *
     * @param array $paths Array of directory paths
     */
    public function setImportPaths(array $paths)
    {
        $this->importPaths = $paths;
    }

    /**
     * Add an import path for the compiler to use
     *
     * @param string $path
     */
    public function addImportPath($path)
    {
        $this->importPaths[] = $path;
    }

    public function filterDump(AssetInterface $asset)
    {
    }

    /**
     * @return \AsseticAdditions\CompilerInterface|\Leafo\ScssPhp\Compiler
     */
    private function getCompiler()
    {
        $factory = new \AsseticAdditions\CompilerFactory\Scss\Leafo();

        return $factory->createCompiler();
    }

    /**
     * @param CompilerInterface $compiler
     */
    private function configureCompass($compiler)
    {
        if (!class_exists('scss_compass')) {
            throw new \LogicException('Class scss_compass not found');
        }
        new \scss_compass($compiler);
    }
}