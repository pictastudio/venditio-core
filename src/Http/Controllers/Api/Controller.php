<?php

namespace PictaStudio\VenditioCore\Http\Controllers\Api;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\{Builder, Collection};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\{JsonResponse, Response};
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\Rule;
use PictaStudio\VenditioCore\Traits\ValidatesData;

use function PictaStudio\VenditioCore\Helpers\Functions\resolve_model;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesData;
    use ValidatesRequests;

    public function applyBaseFilters(Builder $query, array $filters, string $model): Collection|LengthAwarePaginator
    {
        $model = app(resolve_model($model));

        $filters = array_filter(
            $filters,
            fn (mixed $value) => filled($value)
        );

        $this->validateData($filters, [
            'all' => [
                'boolean',
            ],
            'id' => [
                'array',
            ],
            'id.*' => [
                Rule::exists(
                    method_exists($model, 'getTableName') ? $model->getTableName() : $model->getTable(),
                    $model->getKeyName()
                ),
            ],
        ]);

        return $query->when(
            isset($filters['id']),
            fn (Builder $query) => $query->whereKey($filters['id']),
        )
            ->when(
                isset($filters['all']),
                fn (Builder $query) => $query->get(),
                fn (Builder $query) => $query->paginate(
                    request('per_page', config('venditio-core.routes.api.v1.pagination.per_page'))
                ),
            );
    }

    public function successJsonResponse(array|string $data = [], ?string $message = null, int $status = Response::HTTP_OK): JsonResponse
    {
        return $this->jsonResponse($data, $message, $status);
    }

    public function errorJsonResponse(array|string $data = [], ?string $message = null, int $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->jsonResponse($data, $message, $status);
    }

    public function jsonResponse(array|string $data = [], ?string $message = null, int $status = Response::HTTP_OK): JsonResponse
    {
        $response = [
            'status' => true,
            'data' => $data,
        ];

        if ($status !== Response::HTTP_OK) {
            $response['status'] = false;
        }

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response, $status);
    }

    protected function authorizeIfConfigured(string $ability, mixed $arguments): void
    {
        if (!config('venditio-core.policies.register')) {
            return;
        }

        if (!auth()->check()) {
            return;
        }

        $this->authorize($ability, $arguments);
    }
}
