<?php

use App\Models\BilonaStep;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (BilonaStep::count() > 0) {
            return;
        }

        $steps = [
            [
                'title' => 'Milking with care',
                'description' => 'A2 Desi Cows are Milked by Hand, Honoring Ancient Ayurvedic traditions for purity and authenticity.',
                'image_path' => 'images/bilona-step-1.jpg',
            ],
            [
                'title' => 'Heating Milk and Preparing Curd',
                'description' => 'Heat A2 milk in a clay pot, add curd culture, and let it ferment into rich curd.',
                'image_path' => 'images/bilona-step-2.jpg',
            ],
            [
                'title' => 'Traditionally wood churned',
                'description' => 'The curd is churned using a wooden bilona, extracting rich and wholesome butter.',
                'image_path' => 'images/steps/stpe-3.png',
            ],
            [
                'title' => 'Slow Cooking the Butter',
                'description' => 'This process evaporates water content and converts the butter into aromatic golden ghee.',
                'image_path' => 'images/steps/stpe-4.png',
            ],
            [
                'title' => 'Filtering and Packaging',
                'description' => 'Once cooled, the pure Bilona A2 Ghee is carefully packaged to preserve its freshness and rich aroma.',
                'image_path' => 'images/steps/stpe-5.png',
            ],
        ];

        foreach ($steps as $index => $step) {
            BilonaStep::create([
                'title' => $step['title'],
                'description' => $step['description'],
                'image_path' => $step['image_path'],
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        BilonaStep::truncate();
    }
};
