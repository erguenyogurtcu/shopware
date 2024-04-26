<?php

namespace Shopware\Core\System\Currency;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\Field;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\FieldType;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\ManyToMany;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\ManyToOne;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\OneToMany;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\OneToOne;
use Shopware\Core\Framework\DataAbstractionLayer\Attribute\Primary;
use Shopware\Core\Framework\DataAbstractionLayer\Entity as EntityStruct;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\Price;

#[Entity(name: 'my_entity')]
class MyEntity extends EntityStruct
{
    #[Primary(type: 'uuid')]
    public string $id;

    #[Field(type: FieldType::STRING)]
    public string $number;

    #[Field(type: FieldType::PRICE)]
    public ?Price $price = null;

    #[Field(type: FieldType::STRING, translated: true)]
    public ?string $name = null;

    #[Field(type: FieldType::TEXT, translated: true)]
    public ?string $description = null;

    #[Field(type: FieldType::INT, translated: true)]
    public ?int $position = null;

    #[Field(type: FieldType::FLOAT, translated: true)]
    public ?float $weight = null;

    #[Field(type: FieldType::BOOL, translated: true)]
    public ?bool $highlight = null;

    #[Field(type: FieldType::DATETIME, translated: true)]
    public ?\DateTimeImmutable $release = null;

//    #[OneToMany(entity: 'product', field: 'uuid', ref: 'my_entity_id')]
//    public array $products = [];

    #[Fk(entity: 'product', column: 'product_id')]
    public string $productId;

    #[Fk(entity: 'product', column: 'follow_id')]
    public ?string $followId;

    #[ManyToOne(entity: 'product', field: 'product_id', ref: 'id')]
    public ?ProductEntity $product = null;

    #[ManyToMany(entity: 'category', mapping: 'my_entity_categories')]
    public array $categories = [];

    #[OneToOne(entity: 'product', field: 'follow_id', ref: 'id')]
    public ?ProductEntity $follow = null;
}
