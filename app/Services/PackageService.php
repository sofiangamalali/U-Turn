<?php

namespace App\Services;

use App\Models\Package;


class PackageService
{
    public function getAll()
    {
        return Package::with(['perAdFeatures', 'subscriptionFeatures'])
            ->latest()
            ->get();
    }
}
