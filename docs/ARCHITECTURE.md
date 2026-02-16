# Venditio Architecture

Venditio is a Laravel package that exposes API-only ecommerce primitives. It is designed for headless use, with the host application providing auth, UI, and rendering.

## Goals

- API-first, headless ecommerce
- Stable, versioned public APIs
- Extensibility without leaking internal details
- Optional behavior with safe defaults

## Package Layout

- `src/Contracts`: public interfaces and contracts
- `src/Http`: controllers, requests, resources
- `src`: internal domain packages (models, enums, validations, factories, variant system)
- `src/Actions`: application-like operations isolated from controllers
- `src/Policies`: policy-based authorization hooks
- `src/Pipelines`: order/cart pipelines
- `src/Validations`: validation rule contracts

## Service Provider

`VenditioServiceProvider` is the primary entry point. It registers:

- Config file
- Routes (optional)
- Validation bindings
- Factory guessing
- Morph map

Routes are loaded only when `venditio.routes.api.enable` is true, and are versioned/prefixed via config.

## HTTP Layer

Controllers are thin and delegate to Actions and Pipelines.

- Form Requests handle validation and authorization hooks.
- API Resources define the response shape and field transformations.
- `Controller::applyBaseFilters()` provides common list filtering and pagination.

## Models

Models live under `src/Packages/*/Models`.

- All models are replaceable via config.
- `Product` supports variants via `parent_id` and the `product_configuration` pivot.
- `ProductType`, `ProductVariant`, and `ProductVariantOption` define variant axes and values.

## Actions

Actions encapsulate state changes and are reusable by controllers and services.
Key actions include:

- Product creation and updates
- Variant generation from option combinations
- Product type, variant, and option CRUD

## Discounts

Discount handling is pipeline-based and API-first:

- `DiscountCalculator` applies line-level discounts.
- `CartTotalDiscountCalculator` resolves cart/order-level discount codes.
- `DiscountUsageRecorder` persists usages in `discount_applications` and increments `discounts.uses`.

Discount rule fields are first-level columns on `discounts`:

- Scope and targeting: `apply_to_cart_total`, `apply_once_per_cart`, `discountable_type`, `discountable_id`
- Usage limits: `max_uses`, `max_uses_per_user`, `one_per_user`, `uses`
- Eligibility: `minimum_order_total`, `active`, `starts_at`, `ends_at`
- Effects: `type`, `value`, `free_shipping`

Rule evaluation is configurable from `config/venditio.php`:

- `venditio.discounts.rules` for line discounts
- `venditio.discounts.cart_total.rules` for cart/order discounts

Host apps can add/replace rule classes by implementing `DiscountRuleInterface`
and overriding those config arrays. This allows introducing custom discount
eligibility logic without changing package internals.

### Custom Rule Example

```php
namespace App\Discounts\Rules;

use Illuminate\Database\Eloquent\Model;
use PictaStudio\Venditio\Contracts\DiscountRuleInterface;
use PictaStudio\Venditio\Discounts\DiscountContext;
use PictaStudio\Venditio\Models\Discount;

class WeekendOnlyRule implements DiscountRuleInterface
{
    public function passes(Discount $discount, Model $line, DiscountContext $context): bool
    {
        return now()->isWeekend();
    }
}
```

Register it in host app config:

```php
'discounts' => [
    'rules' => [
        // keep existing rules or replace them entirely
        App\Discounts\Rules\WeekendOnlyRule::class,
    ],
],
```

## Validation

Validation is decoupled using contracts so host apps can override rule sources.

- Contract interfaces live in `Validations/Contracts/*ValidationRules`.
- Default implementations live in `src/Validations`.
- The service provider binds contract → implementation by reading `config('venditio.validations')`. Each key is the contract class, each value the implementation class.
- To override rules for a resource, publish the config and point that contract to your custom class. To disable validation for a resource, remove its entry from `venditio.validations`.

## Authorization

Authorization is optional and policy-based.

- Policies are registered if `venditio.policies.register` is true.
- Controllers call `authorizeIfConfigured()` to avoid enforcing auth by default.
- Requests use permissive defaults and defer to the host app.

## Variant System

The product variant system models variant axes and combinations:

- `product_types` define a product family.
- `product_variants` define axes like Color, Size.
- `product_variant_options` define values like Red, Blue, S, M.
- The base product is the parent row. Each combination generates a variant product with `parent_id` pointing to the base product.
- Variant products are linked to options via the `product_configuration` pivot.

Variant generation is handled by the `CreateProductVariants` action and uses:

- Option matrix to generate combinations
- Deduping against existing combinations
- Configurable naming and copy behavior

## Configuration

All behavior is controlled via `config/venditio.php`.
Important sections:

- `routes.api` for prefix, version, middleware, pagination, and wrapping
- `models` for model overrides
- `validations` for validation contract → implementation bindings (Form Request rules)
- `auth` for roles/permissions and optional root user
- `product` enums
- `product_variants` naming/copy behavior

## Testing

Tests use Orchestra Testbench and Pest.
Test focus areas:

- API responses and validation
- Variant generation logic
- Optional authorization behavior
- Service provider boot

For endpoint details and example payloads, see `docs/API.md`.
