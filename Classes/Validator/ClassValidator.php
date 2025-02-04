<?php

declare(strict_types=1);

namespace Mirko\T3maker\Validator;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Bundle\MakerBundle\Str;

final class ClassValidator
{
    public const RESERVED_WORDS = ['__halt_compiler', 'abstract', 'and', 'array',
        'as', 'break', 'callable', 'case', 'catch', 'class',
        'clone', 'const', 'continue', 'declare', 'default', 'die', 'do',
        'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor',
        'endforeach', 'endif', 'endswitch', 'endwhile', 'eval',
        'exit', 'extends', 'final', 'finally', 'for', 'foreach', 'function',
        'global', 'goto', 'if', 'implements', 'include',
        'include_once', 'instanceof', 'insteadof', 'interface', 'isset',
        'list', 'namespace', 'new', 'or', 'print', 'private',
        'protected', 'public', 'require', 'require_once', 'return',
        'static', 'switch', 'throw', 'trait', 'try', 'unset',
        'use', 'var', 'while', 'xor', 'yield',
        'int', 'float', 'bool', 'string', 'true', 'false', 'null', 'void',
        'iterable', 'object', '__file__', '__line__', '__dir__', '__function__', '__class__',
        '__method__', '__namespace__', '__trait__', 'self', 'parent',
    ];
    public const RESERVED_WORDS_FOR_PROPERTIES = [
        'id', 'createdAt', 'updatedAt',
        'version', 'deletedAt', 'uid',
        'pid', 'tstamp', 'crdate',
        'cruser_id', 'deleted', 'hidden',
        'starttime', 'endtime',
    ];

    /**
     * @param string $className
     * @param string $errorMessage
     *
     * @throws RuntimeException
     *
     * @return string
     */
    public static function validateClassName(string $className, string $errorMessage = ''): string
    {
        // Remove potential opening slash, so we don't match on it.
        $pieces = explode('\\', ltrim($className, '\\'));
        $shortClassName = Str::getShortClassName($className);

        foreach ($pieces as $piece) {
            if (!mb_check_encoding($piece, 'UTF-8')) {
                $errorMessage = $errorMessage ?: sprintf('"%s" is not a UTF-8-encoded string.', $piece);

                throw new RuntimeException($errorMessage);
            }

            if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $piece)) {
                $errorMessage = $errorMessage ?: sprintf(
                    '"%s" is not valid as a PHP class name (it must start with a letter or underscore,
                    followed by any number of letters, numbers, or underscores)',
                    $className
                );

                throw new RuntimeException($errorMessage);
            }

            if (\in_array(strtolower($shortClassName), self::RESERVED_WORDS, true)) {
                throw new RuntimeException(
                    sprintf('"%s" is a reserved keyword and thus cannot be used as class name in PHP.', $shortClassName)
                );
            }
        }

        // return original class name
        return $className;
    }

    public static function notEmpty(string $value = null): string
    {
        if ($value === null || $value === '') {
            throw new RuntimeException('This value cannot be empty.');
        }

        return $value;
    }

    public static function validateDoctrineFieldName(string $name): string
    {
        $reservedKeywords = self::RESERVED_WORDS_FOR_PROPERTIES;

        if (\in_array(strtolower($name), $reservedKeywords, true)) {
            throw new RuntimeException(
                sprintf('"%s" is a reserved keyword and thus cannot be used as property name.', $name)
            );
        }
        self::validatePropertyName($name);

        return $name;
    }

    public static function validatePropertyName(string $name): string
    {
        // check for valid PHP variable name
        if (!Str::isValidPhpVariableName($name)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid PHP property name.', $name));
        }

        return $name;
    }

    public static function validateLength($length)
    {
        if (!$length) {
            return $length;
        }

        $result = filter_var(
            $length,
            \FILTER_VALIDATE_INT,
            [
                'options' => ['min_range' => 1],
            ]
        );

        if ($result === false) {
            throw new RuntimeException(sprintf('Invalid length "%s".', $length));
        }

        return $result;
    }

    public static function validatePrecision($precision)
    {
        if (!$precision) {
            return $precision;
        }

        $result = filter_var(
            $precision,
            \FILTER_VALIDATE_INT,
            [
                'options' => ['min_range' => 1, 'max_range' => 65],
            ]
        );

        if ($result === false) {
            throw new RuntimeException(sprintf('Invalid precision "%s".', $precision));
        }

        return $result;
    }

    public static function validateScale($scale)
    {
        if (!$scale) {
            return $scale;
        }

        $result = filter_var(
            $scale,
            \FILTER_VALIDATE_INT,
            [
                'options' => ['min_range' => 0, 'max_range' => 30],
            ]
        );

        if ($result === false) {
            throw new RuntimeException(sprintf('Invalid scale "%s".', $scale));
        }

        return $result;
    }

    public static function validateBoolean($value)
    {
        if ($value == 'yes') {
            return true;
        }

        if ($value == 'no') {
            return false;
        }
        $valueAsBool = filter_var($value, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE);
        if ($valueAsBool === null) {
            throw new RuntimeException(sprintf('Invalid bool value "%s".', $value));
        }

        return $valueAsBool;
    }

    public static function notBlank(string $value = null): string
    {
        if ($value === null || $value === '') {
            throw new RuntimeException('This value cannot be blank.');
        }

        return $value;
    }
}
