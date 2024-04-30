<?php declare(strict_types=1);

namespace Shopware\Storefront\Page;

use Shopware\Core\Checkout\Payment\PaymentMethodCollection;
use Shopware\Core\Checkout\Shipping\ShippingMethodCollection;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Storefront\Pagelet\Footer\FooterPagelet;
use Shopware\Storefront\Pagelet\Header\HeaderPagelet;

#[Package('storefront')]
class Page extends Struct
{
    /**
     * @deprecated tag:v6.7.0 - Will be removed, header is loaded via esi and will be rendered in an separate request
     *
     * @var HeaderPagelet|null
     */
    protected $header;

    /**
     * @deprecated tag:v6.7.0 - Will be removed, footer is loaded via esi and will be rendered in an separate request
     *
     * @var FooterPagelet|null
     */
    protected $footer;

    /**
     * @deprecated tag:v6.7.0 - Will be removed, no more needed
     *
     * @var ShippingMethodCollection
     */
    protected $salesChannelShippingMethods;

    /**
     * @deprecated tag:v6.7.0 - Will be removed, no more needed
     *
     * @var PaymentMethodCollection
     */
    protected $salesChannelPaymentMethods;

    /**
     * @var MetaInformation
     */
    protected $metaInformation;

    /**
     * @deprecated tag:v6.7.0 - Will be removed, header is loaded via esi and will be rendered in an separate request
     */
    public function getHeader(): ?HeaderPagelet
    {
        return $this->header;
    }

    /**
     * @deprecated tag:v6.7.0 - Will be removed, header is loaded via esi and will be rendered in an separate request
     */
    public function setHeader(?HeaderPagelet $header): void
    {
        $this->header = $header;
    }

    /**
     * @deprecated tag:v6.7.0 - Will be removed, footer is loaded via esi and will be rendered in an separate request
     */
    public function getFooter(): ?FooterPagelet
    {
        return $this->footer;
    }

    /**
     * @deprecated tag:v6.7.0 - Will be removed, footer is loaded via esi and will be rendered in an separate request
     */
    public function setFooter(?FooterPagelet $footer): void
    {
        $this->footer = $footer;
    }

    /**
     * @deprecated tag:v6.7.0 - Will be removed, no more needed
     */
    public function getSalesChannelShippingMethods(): ?ShippingMethodCollection
    {
        return $this->salesChannelShippingMethods;
    }

    /**
     * @deprecated tag:v6.7.0 - Will be removed, no more needed
     */
    public function setSalesChannelShippingMethods(ShippingMethodCollection $salesChannelShippingMethods): void
    {
        $this->salesChannelShippingMethods = $salesChannelShippingMethods;
    }

    /**
     * @deprecated tag:v6.7.0 - Will be removed, no more needed
     */
    public function getSalesChannelPaymentMethods(): ?PaymentMethodCollection
    {
        return $this->salesChannelPaymentMethods;
    }

    /**
     * @deprecated tag:v6.7.0 - Will be removed, no more needed
     */
    public function setSalesChannelPaymentMethods(PaymentMethodCollection $salesChannelPaymentMethods): void
    {
        $this->salesChannelPaymentMethods = $salesChannelPaymentMethods;
    }

    public function getMetaInformation(): ?MetaInformation
    {
        return $this->metaInformation;
    }

    public function setMetaInformation(MetaInformation $metaInformation): void
    {
        $this->metaInformation = $metaInformation;
    }
}
