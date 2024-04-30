<?php declare(strict_types=1);

namespace Shopware\Core\Content\Category\Dto;

use Shopware\Core\Framework\Struct\Extendable;
use Shopware\Core\Framework\Struct\Struct;

class Navigation extends Struct
{
    use Extendable;

    /**
     * @param array<NavigationItem> $items
     */
    public function __construct(public string $root, public array $items)
    {
    }
}
