<?php

namespace Helper;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Exception;
use InvalidArgumentException;
use IteratorAggregate;
use JsonSerializable;
use Stringable;
use Symfony\Component\Yaml\Yaml;
use Traversable;

class Magic implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable, Stringable
{
    /**
     * Build a Magic instance from JSON string.
     *
     * To load a JSON file use
     *
     * $magic = \Helper\Magic::fromJSON(file_get_contents('/path/to/file.json'));
     *
     * @link https://php.net/manual/en/function.json-decode.php
     *
     * @param string $input The JSON string being decoded. This function only works with UTF-8 encoded strings.
     * @param int    $depth User specified recursion depth.
     * @param int    $flags Bitmask of JSON decode options JSON_*
     */
    public static function fromJSON(string $input, int $depth = 512, int $flags = 0): Magic
    {
        // Decode always to an associative array as expected by constructor
        $input = json_decode($input, true, $depth, $flags);

        if (json_last_error() === JSON_ERROR_NONE) {
            return new static($input);
        }

        throw new InvalidArgumentException(json_last_error_msg(), 100);
    }

    /**
     * Build a Magic instance from YAML string.
     *
     * To load a YAML file use
     *
     * $magic = \Helper\Magic::fromYAML(file_get_contents('/path/to/file.yaml'));
     *
     * @throws \Symfony\Component\Yaml\Exception\ParseException If the YAML is not valid
     *
     * @param string $input The YAML string being parsed
     * @param int    $flags A bit field of \Symfony\Component\Yaml\Yaml::PARSE_* constants
     *                      to customize the YAML parser behavior
     */
    public static function fromYAML(string $input, int $flags = 0): Magic
    {
        return new static(Yaml::parse($input, $flags));
    }

    /**
     * Build a Magic instance from raw POST string.
     *
     * e.g. x=1&y=2
     *
     * @param string $input The string being parsed
     */
    public static function fromString(string $input): Magic
    {
        parse_str($input, $data);

        return new static($data);
    }

    /**
     * Build a Magic instance from an INI string.
     *
     * To load an INI file use
     *
     * $magic = \Helper\Magic::fromINI(file_get_contents('/path/to/file.ini'));
     *
     * @link https://www.php.net/manual/en/function.parse-ini-string.php
     *
     * @throws InvalidArgumentException If the INI is not valid
     *
     * @param string $input    The INI string being parsed
     * @param bool   $sections By setting the process_sections parameter to true, you get
     *                         a multidimensional array, with the section names and settings included.
     * @param int    $mode     Can either be INI_SCANNER_NORMAL (default) or INI_SCANNER_RAW.
     *                         If INI_SCANNER_RAW is supplied, then option values will not be parsed.
     */
    public static function fromINI(string $input, bool $sections = false, int $mode = INI_SCANNER_NORMAL): Magic
    {
        // Suppress syntax error output
        $input = @parse_ini_string($input, $sections, $mode);

        if ($input !== false) {
            return new static($input);
        }

        throw new InvalidArgumentException('Invalid INI string!', 101);
    }

    /**
     * Load data from a file, MUST be created with save() before!
     *
     * @throws InvalidArgumentException If file not exists, is empty or not deserializable
     */
    public static function fromFile(string $filename): Magic
    {
        if (!file_exists($filename)) {
            throw new InvalidArgumentException('File not found: ' . $filename, 1);
        }

        // Check for file marker
        $data  = file_get_contents($filename);
        $regex = '~^' . self::$marker . '~';

        if (!preg_match($regex, $data)) {
            throw new InvalidArgumentException('Invalid file content!', 2);
        }

        return new static(unserialize(trim(preg_replace($regex, '', $data))));
    }

    /**
     * Build a Magic instance from an array.
     */
    public function __construct(array $data = [])
    {
        $this->data = [];

        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }

        $this->bootstrap();
    }

    /**
     * Get a variable.
     */
    public function get(string $key, $default = null)
    {
        return $this->exists($key) ? $this->data[$key] : $default;
    }

    /**
     * Set a variable.
     *
     * @param string $key
     * @param mixed  $value If is an array, it will be also stored as a Magic
     */
    public function set(string $key, $value): Magic
    {
        $this->data[$key] = is_array($value) ? new static($value) : $value;

        return $this;
    }

    /**
     * Merge values to a variable.
     *
     * @throws Exception If $key exists and is not an Magic
     *
     * @param string $key
     * @param array  $values Will be stored also as an Magic
     */
    public function merge(string $key, array $values): Magic
    {
        $this->exists($key) || $this->set($key, new static());

        $value = $this->get($key);

        if (is_iterable($value)) {
            foreach ($values as $k => $v) {
                $value[$k] = $v;
            }

            return $this->set($key, $value);
        }

        throw new Exception('"' . $key . '" is not iterable!');
    }

    /**
     * Delete a variable.
     */
    public function delete(string $key): Magic
    {
        if ($this->exists($key)) {
            unset($this->data[$key]);
        }

        return $this;
    }

    /**
     * Clear all variables.
     */
    public function clear(): Magic
    {
        $this->data = [];

        return $this;
    }

    /**
     * Check if a variable is set.
     */
    public function exists(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Magic method for get.
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * Magic method for set.
     */
    public function __set(string $key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Magic method for delete.
     */
    public function __unset(string $key)
    {
        $this->delete($key);
    }

    /**
     * Magic method for exists.
     */
    public function __isset(string $key): bool
    {
        return $this->exists($key);
    }

    /**
     * Count elements of an object
     *
     * Implements \Countable
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Retrieve an external Iterator.
     *
     * Implements \IteratorAggregate
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }

    /**
     * Whether an array offset exists.
     *
     * Implements \ArrayAccess
     */
    public function offsetExists($offset): bool
    {
        return $this->exists($offset);
    }

    /**
     * Retrieve value by offset.
     *
     * Implements \ArrayAccess
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Set a value by offset.
     *
     * Implements \ArrayAccess
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Remove a value by offset.
     *
     * Implements \ArrayAccess
     */
    public function offsetUnset($offset): void
    {
        $this->delete($offset);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * Implements \JsonSerializable
     */
    public function jsonSerialize()
    {
        return $this->data;
    }

    /**
     * Gets a JSON string representation of the object
     *
     * Implements \Stringable
     */
    public function __toString(): string
    {
        return json_encode($this->data);
    }

    /**
     * Gets whole data as array
     */
    public function toArray(): array
    {
        return json_decode(json_encode($this->data), true);
    }

    /**
     * Save data serialized to a file, reload with load()
     *
     * @param  string  $filename
     * @return integer Bytes written
     */
    public function save(string $filename): int
    {
        return file_put_contents($filename, self::$marker . PHP_EOL . serialize($this->data));
    }

    // ----------------------------------------------------------------------
    // PROTECTED
    // ----------------------------------------------------------------------

    /**
     * Overwrite in derived classes if needed
     */
    protected function bootstrap()
    {
    }

    // ----------------------------------------------------------------------
    // PRIVATE
    // ----------------------------------------------------------------------

    /**
     * File content marker
     */
    private static $marker = 'Magic.serialized';

    /** @var array */
    private $data;
}
