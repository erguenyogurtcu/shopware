<?php declare(strict_types=1);

namespace Shopware\Core\Content\Category\Service;

use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Category\Dto\NavigationItem;
use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandlerInterface;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

#[Package('inventory')]
class CategoryUrlGenerator extends AbstractCategoryUrlGenerator
{
    /**
     * @internal
     */
    public function __construct(private readonly SeoUrlPlaceholderHandlerInterface $seoUrlReplacer)
    {
    }

    public function getDecorated(): AbstractCategoryUrlGenerator
    {
        throw new DecorationPatternException(self::class);
    }

    public function generate(CategoryEntity|NavigationItem $category, ?SalesChannelEntity $salesChannel): ?string
    {
        $type = $category instanceof NavigationItem ? $category->type : $category->getType();
        $id = $category instanceof NavigationItem ? $category->id : $category->getId();

        if ($type === CategoryDefinition::TYPE_FOLDER) {
            return null;
        }

        if ($type !== CategoryDefinition::TYPE_LINK) {
            return $this->seoUrlReplacer->generate('frontend.navigation.page', ['navigationId' => $id]);
        }

        $linkType = $category instanceof NavigationItem ? $category->link['type'] : $category->getTranslation('linkType');
        $reference = $category instanceof NavigationItem ? $category->link['reference'] : $category->getTranslation('reference');
        $external = $category instanceof NavigationItem ? $category->link['external'] : $category->getTranslation('externalLink');

        if (!$reference && $linkType && $linkType !== CategoryDefinition::LINK_TYPE_EXTERNAL) {
            return null;
        }

        switch ($linkType) {
            case CategoryDefinition::LINK_TYPE_PRODUCT:
                return $this->seoUrlReplacer->generate('frontend.detail.page', ['productId' => $reference]);

            case CategoryDefinition::LINK_TYPE_CATEGORY:
                if ($salesChannel !== null && $reference === $salesChannel->getNavigationCategoryId()) {
                    return $this->seoUrlReplacer->generate('frontend.home.page');
                }

                return $this->seoUrlReplacer->generate('frontend.navigation.page', ['navigationId' => $reference]);

            case CategoryDefinition::LINK_TYPE_LANDING_PAGE:
                return $this->seoUrlReplacer->generate('frontend.landing.page', ['landingPageId' => $reference]);

            case CategoryDefinition::LINK_TYPE_EXTERNAL:
            default:
                return $external;
        }
    }
}
