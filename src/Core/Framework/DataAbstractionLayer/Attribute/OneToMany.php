<?php

namespace Shopware\Core\Framework\DataAbstractionLayer\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class OneToMany extends Field
{
    public function __construct(
        public string $entity,
        public string $field,
        public string $ref
    ) {
        parent::__construct(type: 'one-to-many');
    }
}
