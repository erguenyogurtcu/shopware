<?php

namespace Shopware\Core\Framework\DataAbstractionLayer\Attribute;

enum FieldType: string
{
    public const STRING = 'string';
    public const TEXT = 'text';
    public const INT = 'int';
    public const FLOAT = 'float';
    public const BOOL = 'bool';
    public const DATETIME = 'datetime';
    public const PRICE = 'price';
    public const UUID = 'uuid';
}
