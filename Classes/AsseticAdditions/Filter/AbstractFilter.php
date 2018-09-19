<?php

namespace AsseticAdditions\Filter;

use Assetic\Filter\FilterInterface;

/**
 * Abstract filter class
 */
abstract class AbstractFilter implements FilterInterface
{
    /**
     * Underlying compiler
     *
     * @var object
     */
    protected $compiler;

    /**
     * Forward undefined method calls to the compiler
     *
     * @param  string $name      Method name
     * @param  array  $arguments Arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if ($this->compiler && method_exists($this->compiler, $name)) {
            return call_user_func_array([$this->compiler, $name], $arguments);
        }

        return null;
    }

    /**
     * Dumps a given variable (or the given variables) wrapped into a 'pre' tag.
     *
     * @param    mixed $var1
     */
    public function pd($var1 = '__iresults_pd_noValue')
    {
        if (class_exists('Tx_Iresults')) {
            $arguments = func_get_args();
            call_user_func_array(['Tx_Iresults', 'pd'], $arguments);
        }
    }
}