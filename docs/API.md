# Venditio Core API Reference

This document describes the HTTP APIs exposed by Venditio Core. All routes are optional and versioned.

## Base URL and Versioning
Default base path is:
- `/venditio/api/v1`

Configure via:
- `venditio-core.routes.api.v1.prefix`

## Auth and Middleware
- No authentication is enforced by default.
- Middleware is configurable via `venditio-core.routes.api.v1.middleware`.
- Policy checks are optional and enabled by `venditio-core.policies.register`.

## Common Query Parameters
The following filters apply to most index endpoints via `applyBaseFilters`.
- `all` boolean, when true returns all records without pagination
- `id[]` array of ids to filter by
- `per_page` page size for pagination

Some endpoints add extra filters.
- `product_type_id` for `/product_variants`
- `product_variant_id` for `/product_variant_options`
- `as_tree` boolean for `/product_categories` (returns nested categories with `children`; forces full, non-paginated result)

## Response Shape
- JSON responses use API Resources.
- By default, timestamps are excluded unless `venditio-core.routes.api.include_timestamps` is true.
- Validation errors return Laravel's standard 422 response with error details.

## Resources
### Products
- `GET /products`
- `GET /products/{product}`
- `POST /products`
- `PATCH /products/{product}`
- `DELETE /products/{product}`

Payload notes:
- `category_ids` can be used to attach categories on create or update.
- `product_type_id` is required for variant generation.
- `measuring_unit` and `qty_for_unit` (optional) define unit of measure and quantity per unit (e.g. box of 6).

### Product Categories
- `GET /product_categories`
- `GET /product_categories/{product_category}`
- `POST /product_categories`
- `PATCH /product_categories/{product_category}`
- `DELETE /product_categories/{product_category}`

### Brands
- `GET /brands`
- `GET /brands/{brand}`
- `POST /brands`
- `PATCH /brands/{brand}`
- `DELETE /brands/{brand}`

### Addresses
- `GET /addresses`
- `GET /addresses/{address}`
- `POST /addresses`
- `PATCH /addresses/{address}`
- `DELETE /addresses/{address}`

### Carts
- `GET /carts`
- `GET /carts/{cart}`
- `POST /carts`
- `PATCH /carts/{cart}`
- `DELETE /carts/{cart}`
- `POST /carts/{cart}/add_lines`
- `PATCH /carts/{cart}/update_lines`

### Orders
- `GET /orders`
- `GET /orders/{order}`
- `POST /orders`
- `PATCH /orders/{order}`

### Discounts
- `GET /discounts`
- `GET /discounts/{discount}`
- `POST /discounts`
- `PATCH /discounts/{discount}`
- `DELETE /discounts/{discount}`

Discounts expose first-level rule columns:
- `type`: `percentage` or `fixed`
- `value`: discount amount
- `code`: unique code
- `active`, `starts_at`, `ends_at`: activation window
- `max_uses`: max global usages
- `max_uses_per_user`: max usages per user
- `one_per_user`: shorthand for one usage per user
- `discountable_type` + `discountable_id`: optional polymorphic target (for a user-specific discount use `discountable_type: "user"` and that user id)
- `minimum_order_total`: minimum subtotal required
- `apply_once_per_cart`: apply to one line only when line-based
- `apply_to_cart_total`: use as cart-level coupon
- `free_shipping`: when cart-level discount is applied, shipping is set to 0

### Discount Applications
- `GET /discount_applications`
- `GET /discount_applications/{discount_application}`
- `POST /discount_applications`
- `PATCH /discount_applications/{discount_application}`
- `DELETE /discount_applications/{discount_application}`

### Product Types
- `GET /product_types`
- `GET /product_types/{product_type}`
- `POST /product_types`
- `PATCH /product_types/{product_type}`
- `DELETE /product_types/{product_type}`

### Product Variants (Axes)
- `GET /product_variants`
- `GET /product_variants/{product_variant}`
- `POST /product_variants`
- `PATCH /product_variants/{product_variant}`
- `DELETE /product_variants/{product_variant}`

### Product Variant Options (Values)
- `GET /product_variant_options`
- `GET /product_variant_options/{product_variant_option}`
- `POST /product_variant_options`
- `PATCH /product_variant_options/{product_variant_option}`
- `DELETE /product_variant_options/{product_variant_option}`

### Product Variants for a Product
- `GET /products/{product}/variants`
- `POST /products/{product}/variants`

This endpoint generates concrete variant products from variant option combinations.

## Variant Workflow
1. Create a product type.
2. Create variant axes for that type, for example Color and Size.
3. Create options for each axis, for example Red, Blue, S, M.
4. Create a base product with `product_type_id` set.
5. Generate variant products using `POST /products/{product}/variants`.

### Example: Create Product Type
```http
POST /venditio/api/v1/product_types
Content-Type: application/json

{
  "name": "Apparel",
  "active": true
}
```

### Example: Create Variant Axes
```http
POST /venditio/api/v1/product_variants
Content-Type: application/json

{
  "product_type_id": 1,
  "name": "Color",
  "sort_order": 1
}
```

```http
POST /venditio/api/v1/product_variants
Content-Type: application/json

{
  "product_type_id": 1,
  "name": "Size",
  "sort_order": 2
}
```

### Example: Create Variant Options
```http
POST /venditio/api/v1/product_variant_options
Content-Type: application/json

{
  "product_variant_id": 10,
  "name": "red",
  "sort_order": 1
}
```

```http
POST /venditio/api/v1/product_variant_options
Content-Type: application/json

{
  "product_variant_id": 11,
  "name": "m",
  "sort_order": 2
}
```

### Example: Create Base Product
```http
POST /venditio/api/v1/products
Content-Type: application/json

{
  "brand_id": 1,
  "tax_class_id": 1,
  "product_type_id": 1,
  "name": "T-shirt",
  "status": "published"
}
```

### Example: Generate Variant Products
```http
POST /venditio/api/v1/products/100/variants
Content-Type: application/json

{
  "variants": [
    {
      "variant_id": 10,
      "option_ids": [101, 102]
    },
    {
      "variant_id": 11,
      "option_ids": [201, 202, 203]
    }
  ]
}
```

Response includes the created variant products and a `meta` object with counts.

## Validation and Errors
- Validation errors return HTTP 422 with error messages in the standard Laravel format.
- Variant generation validates that:
  - The base product has a `product_type_id`
  - Variant axes belong to that product type
  - Options belong to their axis
  - Variant ids are unique in the payload
  - The base product is not itself a variant

## Discount Examples

### Example: Create Cart Total Discount With Free Shipping
```http
POST /venditio/api/v1/discounts
Content-Type: application/json

{
  "name": "Checkout 10 + free shipping",
  "type": "percentage",
  "value": 10,
  "code": "CHECKOUT10FREE",
  "active": true,
  "starts_at": "2026-02-12 09:00:00",
  "apply_to_cart_total": true,
  "free_shipping": true,
  "minimum_order_total": 100,
  "max_uses": 500,
  "max_uses_per_user": 3
}
```

### Example: Create Discount Reserved To One User
```http
POST /venditio/api/v1/discounts
Content-Type: application/json

{
  "name": "VIP user discount",
  "type": "fixed",
  "value": 20,
  "code": "VIP20",
  "active": true,
  "starts_at": "2026-02-12 09:00:00",
  "discountable_type": "user",
  "discountable_id": 42,
  "one_per_user": true
}
```

### Example: Update Discount Limits
```http
PATCH /venditio/api/v1/discounts/10
Content-Type: application/json

{
  "max_uses": 1000,
  "max_uses_per_user": 5,
  "minimum_order_total": 150
}
```

## Configuration Highlights
See `config/venditio-core.php` for:
- Routes configuration and versioning
- Pagination defaults
- Product variant naming and copy behavior
- Model overrides
