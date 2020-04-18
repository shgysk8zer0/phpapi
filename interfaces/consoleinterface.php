<?php
namespace shgysk8zer0\PHPAPI\Interfaces;

interface ConsoleInterface
{
	public static function log(...$args): void;

    /**
     * logs a warning to the console
     *
     * @param mixed $data,... unlimited OPTIONAL number of additional logs [...]
     * @return void
     */
    public static function warn(...$args): void;

    /**
     * logs an error to the console
     *
     * @param mixed $data,... unlimited OPTIONAL number of additional logs [...]
     * @return void
     */
    public static function error(...$args): void;

    /**
     * sends a group log
     *
     * @param string value
     */
    public static function group(...$args): void;

    /**
     * sends an info log
     *
     * @param mixed $data,... unlimited OPTIONAL number of additional logs [...]
     * @return void
     */
    public static function info(...$args): void;

    /**
     * sends a collapsed group log
     *
     * @param string value
     */
    public static function groupCollapsed(...$args): void;

    /**
     * ends a group log
     *
     * @param string value
     */
    public static function groupEnd(...$args): void;

    /**
     * sends a table log
     *
     * @param string value
     */
    public static function table(...$args): void;

    /**
     * adds a setting
     *
     * @param string key
     * @param mixed value
     * @return void
     */
    public function addSetting(string $key, $value): void;

    /**
     * add ability to set multiple settings in one call
     *
     * @param array $settings
     * @return void
     */
    public function addSettings(array $settings): void;

    /**
     * gets a setting
     *
     * @param string key
     * @return mixed
     */
    public function getSetting(string $key);
}
