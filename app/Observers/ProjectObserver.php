<?php

namespace App\Observers;

use App\Services\ImageOptimizer;

class ProjectObserver
{
    public function saved($project): void
    {
        // Optimize banner image
        if ($project->banner_image && $project->wasChanged('banner_image')) {
            ImageOptimizer::optimize($project->banner_image, 'public');
        }

        // Optimize gallery images
        if ($project->gallery && $project->wasChanged('gallery')) {
            foreach ($project->gallery as $imagePath) {
                ImageOptimizer::optimize($imagePath, 'public');
            }
        }
    }
}
