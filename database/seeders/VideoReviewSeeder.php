<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\VideoReview;
use Illuminate\Database\Seeder;

class VideoReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing video reviews to avoid cluttering
        VideoReview::truncate();

        $products = Product::all();
        
        // Public Mixkit food-related video URLs
        $videoUrls = [
            'https://assets.mixkit.co/videos/preview/mixkit-pouring-honey-from-a-wooden-spoon-42358-large.mp4',
            'https://assets.mixkit.co/videos/preview/mixkit-slow-motion-of-runny-honey-dripping-42797-large.mp4',
            'https://assets.mixkit.co/videos/preview/mixkit-close-up-of-honey-spoon-dripping-42784-large.mp4',
            'https://assets.mixkit.co/videos/preview/mixkit-cooking-with-oil-and-fresh-ingredients-in-a-pan-43093-large.mp4',
            'https://assets.mixkit.co/videos/preview/mixkit-pouring-olive-oil-on-fresh-salad-close-up-43033-large.mp4',
        ];

        $reviewers = [
            'Sita Devi', 
            'Ramesh Kumar', 
            'Priya Patel', 
            'Amit Sharma', 
            'Anjali Gupta', 
            'Vikram Singh', 
            'Neha Joshi',
            'Sunil Verma', 
            'Kavita Reddy',
            'Rajesh Mehta'
        ];

        for ($i = 0; $i < 10; $i++) {
            // Pick a product sequentially or randomly to ensure diversity
            $product = $products->count() > 0 ? $products[$i % $products->count()] : null;
            
            VideoReview::create([
                'reviewer_name' => $reviewers[$i],
                'video_path' => $videoUrls[$i % count($videoUrls)],
                'product_id' => $product ? $product->id : null,
                'is_active' => true,
                'sort_order' => $i,
            ]);
        }
    }
}
