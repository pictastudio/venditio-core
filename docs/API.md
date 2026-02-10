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

## Configuration Highlights
See `config/venditio-core.php` for:
- Routes configuration and versioning
- Pagination defaults
- Product variant naming and copy behavior
- Model overrides
