<?php

namespace Shopware\Core\Framework\DataAbstractionLayer\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Primary extends Field
{
    public function __construct(public string $type, public string $column = '')
    {
        parent::__construct(type: $type, column: $column);
    }
}
