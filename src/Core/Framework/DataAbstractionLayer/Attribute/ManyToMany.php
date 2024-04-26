<?php

namespace Shopware\Core\Framework\DataAbstractionLayer\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ManyToMany extends Field
{
    public function __construct(
        public string $entity,
        public string $mapping
    ) {
        parent::__construct(type: 'many-to-many');
    }
}
