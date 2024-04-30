<?php declare(strict_types=1);

namespace Shopware\Storefront\Pagelet;

use Shopware\Core\Content\Category\Dto\Navigation;
use Shopware\Core\Content\Category\Tree\Tree;
use Shopware\Core\Framework\Log\Package;

#[Package('storefront')]
abstract class NavigationPagelet extends Pagelet
{
    /**
     * @var Tree|Navigation|null
     */
    protected $navigation;

    public function __construct(null|Tree|Navigation $navigation)
    {
        $this->navigation = $navigation;
    }

    public function getNavigation(): null|Tree|Navigation
    {
        return $this->navigation;
    }
}
