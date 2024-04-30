<?php declare(strict_types=1);

namespace Shopware\Core\System\SalesChannel;

use Shopware\Core\Checkout\Cart\CartException;
use Shopware\Core\Checkout\Cart\Delivery\Struct\ShippingLocation;
use Shopware\Core\Checkout\Cart\Tax\Struct\TaxRule;
use Shopware\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerGroup\CustomerGroupEntity;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\CashRoundingConfig;
use Shopware\Core\Framework\Feature;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Struct\StateAwareTrait;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\System\Currency\CurrencyEntity;
use Shopware\Core\System\SalesChannel\Exception\ContextPermissionsLockedException;
use Shopware\Core\System\Tax\Exception\TaxNotFoundException;
use Shopware\Core\System\Tax\TaxCollection;

#[Package('core')]
class SalesChannelContext extends Struct
{
    use StateAwareTrait;

    /**
     * Unique token for context, e.g. stored in session or provided in request headers
     *
     * @var string
     */
    protected $token;

    /**
     * used:
     *  .id
     *  .displayGross
     *
     * @var CustomerGroupEntity
     */
    protected $currentCustomerGroup;

    /**
     * used:
     *  .id (many many usages)
     *  .factor (many usages)
     *  .taxFreeFrom (Only in CountryTaxCalculator)
     *  .isSystemDefault (possible to handle in another way)
     *  .isoCode (Only in twig currency filter)
     *
     * @var CurrencyEntity
     */
    protected $currency;

    /**
     * usages:
     *  .id (delegate to salesChannelId)
     *
     *  # cart
     *  .taxCalculationType (AmountCalculator)
     *  .paymentMethodId (PaymentMethodCollection, BlockedPaymentMethodSwitcher)
     *  .paymentMethodIds (PaymentMethodValidator)
     *  .shippingMethodId (ShippingMethodCollection, BlockedShippingMethodSwitcher)
     *
     *  # navigation
     *  .navigationId (CategoryRoute, NavigationRoute, CategoryUrlProvider, ErrorController)
     *  .footerCategoryId (NavigationRoute, CategoryUrlProvider, FooterPageletLoader)
     *  .serviceCategoryId (NavigationRoute, CategoryUrlProvider, HeaderPageletLoader)
     *  .navigationCategoryDepth (HeaderPageletLoader)
     *
     *  # get rid of
     *  .name (CustomerAccountRecoverRequestEvent)
     *  .domains (RegisterRoute / SendPasswordRecoveryMailRoute / NewsletterSubscribeRoute / SitemapExporter / ContextSwitchRoute)
     *  .typeId (ProductExportPartialGenerationHandler)
     *
     *  # Storefront meta data
     *  .analyticsId (CookieController)
     *  .hreflangActive (HreflangLoader)
     *  .hreflangDefaultDomainId (HreflangLoader)
     *  .homeMetaDescription/homeMetaTitle/homeKeywords (NavigationPageLoader)
     *
     * @var SalesChannelEntity
     */
    protected $salesChannel;

    /**
     * # usages
     *  .* pricing product/cart/delivery etc
     *
     * @var TaxCollection
     */
    protected $taxRules;

    /**
     * @var CustomerEntity|null
     */
    protected $customer;

    /**
     * usages:
     *  .id (current payment method)
     *  .appPaymentMethod (PreparedPaymentService/AppPaymentHandler)
     *  .active
     *  .name (Blocked message)
     *
     * @var PaymentMethodEntity
     */
    protected $paymentMethod;

    /**
     * @var ShippingMethodEntity
     */
    protected $shippingMethod;

    /**
     * usages:
     *   .country.id
     *   .country.active
     *   .country.shippingAvailable
     *   .country.name (error message)
     *
     * @var ShippingLocation
     */
    protected $shippingLocation;

    /**
     * @var array<string, bool>
     */
    protected $permissions = [];

    /**
     * @var bool
     */
    protected $permisionsLocked = false;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @internal
     *
     * @param array<string, string[]> $areaRuleIds
     */
    public function __construct(
        Context $baseContext,
        string $token,
        private ?string $domainId,
        SalesChannelEntity $salesChannel,
        CurrencyEntity $currency,
        CustomerGroupEntity $currentCustomerGroup,
        TaxCollection $taxRules,
        PaymentMethodEntity $paymentMethod,
        ShippingMethodEntity $shippingMethod,
        ShippingLocation $shippingLocation,
        ?CustomerEntity $customer,
        protected CashRoundingConfig $itemRounding,
        protected CashRoundingConfig $totalRounding,
        /**
         * @deprecated tag:v6.7.0 - Context contains no more rule ids or area rule ids
         */
        protected array $areaRuleIds = []
    ) {
        $this->currentCustomerGroup = $currentCustomerGroup;
        $this->currency = $currency;
        $this->salesChannel = $salesChannel;
        $this->taxRules = $taxRules;
        $this->customer = $customer;
        $this->paymentMethod = $paymentMethod;
        $this->shippingMethod = $shippingMethod;
        $this->shippingLocation = $shippingLocation;
        $this->token = $token;
        $this->context = $baseContext;
    }

    public function getCurrentCustomerGroup(): CustomerGroupEntity
    {
        return $this->currentCustomerGroup;
    }

    public function getCurrency(): CurrencyEntity
    {
        return $this->currency;
    }

    public function getSalesChannel(): SalesChannelEntity
    {
        return $this->salesChannel;
    }

    public function getTaxRules(): TaxCollection
    {
        return $this->taxRules;
    }

    /**
     * Get the tax rules depend on the customer billing address
     * respectively the shippingLocation if there is no customer
     */
    public function buildTaxRules(string $taxId): TaxRuleCollection
    {
        $tax = $this->taxRules->get($taxId);

        if ($tax === null || $tax->getRules() === null) {
            throw new TaxNotFoundException($taxId);
        }

        if ($tax->getRules()->first() !== null) {
            // NEXT-21735 - This is covered randomly
            // @codeCoverageIgnoreStart
            return new TaxRuleCollection([
                new TaxRule($tax->getRules()->first()->getTaxRate(), 100),
            ]);
            // @codeCoverageIgnoreEnd
        }

        return new TaxRuleCollection([
            new TaxRule($tax->getTaxRate(), 100),
        ]);
    }

    public function getCustomer(): ?CustomerEntity
    {
        return $this->customer;
    }

    public function getPaymentMethod(): PaymentMethodEntity
    {
        return $this->paymentMethod;
    }

    public function getShippingMethod(): ShippingMethodEntity
    {
        return $this->shippingMethod;
    }

    public function getShippingLocation(): ShippingLocation
    {
        return $this->shippingLocation;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * @deprecated tag:v6.7.0 - #cache_rework_rule_reason#
     *
     * @return string[]
     */
    public function getRuleIds(): array
    {
        if (Feature::isActive('cache_rework')) {
            return [];
        }

        return $this->getContext()->getRuleIds();
    }

    /**
     * @param array<string> $ruleIds
     */
    public function setRuleIds(array $ruleIds): void
    {
        if (Feature::isActive('cache_rework')) {
            $this->getContext()->setRuleIds([]);

            return;
        }
        $this->getContext()->setRuleIds($ruleIds);
    }

    /**
     * @internal
     *
     * @return array<string, string[]>
     */
    public function getAreaRuleIds(): array
    {
        if (Feature::isActive('cache_rework')) {
            return [];
        }

        return $this->areaRuleIds;
    }

    /**
     * @internal
     *
     * @param string[] $areas
     *
     * @return string[]
     */
    public function getRuleIdsByAreas(array $areas): array
    {
        if (Feature::isActive('cache_rework')) {
            return [];
        }

        $ruleIds = [];

        foreach ($areas as $area) {
            if (empty($this->areaRuleIds[$area])) {
                continue;
            }

            $ruleIds = array_unique(array_merge($ruleIds, $this->areaRuleIds[$area]));
        }

        return array_values($ruleIds);
    }

    /**
     * @internal
     *
     * @param array<string, string[]> $areaRuleIds
     */
    public function setAreaRuleIds(array $areaRuleIds): void
    {
        $this->areaRuleIds = $areaRuleIds;
    }

    public function lockRules(): void
    {
        $this->getContext()->lockRules();
    }

    public function lockPermissions(): void
    {
        $this->permisionsLocked = true;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getTaxState(): string
    {
        return $this->context->getTaxState();
    }

    public function setTaxState(string $taxState): void
    {
        $this->context->setTaxState($taxState);
    }

    public function getTaxCalculationType(): string
    {
        return $this->getSalesChannel()->getTaxCalculationType();
    }

    /**
     * @return array<string, bool>
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @param array<string, bool> $permissions
     */
    public function setPermissions(array $permissions): void
    {
        if ($this->permisionsLocked) {
            throw new ContextPermissionsLockedException();
        }

        $this->permissions = array_filter($permissions);
    }

    public function getApiAlias(): string
    {
        return 'sales_channel_context';
    }

    public function hasPermission(string $permission): bool
    {
        return \array_key_exists($permission, $this->permissions) && $this->permissions[$permission];
    }

    public function getSalesChannelId(): string
    {
        return $this->getSalesChannel()->getId();
    }

    public function addState(string ...$states): void
    {
        $this->context->addState(...$states);
    }

    public function removeState(string $state): void
    {
        $this->context->removeState($state);
    }

    public function hasState(string ...$states): bool
    {
        return $this->context->hasState(...$states);
    }

    /**
     * @return string[]
     */
    public function getStates(): array
    {
        return $this->context->getStates();
    }

    public function getDomainId(): ?string
    {
        return $this->domainId;
    }

    public function setDomainId(?string $domainId): void
    {
        $this->domainId = $domainId;
    }

    /**
     * @return string[]
     */
    public function getLanguageIdChain(): array
    {
        return $this->context->getLanguageIdChain();
    }

    public function getLanguageId(): string
    {
        return $this->context->getLanguageId();
    }

    public function getVersionId(): string
    {
        return $this->context->getVersionId();
    }

    public function considerInheritance(): bool
    {
        return $this->context->considerInheritance();
    }

    public function getTotalRounding(): CashRoundingConfig
    {
        return $this->totalRounding;
    }

    public function setTotalRounding(CashRoundingConfig $totalRounding): void
    {
        $this->totalRounding = $totalRounding;
    }

    public function getItemRounding(): CashRoundingConfig
    {
        return $this->itemRounding;
    }

    public function setItemRounding(CashRoundingConfig $itemRounding): void
    {
        $this->itemRounding = $itemRounding;
    }

    public function getCurrencyId(): string
    {
        return $this->getCurrency()->getId();
    }

    public function ensureLoggedIn(bool $allowGuest = true): void
    {
        if ($this->customer === null) {
            throw CartException::customerNotLoggedIn();
        }

        if (!$allowGuest && $this->customer->getGuest()) {
            throw CartException::customerNotLoggedIn();
        }
    }

    public function getCustomerId(): ?string
    {
        return $this->customer?->getId();
    }

    /**
     * @template TReturn of mixed
     *
     * @param callable(SalesChannelContext): TReturn $callback
     *
     * @return TReturn the return value of the provided callback function
     */
    public function live(callable $callback): mixed
    {
        $before = $this->context;

        $this->context = $this->context->createWithVersionId(Defaults::LIVE_VERSION);

        $result = $callback($this);

        $this->context = $before;

        return $result;
    }

    public function getCustomerGroupId(): string
    {
        return $this->currentCustomerGroup->getId();
    }
}
