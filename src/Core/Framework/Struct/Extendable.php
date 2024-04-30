<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Struct;

trait Extendable
{
    private array $_data = [];

    public function set(string $name, mixed $value): void
    {
        $this->_data[$name] = $value;
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->_data[$name] ?? $default;
    }

    public function getString(string $name, ?string $default = null): ?string
    {
        return $this->get($name, $default);
    }

    public function getArray(string $name, ?array $default = null): ?array
    {
        return $this->get($name, $default);
    }

    public function getFloat(string $name, ?float $default = null): ?float
    {
        return $this->get($name, $default);
    }

    public function getInt(string $name, ?int $default = null): ?int
    {
        return $this->get($name, $default);
    }

    public function getBool(string $name, ?bool $default = null): ?bool
    {
        return $this->get($name, $default);
    }
}
