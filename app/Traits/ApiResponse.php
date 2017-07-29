<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

trait ApiResponse
{
    private function successResponse($data, $code)
    {
        return response()->json($data, $code);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }

    protected function showAll(Collection $collection, $code = 200)
    {

        if ($collection->isEmpty()) {
            return $this->successResponse(['data' => $collection], $code);
        }
        $transformer = $collection->first()->transformer;
        $collection = $this->orderBy($collection);
        $collection = $this->filterData($collection, $transformer);
        $collection = $this->sortData($collection, $transformer);
        $collection = $this->paginate($collection);
        $collection = $this->transformData($collection, $transformer);
      //  $collection = $this->cacheResponse($collection);
        return $this->successResponse($collection, $code);
    }

    protected function showOne(Model $instance, $code = 200)
    {

        $transformer = $instance->transformer;
        $instance = $this->transformData($instance, $transformer);
        return $this->successResponse($instance, $code);
    }

    protected function showMessage($message, $code = 200)
    {
        return $this->successResponse(['data' => $message], $code);
    }

    protected function sortData(Collection $collection, $transformer)
    {
        if (request()->has('sort_by')) {
            $attribute = $transformer::originalAttribute(request()->sort_by);
            $collection = $collection->sortBy($attribute);
        }
        return $collection;
    }

    protected function transformData($data, $tranformer)
    {
        $transformation = fractal($data, new $tranformer);
        return $transformation->toArray();
    }

    protected function filterData(Collection $collection, $tranformer)
    {
        foreach (request()->query() as $query => $values) {
            $attribute = $tranformer::originalAttribute($query);
            if (isset($attribute, $values)) {
                $collection = $collection->where($attribute, $values);
            }
        }
        return $collection;
    }

    protected function paginate(Collection $collection)
    {
        $rules = [
            'per_page' => 'integer|min:2|max:50'
        ];
        Validator::validate(request()->all(), $rules);
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = config('my_conf.pagination_limit');
        if (request()->has('per_page')) {
            $perPage = (int)request()->per_page;
        }
        $result = $collection->slice(($page - 1) * $perPage, $perPage)->values();
        $paginated = new LengthAwarePaginator($result, $collection->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);
        $paginated->appends(request()->all());
        return $paginated;
    }

    protected function orderBy(Collection $collection)
    {
        $rules = [
            'order_by' => 'string|in:asc,desc'
        ];
        Validator::validate(request()->all(), $rules);
        if (request()->has('order_by')) {
            if (request()->order_by == 'desc') {
                return $collection->reverse();
            } else {
                return $collection;
            }
        } else {
            return $collection->reverse();
        }
    }

    public function cacheResponse($data)
    {

        $url = request()->url();
        $queryParam = request()->query();
        ksort($queryParam);

        $queryString = http_build_query($queryParam);

        $fullUrl = "{$url}?{$queryString}";
        return Cache::remember($fullUrl, 30 / 60, function () use ($data) {
            return $data;
        });

    }

}