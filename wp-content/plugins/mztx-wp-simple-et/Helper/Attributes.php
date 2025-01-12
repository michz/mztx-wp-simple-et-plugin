<?php

declare(strict_types=1);

namespace mztx\wp\plugin\SimpleEt\Helper;

use function array_filter;
use function array_key_exists;
use function explode;
use function in_array;
use function strtolower;

readonly class Attributes
{
    /**
     * @param array<array-key, string> $attributes
     */
    public function __construct(
        private array $attributes = [],
    ) {}

    /**
     * @param array<array-key, string> $allowedValues
     */
    public function getFromEnumLowercase(
        string $key,
        array $allowedValues,
        ?string $default,
    ): ?string {
        $key = strtolower($key);
        if (false === array_key_exists($key, $this->attributes)) {
            return $default;
        }

        $value = strtolower($this->attributes[$key]);
        if (false === in_array($value, $allowedValues)) {
            return $default;
        }

        return $value;
    }

    public function getInt(
        string $key,
        ?int $default,
    ): ?int {
        $key = strtolower($key);
        if (false === array_key_exists($key, $this->attributes)) {
            return $default;
        }

        return (int) $this->attributes[$key];
    }

    public function getString(
        string $key,
        string $default = '',
    ): string {
        $key = strtolower($key);
        if (false === array_key_exists($key, $this->attributes)) {
            return $default;
        }

        return $this->attributes[$key];
    }

    /**
     * @return array<array-key, string>
     */
    public function getStringArray(
        string $key,
        string $separator = ',',
    ): array {
        if ($separator === '') {
            $separator = ',';
        }

        $rawValue = $this->getString($key, '');
        return array_filter(explode($separator, $rawValue));
    }
}
