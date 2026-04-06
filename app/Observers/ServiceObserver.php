<?php

namespace App\Observers;

use App\Services\ImageOptimizer;

class ServiceObserver
{
    public function saved($service): void
    {
        if ($service->banner_image && $service->wasChanged('banner_image')) {
            ImageOptimizer::optimize($service->banner_image, 'public');
        }
    }
}
