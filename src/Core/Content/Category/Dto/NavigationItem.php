<?php declare(strict_types=1);

namespace Shopware\Core\Content\Category\Dto;

use Shopware\Core\Content\Media\Core\Dto\Media;
use Shopware\Core\Framework\Struct\Extendable;
use Shopware\Core\Framework\Struct\Struct;

class NavigationItem extends Struct
{
    use Extendable;

    /**
     * @param array{type: string, reference: string, target: bool, external: string} $link
     * @param array<string, mixed> $customFields
     * @param NavigationItem[] $children
     */
    public function __construct(
        public string $id,
        public string $parentId,
        public string $path,
        public string $name,
        public string $type,
        public array $link = [],
        public ?Media $media = null,
        public array $customFields = [],
        public array $children = []
    ) {
    }
}
