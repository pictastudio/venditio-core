# Venditio Core Architecture

Venditio Core is a Laravel package that exposes API-only ecommerce primitives. It is designed for headless use, with the host application providing auth, UI, and rendering.

## Goals

- API-first, headless ecommerce
- Stable, versioned public APIs
- Extensibility without leaking internal details
- Optional behavior with safe defaults

## Package Layout

- `src/Contracts`: public interfaces and contracts
- `src/Http`: controllers, requests, resources
- `src/Packages`: internal domain packages (core models, enums, validations, factories, variant system)
- `src/Actions`: application-like operations isolated from controllers
- `src/Policies`: policy-based authorization hooks
- `src/Pipelines`: order/cart pipelines
- `src/Validations`: validation rule contracts

## Service Provider

`VenditioCoreServiceProvider` is the primary entry point. It registers:

- Config file
- Routes (optional)
- Validation bindings
- Factory guessing
- Morph map

Routes are loaded only when `venditio-core.routes.api.enable` is true, and are versioned/prefixed via config.

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

## Validation

Validation is decoupled using contracts so host apps can override rule sources.

- `Validations/Contracts/*ValidationRules`
- Implementations live in `src/Packages/*/Validations`

## Authorization

Authorization is optional and policy-based.

- Policies are registered if `venditio-core.policies.register` is true.
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

All behavior is controlled via `config/venditio-core.php`.
Important sections:

- `routes.api` for prefix, version, middleware, pagination, and wrapping
- `models` for model overrides
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
