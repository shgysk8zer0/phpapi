<?php
/**
 * @author Chris Zuber <shgysk8zer0@gmail.com>
 * @package shgysk8zer0\PHPAPI
 * @version 1.0.0
 * @copyright 2020, Chris Zuber
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Interfaces\{ConsoleInterface};
use \shgysk8zer0\PHPAPI\Traits\{Singleton};
use \ReflectionProperty;
use \ReflectionClass;
use \ReflectionException;
use \RuntimeException;

final class Console implements ConsoleInterface
{
	use Singleton;

    /**
     * @var string
     */
    public const VERSION = '4.1.0';

    /**
     * @var string
     */
    public const HEADER_NAME = 'X-ChromeLogger-Data';

    /**
     * @var string
     */
    protected const BACKTRACE_LEVEL = 'backtrace_level';

    /**
     * @var string
     */
    protected const LOG = 'log';

    /**
     * @var string
     */
    protected const WARN = 'warn';

    /**
     * @var string
     */
    protected const ERROR = 'error';

    /**
     * @var string
     */
    protected const GROUP = 'group';

    /**
     * @var string
     */
    protected const INFO = 'info';

    /**
     * @var string
     */
    protected const GROUP_END = 'groupEnd';

    /**
     * @var string
     */
    protected const GROUP_COLLAPSED = 'groupCollapsed';

    /**
     * @var string
     */
    protected const TABLE = 'table';

    /**
     * @var string
     */
    protected $_php_version = PHP_VERSION;

    /**
     * @var int
     */
    protected $_timestamp;

    /**
     * @var array
     */
    protected $_json = [
        'version' => self::VERSION,
        'columns' => array('log', 'backtrace', 'type'),
        'rows' => []
    ];

    /**
     * @var array
     */
    protected $_backtraces = [];

    /**
     * @var bool
     */
    protected $_error_triggered = false;

    /**
     * @var array
     */
    protected $_settings = [
        self::BACKTRACE_LEVEL => 1
    ];

    /**
     * Prevent recursion when working with objects referring to each other
     *
     * @var array
     */
    protected $_processed = [];

    /**
     * constructor
     */
    private function __construct()
    {
        $this->_timestamp = $_SERVER['REQUEST_TIME'];
        $this->_json['request_uri'] = $_SERVER['REQUEST_URI'];
    }

    /**
     * logs a variable to the console
     *
     * @param mixed $data,... unlimited OPTIONAL number of additional logs [...]
     * @return void
     */
    public static function log(...$args): void
    {
    	// @TODO Check why this isn't self::LOG
        self::_log('', $args);
    }

    /**
     * logs a warning to the console
     *
     * @param mixed $data,... unlimited OPTIONAL number of additional logs [...]
     * @return void
     */
    public static function warn(...$args): void
    {
        self::_log(self::WARN, $args);
    }

    /**
     * logs an error to the console
     *
     * @param mixed $data,... unlimited OPTIONAL number of additional logs [...]
     * @return void
     */
    public static function error(...$args): void
    {
        self::_log(self::ERROR, $args);
    }

    /**
     * sends a group log
     *
     * @param string value
     */
    public static function group(...$args): void
    {
        self::_log(self::GROUP, $args);
    }

    /**
     * sends an info log
     *
     * @param mixed $data,... unlimited OPTIONAL number of additional logs [...]
     * @return void
     */
    public static function info(...$args): void
    {
        self::_log(self::INFO, $args);
    }

    /**
     * sends a collapsed group log
     *
     * @param string value
     */
    public static function groupCollapsed(...$args): void
    {
        self::_log(self::GROUP_COLLAPSED, $args);
    }

    /**
     * ends a group log
     *
     * @param string value
     */
    public static function groupEnd(...$args): void
    {
        self::_log(self::GROUP_END, $args);
    }

    /**
     * sends a table log
     *
     * @param string value
     */
    public static function table(...$args): void
    {
        self::_log(self::TABLE, $args);
    }

    /**
     * internal logging call
     *
     * @param string $type
     * @return void
     */
    protected static function _log(string $type, array $args): void
    {
        // nothing passed in, don't do anything
        if (count($args) == 0 && $type != self::GROUP_END) {
            return;
        }

        $logger = self::getInstance();

        $logger->_processed = [];

        $logs = [];

        foreach ($args as $arg) {
            $logs[] = $logger->_convert($arg);
        }

        $backtrace = debug_backtrace(false);
        $level = $logger->getSetting(self::BACKTRACE_LEVEL);

        if (isset($backtrace[$level]['file']) && isset($backtrace[$level]['line'])) {
            $backtrace_message = $backtrace[$level]['file'] . ' : ' . $backtrace[$level]['line'];
        } else {
        	$backtrace_message = 'unknown';
        }

        $logger->_addRow($logs, $backtrace_message, $type);
    }

    /**
     * converts an object to a better format for logging
     *
     * @param Object
     * @return array
     */
    protected function _convert($object)
    {
        // if this isn't an object then just return it
        if (!is_object($object)) {
            return $object;
        }

        //Mark this object as processed so we don't convert it twice and it
        //Also avoid recursion when objects refer to each other
        $this->_processed[] = $object;

        $object_as_array = [];

        // first add the class name
        $object_as_array['___class_name'] = get_class($object);

        // loop through object vars
        $object_vars = get_object_vars($object);

        foreach ($object_vars as $key => $value) {
            // same instance as parent object
            if ($value === $object || in_array($value, $this->_processed, true)) {
                $value = 'recursion - parent object [' . get_class($value) . ']';
            }
            $object_as_array[$key] = $this->_convert($value);
        }

        $reflection = new ReflectionClass($object);

        // loop through the properties and add those
        foreach ($reflection->getProperties() as $property) {

            // if one of these properties was already added above then ignore it
            if (array_key_exists($property->getName(), $object_vars)) {
                continue;
            }
            $type = $this->_getPropertyKey($property);

            if ($this->_php_version >= 5.3) {
                $property->setAccessible(true);
            }

            try {
                $value = $property->getValue($object);
            } catch (ReflectionException $e) {
                $value = 'only PHP 5.3 can access private/protected properties';
            }

            // same instance as parent object
            if ($value === $object || in_array($value, $this->_processed, true)) {
                $value = 'recursion - parent object [' . get_class($value) . ']';
            }

            $object_as_array[$type] = $this->_convert($value);
        }
        return $object_as_array;
    }

    /**
     * takes a reflection property and returns a nicely formatted key of the property name
     *
     * @param ReflectionProperty
     * @return string
     */
    protected function _getPropertyKey(ReflectionProperty $property)
    {
        $static = $property->isStatic() ? ' static' : '';

        if ($property->isPublic()) {
            return 'public' . $static . ' ' . $property->getName();
        } elseif ($property->isProtected()) {
            return 'protected' . $static . ' ' . $property->getName();
        } elseif ($property->isPrivate()) {
            return 'private' . $static . ' ' . $property->getName();
        }
    }

    /**
     * adds a value to the data array
     *
     * @var mixed
     * @return void
     */
    protected function _addRow(array $logs, $backtrace, string $type): void
    {
        // if this is logged on the same line for example in a loop, set it to null to save space
        if (in_array($backtrace, $this->_backtraces)) {
            $backtrace = null;
        }

        // for group, groupEnd, and groupCollapsed
        // take out the backtrace since it is not useful
        if ($type == self::GROUP || $type == self::GROUP_END || $type == self::GROUP_COLLAPSED) {
            $backtrace = null;
        }

        if ($backtrace !== null) {
            $this->_backtraces[] = $backtrace;
        }

        $row = [$logs, $backtrace, $type];

        $this->_json['rows'][] = $row;
        $this->_writeHeader($this->_json);
    }

    protected function _writeHeader($data): void
    {
    	if (headers_sent($file, $line)) {
    		throw new RuntimeException("Headers sent at {$file}:{$line}");
    	} else {
		    header(self::HEADER_NAME . ': ' . $this->_encode($data));
    	}
    }

    /**
     * encodes the data to be sent along with the request
     *
     * @param array $data
     * @return string
     */
    protected function _encode(array $data): string
    {
        return base64_encode(utf8_encode(json_encode($data)));
    }

    /**
     * adds a setting
     *
     * @param string key
     * @param mixed value
     * @return void
     */
    public function addSetting(string $key, $value): void
    {
        $this->_settings[$key] = $value;
    }

    /**
     * add ability to set multiple settings in one call
     *
     * @param array $settings
     * @return void
     */
    public function addSettings(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $this->addSetting($key, $value);
        }
    }

    /**
     * gets a setting
     *
     * @param string key
     * @return mixed
     */
    public function getSetting(string $key)
    {
        if (!isset($this->_settings[$key])) {
            return null;
        }
        return $this->_settings[$key];
    }
}

