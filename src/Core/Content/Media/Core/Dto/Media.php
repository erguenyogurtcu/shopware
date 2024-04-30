<?php declare(strict_types=1);

namespace Shopware\Core\Content\Media\Core\Dto;

use Shopware\Core\Framework\Struct\Struct;

class Media extends Struct
{
    /**
     * @param array{url: string, width: int, height: int}[] $thumbnails
     */
    public function __construct(
        public string $id,
        public string $url,
        public ?string $title,
        public ?string $alt,
        public array $thumbnails
    ) {
    }
}
