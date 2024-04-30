<?php declare(strict_types=1);

namespace Shopware\Core\Content\Category\DataResolver\Element;

use Shopware\Core\Content\Category\SalesChannel\AbstractNavigationRoute;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\Feature;
use Symfony\Component\HttpFoundation\Request;

class CategoryNavigationCmsElementResolver extends AbstractCmsElementResolver
{
    public function __construct(private readonly AbstractNavigationRoute $route)
    {
    }

    public function getType(): string
    {
        return 'category-navigation';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        return null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        if (!Feature::isActive('cache_rework')) {
            return;
        }
        $slot->setData(
            $this->route->header(new Request(), $resolverContext->getSalesChannelContext())
        );
    }
}
