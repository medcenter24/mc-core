<?php
/**
 * Copyright (c) 2017.
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace App\Support\Core;

/**
 * Make things more configurable
 *
 * Class Configurable
 * @package App\Services
 */
abstract class Configurable implements ConfigurableInterface
{
    private $options = [];

    /**
     * public constructor to allow the object to be recreated from php code
     *
     * @param array $options
     */
    public function __construct($options = []) {
        $this->setOptions($options);
    }

    /**
     * Set option
     *
     * @param $name
     * @param $value
     */
    public function setOption($name, $value) {
        $this->options[$name] = $value;
    }

    /**
     * Set options
     *
     * @param array $options
     * @param bool $overwrite
     * @return void
     */
    public function setOptions($options, $overwrite = false) {
        if (!is_array($options)) {
            if (is_object($options) && method_exists($options, 'toArray')) {
                $options = $options->toArray();
            } else {
                new \Exception('Options submitted to '.get_called_class().' must be an array or implement toArray');
            }
        }

        if (!$overwrite) {
            $this->options = array_merge($this->getOptions(), $options);
        } else {
             $this->options = $options;
        }
    }

    /**
     * Returns whenever or not the option is defined
     *
     * @param  string $name
     * @return boolean
     */
    public function hasOption($name) {
        return isset($this->options[$name]);
    }

    /**
     * Get an option value by name
     *
     * If the option is empty or not set a NULL value will be returned.
     *
     * @param  string $name
     * @return mixed
     */
    public function getOption($name) {
        return isset($this->options[$name]) ? $this->options[$name] : null;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }
}
