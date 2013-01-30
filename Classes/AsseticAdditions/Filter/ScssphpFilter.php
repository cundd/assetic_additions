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
#use AsseticAdditions\Filter\AbstractFilter;

/**
 * Loads SCSS files using the PHP implementation of scss, scssphp.
 */
#class ScssphpFilter extends AbstractFilter implements FilterInterface {
class ScssphpFilter implements FilterInterface {
    protected $importPaths = array();

    protected $options = array(
        'style' => 'nested',
        'cache' => FALSE,
        'syntax' => 'scss',
        'debug' => FALSE,
        'debug_info' => TRUE,
        // 'load_paths' => $loadPaths,
        // 'filename' => $path,

        #'load_path_functions' => array('sassy_load_callback'),
        #'functions' => sassy_get_functions(),
        #'callbacks' => array(
        #    'warn' => $watchdog ? 'sassy_watchdog_warn' : NULL,
        #    'debug' => $watchdog ? 'sassy_watchdog_debug' : NULL,
        #),
    );

    public function filterLoad(AssetInterface $asset) {
        $root = $asset->getSourceRoot();
        $path = $asset->getSourcePath();

        $loadPaths = $this->importPaths;
        array_unshift($loadPaths, $root);

        $options = $this->options;
        $options['load_paths'] = $loadPaths;
        $options['filename'] = $path;

        // Execute the compiler.
        $parser = new \SassParser($options);
        $result = $parser->toCss($asset->getContent(), FALSE);
        $asset->setContent($result);
    }

    public function setImportPaths(array $paths) {
        $this->importPaths = $paths;
    }

    public function addImportPath($path) {
        $this->importPaths[] = $path;
    }

    public function filterDump(AssetInterface $asset) {}
}
