<?php
/**
 * @file
 * PHP server-site support for Cirsim: API settings.
 */

namespace CL\CirsimPHP;

/**
 * PHP server-site support for Cirsim: API settings.
 *
 * @property string save API path for saving files
 * @property string load API path for loading files
 * @property string files API path for getting possible files
 * @property string test API path for test results
 * @property boolean any true if any API paths are specified
 * @property array extra Array of extra properties to add to API calls
 */
class CirsimAPI {
    /**
     * Property get magic method
     *
     * <b>Properties</b>
     * Property | Type | Description
     * -------- | ---- | -----------
     *
     * @param string $property Property name
     * @return mixed
     */
    public function __get($property) {
        switch($property) {
            case 'save':
                return $this->save;

            case 'load':
                return $this->load;

            case 'files':
                return $this->files;

            case 'test':
                return $this->test;

            case 'any':
                return $this->save !== null || $this->load !== null || $this->test !== null;

            case 'extra':
                return $this->extra;

            default:
                $trace = debug_backtrace();
                trigger_error(
                    'Undefined property ' . $property .
                    ' in ' . $trace[0]['file'] .
                    ' on line ' . $trace[0]['line'],
                    E_USER_NOTICE);
                return null;
        }
    }

    /**
     * Property set magic method
     *
     * <b>Properties</b>
     * Property | Type | Description
     * -------- | ---- | -----------
     * load | string | API path for loading files
     * files | string | API path for getting possible files
     * save | string | API path for saving files
     *
     * @param string $property Property name
     * @param mixed $value Value to set
     */
    public function __set($property, $value) {
        switch($property) {
            case 'save':
                $this->save = $value;
                break;

            case 'load':
                $this->load = $value;
                break;

            case 'files':
                $this->files = $value;
                break;

            case 'test':
                $this->test = $value;
                break;

            default:
                $trace = debug_backtrace();
                trigger_error(
                    'Undefined property ' . $property .
                    ' in ' . $trace[0]['file'] .
                    ' on line ' . $trace[0]['line'],
                    E_USER_NOTICE);
                break;
        }
    }

    public function addExtra($key, $value) {
        $this->extra[] = ['key'=>$key, 'value'=>$value];
    }

    private $save = null;
    private $load = null;
    private $files = null;
    private $test = null;
    private $extra = [];
}