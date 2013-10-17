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
use Assetic\Filter\Sass\SassFilter;
use Assetic\Exception\FilterException;
#use AsseticAdditions\Filter\AbstractFilter;



/**
 * Loads SCSS files using the C implementation sassc and libsass.
 */
class SasscFilter extends SassFilter implements FilterInterface {
	public function __construct($sassPath = '/usr/bin/sassc', $rubyPath = null)
    {
        $this->sassPath = $sassPath;
    }

    public function setCompass($compass)
    {
    	// Not supported
    }

    public function filterLoad(AssetInterface $asset)
    {
        $sassProcessArgs = array($this->sassPath);
        // if (null !== $this->rubyPath) {
        //     $sassProcessArgs = array_merge(explode(' ', $this->rubyPath), $sassProcessArgs);
        // }

        $pb = $this->createProcessBuilder($sassProcessArgs);


        $assetDirectory = '';
        if (method_exists($asset, 'getSourceDirectory')) {
        	$assetDirectory = $asset->getSourceDirectory();
        } else {
        	$root = $asset->getSourceRoot();
	        $path = $asset->getSourcePath();
        	$assetDirectory = dirname($root.'/'.$path);
        }
        
        $allLoadPaths = $this->loadPaths;
        array_unshift($allLoadPaths, $assetDirectory);
        $pb->add('-I')->add(implode(':', $allLoadPaths));

        // if ($this->unixNewlines) {
        //     $pb->add('--unix-newlines');
        // }

        // if (true === $this->scss || (null === $this->scss && 'scss' == pathinfo($asset->getSourcePath(), PATHINFO_EXTENSION))) {
        //     $pb->add('--scss');
        // }

        if ($this->style) {
            $pb->add('-t')->add($this->style);
        }

        // if ($this->quiet) {
        //     $pb->add('--quiet');
        // }

        // if ($this->debugInfo) {
        //     $pb->add('--debug-info');
        // }

        if ($this->lineNumbers) {
            $pb->add('-l');
        }

        

        // if ($this->cacheLocation) {
        //     $pb->add('--cache-location')->add($this->cacheLocation);
        // }

        // if ($this->noCache) {
        //     $pb->add('--no-cache');
        // }

        // if ($this->compass) {
        //     $pb->add('--compass');
        // }

        // input
        // $pb->add($input = tempnam(sys_get_temp_dir(), 'assetic_sass'));
        // file_put_contents($input, $asset->getContent());

	    $pb->add($asset->getSourceRoot() . '/' . $asset->getSourcePath());

        $proc = $pb->getProcess();
        $code = $proc->run();
        // unlink($input);

        if (0 !== $code) {
            throw FilterException::fromProcess($proc);//->setInput($asset->getContent());
        }

        $asset->setContent($proc->getOutput());
    }
	
	/**
	 * Sets the import paths for the compiler to use
	 * @param array $paths Array of directory paths
	 */
	public function setImportPaths(array $paths) {
		$this->loadPaths = $paths;
	}

	/**
	 * Add an import path for the compiler to use
	 * @param string $path
	 */
	public function addImportPath($path) {
		$this->loadPaths[] = $path;
	}
}