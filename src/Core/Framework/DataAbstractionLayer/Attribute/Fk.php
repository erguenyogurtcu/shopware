<?php

namespace Shopware\Core\Framework\DataAbstractionLayer\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Fk
{
    public bool $nullable;

    public function __construct(public string $column = '') {}
}
