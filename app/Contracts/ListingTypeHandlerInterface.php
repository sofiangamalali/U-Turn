<?php

namespace App\Contracts;

interface ListingTypeHandlerInterface
{
    public function create(array $data): array;

    public function update(array $data, $model): array;
}