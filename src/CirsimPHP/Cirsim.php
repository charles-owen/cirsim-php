<?php
/**
 * @file
 * PHP server-side support for Cirsim
 */

namespace CL\CirsimPHP;

/**
 * Server-side support for Cirsim
 *
 * @cond
 * @property string answer JSON for a question answer/solution
 * @property string appTag
 * @property mixed components Array of components to use
 * @property boolean export If true, export/import menu options are available (default=true)
 * @property boolean save If true, the save menu option is included
 * @property boolean staff If true, user is a staff member (default=false)
 * @property string tab
 * @property array tabs
 * @endcond
 */
class Cirsim {
    /**
     * Cirsim constructor.
     * Sets the default values.
     */
    public function __construct() {
        $this->reset();
    }

    /**
     * Reset the Cirsim object to no single, tests, or only options.
     */
    public function reset() {
        $this->tests = [];
        $this->appTag = null;
        $this->name = null;
        $this->tabs = [];
        $this->imports = [];
        $this->save = false;
        $this->options = [];
        $this->user = null;
        $this->components = null;
        $this->load = null;
        $this->answer = null;

        $this->api = new CirsimAPI();
    }


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
            case 'tests':
                return $this->tests;

            case 'appTag':
                return $this->appTag;

            case 'api':
                return $this->api;

            case 'name':
                return $this->name;

            case 'save':
                return $this->save;

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
     * answer | string | JSON answer to the problem
     * appTag | string | If set, value is used as appTag for the file system
     * components | array | Array of components to include
     * export | boolean | If true, export/import menu options are available (default=true)
     * save | boolean | If true, the save menu option is included (default=false)
     * staff | boolean | If true, current user is a staff member (default=false);
     * tab | string | Adds a single tab to the circuit
     * tabs | array | Adds an array of tabs to the circuit
     * tests | array | Array of Cirsim tests
     *
     * @param string $property Property name
     * @param mixed $value Value to set
     */
    public function __set($property, $value) {
        if(!$this->set($property, $value)) {
            $trace = debug_backtrace();
            trigger_error(
                'Undefined property ' . $property .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
                E_USER_NOTICE);
        }
    }

    /**
     * Property set direct function. Returns true if set was successful.
     * @param string $property Property name
     * @param mixed $value Value to set
     * @return bool true if a supported set value.
     */
    public function set($property, $value) {
        switch($property) {
            case 'answer':
                $this->answer = $value;
                break;

            case 'appTag':
                $this->appTag = $value;
                break;

            case 'components':
                $this->components = $value;
                break;

            case 'export':
                $this->export = $value;
                break;

            case 'save':
                $this->save = $value;
                break;

            case "tab":
                $this->tabs[] = $value;
                break;

            case "tabs":
                $this->tabs = $value;
                break;

            case 'tests':
                $this->tests = $value;
                break;

            case 'staff':
                $this->staff = $value;
                break;

            case 'load':
                $this->load = $value;
                break;

            default:
                return false;
        }

        return true;
    }


    /**
     * Calling this function puts Cirsim into single file mode.
     * Only one file can be saved in this mode.
     *
     * The application tag is an assignment tag if the submission is
     * limited to submission only during the assignment.
     *
     * @param string $appTag The application tag. Can be an assignment tag
     * @param string $name The name to use
     * @param boolean $save If true, the save option is included.
     * @param boolean $singleAjaxLoad If true, load the single file using Ajax
     */
    public function single($appTag, $name, $save = true, $singleAjaxLoad = true) {
        $this->appTag = $appTag;
        $this->name = $name;
        $this->save = $save;
        $this->singleAjaxLoad = $singleAjaxLoad;
    }

    /**
     * Add a menu option to import a tab from another file. This is
     * used to import tabs from one quiz to another.
     *
     * @param string $intoTab The tab we are importing into
     * @param string $appTag The assignment for the source file
     * @param string $name The name of the source file
     * @param string $fromTab The tab in the source file we are importing from.
     */
    public function tab_import($intoTab, $appTag, $name, $fromTab) {
        $this->imports[] = ["into"=>$intoTab,
            "name"=>$name,
            "from"=>$fromTab,
            'extra'=>[
                "appTag"=>$appTag,
            ]];
    }

    /**
     * Present the cirsim window in a view.
     * @param bool $full True for full screen (cirsim-full),
     * false for windowed (cirsim-window).
     * @param string $class Additional classes to add to the tag
     * @return string
     */
    public function present($full = false, $class=null) {
        $html = '';

//        if(!$full) {
//            $html .= '<div class="cl-cirsim-gap-before"></div>'
//                . $this->view->exitBody();
//        }
//
//        $site = $this->view->site;
//        if($site->installed('users')) {
//            $user = $site->users->user;
//        } else {
//            $user = null;
//        }

        $html .= $this->present_div($full, $class);

//        if(!$full) {
//            $html .= $this->view->reenterBody() .
//                '<div class="cl-cirsim-gap-after"></div>';
//        }

        return $html;
    }

    /**
     *
     * @param bool $full
     * @return string
     */

    /**
     * Present the cirsim div in a view.
     * @param bool $full True for full screen (cirsim-full),
     * false for windowed (cirsim-window).
     * @param string|null $class Optional class to add to the div
     * @return string HTML
     */
    public function present_div($full = false, $class=null) {
        $html = '';

        $data = [
            'display'=>$full ? 'full' : 'window'
        ];

        foreach($this->options as $option => $value) {
            $data[$option] = $value;
        }

        if(!$this->export) {
            // Import/export feature is not available
            $data['export'] = 'none';
        }

        // Optional answer
        if($this->answer !== null) {
            $data['loadMenu'] = [['name'=>'Load Solution', 'json'=>$this->answer]];
        }

        if($this->api->any) {
            // Filesystem dependent features
            $data['api'] = [
                'extra'=>[
                    'type'=>'application/json'
                ]
            ];

            foreach($this->api->extra as $extra) {
                $data['api']['extra'][$extra['key']] = $extra['value'];
            }

            if($this->name !== null) {
                //
                // Single-save mode
                //

                if($this->save && $this->api->save !== null) {
                    $data['api']['save'] = [
                        'url'=> $this->api->save, // $root . '/cl/api/filesystem/save',
                        'name'=>$this->name
                    ];

                    if($this->load === null && $this->singleAjaxLoad) {
                        // If the load path is set, this will load the file
                        // using Ajax
                        if($this->api->load !== null) {
                            $data['api']['open'] = [
                                'url'=> $this->api->load,
                                'name'=>$this->name
                            ];
                        }
                    }

                }

            } else {
                if($this->save) {
                    if($this->api->save !== null) {
                        $data['api']['save'] = [
                            'url'=> $this->api->save
                        ];
                    }

                    if($this->api->files !== null) {
                        $data['api']['files'] = [
                            'url'=> $this->api->files
                        ];
                    }

                    if($this->api->load !== null) {
                        $data['api']['open'] = [
                            'url'=> $this->api->load
                        ];
                    }
                }
            }

            if($this->appTag !== null) {
                $data['api']['extra']['appTag'] = $this->appTag;
            }

            if(count($this->imports) > 0 && $this->api->load !== null) {
                $data['imports'] = $this->imports;

                $data['api']['import'] = [
                    'url'=> $this->api->load
                ];
            }
        }

        if(count($this->tabs) > 0) {
            $data['tabs'] = $this->tabs;
        }

        $this->optional($data, 'components', $this->components);
        $this->optional($data, 'load', $this->load);


        //
        // Tests
        //
        $tests = [];
        foreach($this->tests as $test) {
            if($test['staff'] && !$this->staff) {
                continue;
            }

            $tests[] = base64_encode(json_encode($test));
        }

        if(count($tests) > 0) {
            $data['tests'] = $tests;
        }

        if(strlen($class) > 0) {
            $class = ' ' . $class;
        }

        $payload = htmlspecialchars(json_encode($data), ENT_NOQUOTES);
        $html .= '<div class="cirsim-install' . $class . '">' . $payload . '</div>';

        return $html;
    }

    private function optional(&$data, $name, $property) {
        if($property !== null) {
            $data[$name] = $property;
        }
    }

    /**
     * Present Cirsim in demo mode.
     *
     * Demo mode is an inline placement with simulation, but no editing
     * capabilities. It's used to demo circuit components on a page.
     * @param string $json JSON file to initially load
     * @param null $class Additional classes to add to the DIV
     * @return string HTML
     */
    public function present_demo($json, $class=null) {
        $class = $class !== null ? ' ' . $class : '';
        $data = [
            'display'=>'inline',
            'load'=>$json
        ];

        foreach($this->options as $option => $value) {
            $data[$option] = $value;
        }

        $payload = htmlspecialchars(json_encode($data), ENT_NOQUOTES);
        $html = '<div class="cirsim-install' . $class . '">' . $payload . '</div>';

        return $html;
    }


    /**
     * Add other Cirsim config options.
     * @param string $option Option name
     * @param mixed $value Value to set
     */
    public function option($option, $value) {
        $this->options[$option] = $value;
    }


    /**
     * Add a test
     * @param string $name Test name
     * @param array $input Array of input pin names
     * @param array $output Array of output pin names
     * @param array|callable $test Either: Array of tests, each an array of values
     *  -or- function to compute result
     * @param boolean $staff True if only staff members see this test
     */
    public function add_test($name, array $input, $output, $test, $staff=false) {
        if(is_callable($test)) {
            $test_func = $test;
            $test = array();

            $size = count($input);
            for($i=0; $i<pow(2, $size); $i++) {
                $row = array();
                for($c=0; $c<$size; $c++) {
                    $a = ($i >> ($size-$c-1)) & 1;
                    $row[] = $a;
                }

                $result = call_user_func_array($test_func, $row);
                if(is_array($result)) {
                    foreach($result as $r) {
                        $row[] = $r ? 1 : 0;
                    }
                } else {
                    $row[] = $result ? 1 : 0;
                }

                $test[] = $row;
            }
        }

        $this->tests[] = ['name' => $name,
            'input' => $input,
            'output' => $output,
            'test' => $test,
            'staff' => $staff];
    }

    private $appTag = null;
    private $name = null;
    private $tests = [];
    private $tabs = [];	        // Any additional tabs to add
    private $imports = [];	    // Any tab imports possible
    private $save;              // True if save support is added
    private $options;           // Other options to set
    private $user;              // User to view/save/etc.
    private $components;        // Components to use
    private $load;              // JSON to load
    private $answer = null;     // Any answer JSON
    private $staff = false;     // Is user a staff member?
    private $api;               // API features
    private $singleAjaxLoad = true; // Should we load single file using Ajax?

    private $export = true;     // True enabled export/import of cirsim files
}