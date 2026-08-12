<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\DeliveryCharge;
use App\Models\Faq;
use App\Models\Menu;
use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductGallery;
use App\Models\ProductImage;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles and Permissions
        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Full access to all settings and dashboard management.'
        ]);

        $customerRole = Role::create([
            'name' => 'customer',
            'display_name' => 'Retail Customer',
            'description' => 'Access to buy products, track orders, and view dashboard.'
        ]);

        $permissions = [
            'manage-catalog' => 'Create, edit and delete products, categories, and brands.',
            'manage-orders' => 'View, update status, and manage customer shipments.',
            'manage-customers' => 'View customer profiles and manage wallet transactions.',
            'manage-settings' => 'Modify site configurations, payment gateways, and SMTP.',
            'view-dashboard' => 'Access administrative analytics panel.'
        ];

        foreach ($permissions as $name => $desc) {
            $permission = Permission::create([
                'name' => $name,
                'display_name' => ucwords(str_replace('-', ' ', $name)),
                'description' => $desc
            ]);
            $adminRole->permissions()->attach($permission);
        }

        // 2. Users
        $admin = User::create([
            'name' => 'RohidaFarm Admin',
            'email' => 'admin@rohidafarm.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '9876543210',
            'email_verified_at' => now(),
        ]);
        $admin->roles()->attach($adminRole);

        $customer = User::create([
            'name' => 'Rajendra Kumar',
            'email' => 'customer@rohidafarm.com',
            'password' => Hash::make('customer123'),
            'role' => 'customer',
            'phone' => '8888888888',
            'wallet_balance' => 500.00, // Seed 500 Rs in wallet
            'email_verified_at' => now(),
        ]);
        $customer->roles()->attach($customerRole);

        // Seed customer address
        Address::create([
            'user_id' => $customer->id,
            'type' => 'shipping',
            'name' => 'Rajendra Kumar',
            'phone' => '8888888888',
            'address_line1' => 'Plot 45, Golden Heights, VIP Road',
            'address_line2' => 'Near Central Park',
            'city' => 'Pune',
            'state' => 'Maharashtra',
            'postal_code' => '411001',
            'country' => 'India',
            'is_default' => true,
        ]);

        // 3. Brand
        $brand = Brand::create([
            'name' => 'RohidaFarm',
            'slug' => 'rohidafarm',
            'logo' => '/assets/images/logo.png',
            'description' => 'RohidaFarm - Pure and Traditional Organic Ghee and Farm Fresh products.',
            'is_active' => true
        ]);

        // 4. Categories & Sub Categories
        $categoriesData = [
            [
                'name' => 'Ghee',
                'slug' => 'ghee',
                'description' => 'Pure and Traditional Bilona Churned Ghee made from grass-fed cows and buffaloes.',
                'sub' => [
                    ['name' => 'Cow Ghee', 'slug' => 'cow-ghee'],
                    ['name' => 'Buffalo Ghee', 'slug' => 'buffalo-ghee'],
                    ['name' => 'Bilona Ghee', 'slug' => 'bilona-ghee'],
                ]
            ],
            [
                'name' => 'Cold Pressed Oil',
                'slug' => 'cold-pressed-oil',
                'description' => '100% natural, wood-pressed and chemical-free cold-pressed oils.',
                'sub' => [
                    ['name' => 'Mustard Oil', 'slug' => 'mustard-oil'],
                    ['name' => 'Groundnut Oil', 'slug' => 'groundnut-oil'],
                    ['name' => 'Coconut Oil', 'slug' => 'coconut-oil'],
                ]
            ],
            [
                'name' => 'Honey',
                'slug' => 'honey',
                'description' => 'Raw, unpasteurized forest and monofloral organic honey.',
                'sub' => [
                    ['name' => 'Wild Forest Honey', 'slug' => 'wild-forest-honey'],
                    ['name' => 'Monofloral Honey', 'slug' => 'monofloral-honey'],
                ]
            ],
            [
                'name' => 'Spices',
                'slug' => 'spices',
                'description' => 'Pure, ground spices sourced directly from sustainable farms.',
                'sub' => [
                    ['name' => 'Turmeric', 'slug' => 'turmeric-powder'],
                    ['name' => 'Coriander', 'slug' => 'coriander-powder'],
                    ['name' => 'Chilli', 'slug' => 'chilli-powder'],
                ]
            ],
            [
                'name' => 'Pickles',
                'slug' => 'pickles',
                'description' => 'Grandma style pickles made with wood-pressed oil and organic spices.',
                'sub' => []
            ],
            [
                'name' => 'Dry Fruits',
                'slug' => 'dry-fruits',
                'description' => 'Premium selection of raw and roasted organic dry fruits.',
                'sub' => []
            ]
        ];

        foreach ($categoriesData as $catData) {
            $category = Category::create([
                'name' => $catData['name'],
                'slug' => $catData['slug'],
                'description' => $catData['description'],
                'image' => '/assets/images/categories/' . $catData['slug'] . '.jpg',
                'is_active' => true
            ]);

            foreach ($catData['sub'] as $subData) {
                SubCategory::create([
                    'category_id' => $category->id,
                    'name' => $subData['name'],
                    'slug' => $subData['slug'],
                    'description' => $subData['name'] . ' subcategory of RohidaFarm.',
                    'image' => '/assets/images/categories/' . $subData['slug'] . '.jpg',
                    'is_active' => true
                ]);
            }
        }

        // 5. Products, Variants, Images & Reviews
        // Fetch categories and subcategories to link
        $gheeCategory = Category::where('slug', 'ghee')->first();
        $cowGheeSub = SubCategory::where('slug', 'cow-ghee')->first();
        $bilonaGheeSub = SubCategory::where('slug', 'bilona-ghee')->first();
        $buffaloGheeSub = SubCategory::where('slug', 'buffalo-ghee')->first();

        $oilCategory = Category::where('slug', 'cold-pressed-oil')->first();
        $groundnutOilSub = SubCategory::where('slug', 'groundnut-oil')->first();
        $mustardOilSub = SubCategory::where('slug', 'mustard-oil')->first();

        $honeyCategory = Category::where('slug', 'honey')->first();
        $wildHoneySub = SubCategory::where('slug', 'wild-forest-honey')->first();

        $spicesCategory = Category::where('slug', 'spices')->first();
        $turmericSub = SubCategory::where('slug', 'turmeric-powder')->first();

        $picklesCategory = Category::where('slug', 'pickles')->first();
        $dryFruitsCategory = Category::where('slug', 'dry-fruits')->first();

        // Product 1: A2 Vedic Bilona Cow Ghee
        $p1 = Product::create([
            'category_id' => $gheeCategory->id,
            'sub_category_id' => $cowGheeSub->id,
            'brand_id' => $brand->id,
            'name' => 'A2 Desi Cow Ghee',
            'slug' => 'a2-desi-cow-ghee',
            'sku' => 'RF-A2-GHEE-1L',
            'weight' => '1L',
            'stock' => 150,
            'mrp' => 1999.00,
            'sale_price' => 1799.00,
            'is_bilona' => true,
            'is_organic' => true,
            'is_featured' => true,
            'is_trending' => true,
            'is_best_seller' => true,
            'is_new_arrival' => false,
            'short_description' => 'Traditionally prepared A2 Vedic Cow Ghee using the curd-churning Bilona method. Highly nutritious, rich in aroma, and granular in texture.',
            'description' => 'Our A2 Vedic Cow Ghee is made from the milk of grass-fed Hallikar/Gir cows. It is prepared in absolute alignment with the Charaka Samhita guidelines. First, the milk is boiled and cultured into curd. Then, the curd is churned bi-directionally using a wooden churner (Bilona) to separate the butter (makkhan). This butter is then slow-cooked in brass vessels over firewood to yield the finest golden liquid ghee. It is free from any preservatives, synthetics, and artificial colors.',
            'benefits' => "• Rich in fat-soluble vitamins (A, D, E, K)\n• Improves digestion and strengthens gut health\n• Enhances memory and cognitive functions\n• High smoke point, making it perfect for cooking\n• Boosts immunity and joint health",
            'ingredients' => '100% Pure A2 Grass-fed Cow Milk Butter fat.',
            'nutrition_facts' => "Servings size: 10ml\nCalories: 90\nTotal Fat: 9.9g (15% DV)\nSaturated Fat: 6.2g\nTrans Fat: 0g\nCholesterol: 25mg\nProtein: 0g\nCarbohydrate: 0g",
            'how_to_use' => 'Drizzle over hot rotis, mix in steamed rice and dal, use for cooking and baking, or consume 1 teaspoon on an empty stomach daily for optimal wellness benefits.',
            'rating' => 4.90,
            'reviews_count' => 3,
            'is_active' => true
        ]);

        ProductImage::create(['product_id' => $p1->id, 'image_path' => 'https://cdn.shopify.com/s/files/1/0270/3346/9006/files/Desi-cow-ghee-500-ml.jpg?v=1778050408', 'is_primary' => true]);
        ProductGallery::create(['product_id' => $p1->id, 'image_path' => 'https://cdn.shopify.com/s/files/1/0270/3346/9006/files/Desi-cow-ghee-1-litre.jpg?v=1778050408', 'sort_order' => 1]);

        ProductVariant::create([
            'product_id' => $p1->id,
            'name' => '500ml',
            'sku' => 'RF-A2-GHEE-500M',
            'weight' => '500ml',
            'stock' => 200,
            'mrp' => 1099.00,
            'sale_price' => 949.00,
        ]);
        ProductVariant::create([
            'product_id' => $p1->id,
            'name' => '1L',
            'sku' => 'RF-A2-GHEE-1L',
            'weight' => '1L',
            'stock' => 150,
            'mrp' => 1999.00,
            'sale_price' => 1799.00,
        ]);
        ProductVariant::create([
            'product_id' => $p1->id,
            'name' => '2L',
            'sku' => 'RF-A2-GHEE-2L',
            'weight' => '2L',
            'stock' => 50,
            'mrp' => 3899.00,
            'sale_price' => 3499.00,
        ]);

        // Product 2: Traditional Buffalo Ghee
        $p2 = Product::create([
            'category_id' => $gheeCategory->id,
            'sub_category_id' => $cowGheeSub->id,
            'brand_id' => $brand->id,
            'name' => 'A2 Gir Cow Ghee',
            'slug' => 'a2-gir-cow-ghee',
            'sku' => 'RF-BUF-GHEE-1L',
            'weight' => '1L',
            'stock' => 80,
            'mrp' => 1299.00,
            'sale_price' => 1149.00,
            'is_bilona' => true,
            'is_organic' => true,
            'is_featured' => true,
            'is_trending' => false,
            'is_best_seller' => false,
            'is_new_arrival' => true,
            'short_description' => 'White, granular, and aromatic traditional Buffalo Ghee churned from curd.',
            'description' => 'Made from milk of grass-fed Murrah buffaloes, this traditional Bilona ghee is packed with healthy fats, granular texture, and a rich, creamy aroma. It is highly beneficial for muscle growth and strengthening bones.',
            'benefits' => '• Promotes healthy weight gain and muscle strength\n• Rich in calcium and iron\n• Improves sleep patterns\n• Relieves dry throat and dry skin issues',
            'ingredients' => '100% Pure Buffalo Milk butter fat.',
            'nutrition_facts' => "Calories: 90\nTotal Fat: 10g\nProtein: 0g\nCarbohydrate: 0g",
            'how_to_use' => 'Ideal for deep frying, making traditional Indian sweets, or adding to standard meals.',
            'rating' => 4.80,
            'reviews_count' => 1,
            'is_active' => true
        ]);

        ProductImage::create(['product_id' => $p2->id, 'image_path' => 'https://cdn.shopify.com/s/files/1/0270/3346/9006/files/Artboard12_9aa0bb70-c8dd-4f7f-b2c0-81e248099162.jpg?v=1773726670', 'is_primary' => true]);
        ProductGallery::create(['product_id' => $p2->id, 'image_path' => 'https://cdn.shopify.com/s/files/1/0270/3346/9006/files/anveshan-girghee-5ltr-dolchi.jpg?v=1768053611', 'sort_order' => 1]);
        ProductVariant::create([
            'product_id' => $p2->id,
            'name' => '1L',
            'sku' => 'RF-BUF-GHEE-1L',
            'weight' => '1L',
            'stock' => 80,
            'mrp' => 1299.00,
            'sale_price' => 1149.00,
        ]);

        // Product 3: Wood Pressed Groundnut Oil
        $p3 = Product::create([
            'category_id' => $oilCategory->id,
            'sub_category_id' => $groundnutOilSub->id,
            'brand_id' => $brand->id,
            'name' => 'Woodpressed Groundnut Oil',
            'slug' => 'woodpressed-groundnut-oil',
            'sku' => 'RF-G-OIL-1L',
            'weight' => '1L',
            'stock' => 120,
            'mrp' => 420.00,
            'sale_price' => 379.00,
            'is_bilona' => false,
            'is_organic' => true,
            'is_featured' => false,
            'is_trending' => true,
            'is_best_seller' => true,
            'is_new_arrival' => false,
            'short_description' => 'Wood-pressed groundnut oil, 100% natural, chemical-free, and unrefined.',
            'description' => 'Cold-pressed under low temperature using traditional wooden ghani machines, preserving all natural nutrition, aroma, and properties of groundnuts.',
            'benefits' => '• Packed with heart-healthy monounsaturated fats\n• Good source of Vitamin E antioxidants\n• Enhances metabolism',
            'ingredients' => '100% Premium Organic Groundnuts.',
            'nutrition_facts' => 'Total Fat: 14g (per tablespoon)\nSaturated: 2.3g\nMonounsaturated: 6.2g\nPolyunsaturated: 4.3g',
            'how_to_use' => 'Ideal for daily cooking, baking, sauteing, and shallow frying.',
            'rating' => 4.70,
            'reviews_count' => 1,
            'is_active' => true
        ]);

        ProductImage::create(['product_id' => $p3->id, 'image_path' => 'https://cdn.shopify.com/s/files/1/0270/3346/9006/files/Artboard_12_6.jpg?v=1763560050', 'is_primary' => true]);
        ProductGallery::create(['product_id' => $p3->id, 'image_path' => 'https://cdn.shopify.com/s/files/1/0270/3346/9006/products/gno_anv_01_glass_1L.jpg?v=1767422676', 'sort_order' => 1]);

        // Product 4: Organic Wild Forest Honey
        $p4 = Product::create([
            'category_id' => $honeyCategory->id,
            'sub_category_id' => $wildHoneySub->id,
            'brand_id' => $brand->id,
            'name' => 'Raw Wild Forest Honey',
            'slug' => 'raw-wild-forest-honey',
            'sku' => 'RF-HONEY-500G',
            'weight' => '500g',
            'stock' => 110,
            'mrp' => 599.00,
            'sale_price' => 499.00,
            'is_bilona' => false,
            'is_organic' => true,
            'is_featured' => false,
            'is_trending' => false,
            'is_best_seller' => false,
            'is_new_arrival' => true,
            'short_description' => 'Raw, unpasteurized honey collected by tribals from wild forest beehives.',
            'description' => 'Naturally sourced wild honey with no added sugar, antibiotics, or heating. Pure, medicinal, and authentic.',
            'benefits' => '• Natural cough suppressant and immunity builder\n• Rich in antioxidants and minerals\n• Excellent source of healthy energy',
            'ingredients' => '100% Raw Wild Forest Honey.',
            'nutrition_facts' => 'Carbohydrates: 17g (per tablespoon)\nSugars: 16g\nProtein: 0g',
            'how_to_use' => 'Consume raw with warm water in the morning, or use as a premium natural sweetener.',
            'rating' => 4.90,
            'reviews_count' => 1,
            'is_active' => true
        ]);

        ProductImage::create(['product_id' => $p4->id, 'image_path' => 'https://cdn.shopify.com/s/files/1/0270/3346/9006/files/500gm.webp?v=1768374154', 'is_primary' => true]);
        ProductGallery::create(['product_id' => $p4->id, 'image_path' => 'https://cdn.shopify.com/s/files/1/0270/3346/9006/files/1kgcopy2.webp?v=1774015457', 'sort_order' => 1]);

        // Product 5: Organic Lakadong Turmeric Powder
        $p5 = Product::create([
            'category_id' => $oilCategory->id,
            'sub_category_id' => $mustardOilSub->id,
            'brand_id' => $brand->id,
            'name' => 'Woodpressed Black Mustard Oil',
            'slug' => 'woodpressed-black-mustard-oil',
            'sku' => 'RF-TUR-250G',
            'weight' => '1L',
            'stock' => 140,
            'mrp' => 249.00,
            'sale_price' => 199.00,
            'is_bilona' => false,
            'is_organic' => true,
            'is_featured' => false,
            'is_trending' => false,
            'is_best_seller' => false,
            'is_new_arrival' => true,
            'short_description' => 'High curcumin Lakadong Turmeric sourced from Meghalaya hills.',
            'description' => 'Sourced directly from the fields of Meghalaya, this turmeric boasts a curcumin content of over 7%, making it extremely high in anti-inflammatory properties compared to normal turmeric.',
            'benefits' => '• Strong anti-inflammatory and antioxidant properties\n• Helps boost immunity\n• Imparts vibrant yellow color and earthy aroma',
            'ingredients' => '100% Pure Ground Lakadong Turmeric Root.',
            'nutrition_facts' => 'Curcumin: >7%\nSodium: 0mg\nTotal Carbs: 2g',
            'how_to_use' => 'Use in everyday cooking, or consume in warm golden milk.',
            'rating' => 5.00,
            'reviews_count' => 0,
            'is_active' => true
        ]);

        ProductImage::create(['product_id' => $p5->id, 'image_path' => 'https://cdn.shopify.com/s/files/1/0270/3346/9006/files/Artboard12_2_422e0a2c-6179-4458-b457-da7ff2eb1385.jpg?v=1773726594', 'is_primary' => true]);
        ProductGallery::create(['product_id' => $p5->id, 'image_path' => 'https://cdn.shopify.com/s/files/1/0270/3346/9006/files/bmo_anv_01_glass_1L-sm.jpg?v=1763559260', 'sort_order' => 1]);

        // Seed product reviews
        ProductReview::create([
            'product_id' => $p1->id,
            'user_id' => $customer->id,
            'rating' => 5,
            'title' => 'Best Ghee Ever!',
            'review' => 'This Ghee smells just like my grandmothers farm. Granular, aromatic, and pure. Worth every rupee.',
            'is_approved' => true,
            'is_featured' => true
        ]);
        ProductReview::create([
            'product_id' => $p1->id,
            'user_id' => $customer->id,
            'rating' => 5,
            'title' => 'Aroma is amazing',
            'review' => 'Pure traditional bilona ghee, granular structure is top notch.',
            'is_approved' => true,
            'is_featured' => false
        ]);
        ProductReview::create([
            'product_id' => $p2->id,
            'user_id' => $customer->id,
            'rating' => 4,
            'title' => 'Very Rich and Delicious',
            'review' => 'Great white ghee, granular texture is amazing. Packaging was clean.',
            'is_approved' => true
        ]);

        // 6. Settings
        $settingsData = [
            // SMTP Settings
            ['key' => 'mail_mailer', 'value' => 'smtp', 'type' => 'string', 'group' => 'smtp', 'description' => 'Mail protocol driver.'],
            ['key' => 'mail_host', 'value' => 'smtp.mailtrap.io', 'type' => 'string', 'group' => 'smtp', 'description' => 'SMTP mail server host.'],
            ['key' => 'mail_port', 'value' => '2525', 'type' => 'string', 'group' => 'smtp', 'description' => 'SMTP port address.'],
            ['key' => 'mail_username', 'value' => 'test_user_id', 'type' => 'string', 'group' => 'smtp', 'description' => 'SMTP authentication username.'],
            ['key' => 'mail_password', 'value' => 'test_password', 'type' => 'string', 'group' => 'smtp', 'description' => 'SMTP authentication password.'],
            ['key' => 'mail_encryption', 'value' => 'tls', 'type' => 'string', 'group' => 'smtp', 'description' => 'SMTP secure transport layer.'],
            ['key' => 'mail_from_address', 'value' => 'no-reply@rohidafarm.com', 'type' => 'string', 'group' => 'smtp', 'description' => 'System mail sender address.'],
            ['key' => 'mail_from_name', 'value' => 'RohidaFarm', 'type' => 'string', 'group' => 'smtp', 'description' => 'System mail sender name.'],
            
            // SEO Settings
            ['key' => 'meta_title', 'value' => 'RohidaFarm - Pure and Traditional Organic Ghee', 'type' => 'string', 'group' => 'seo', 'description' => 'Default SEO browser title tag.'],
            ['key' => 'meta_description', 'value' => 'Premium luxury organic ghee, wood pressed oils, wild forest honey, and organic spices. Traditionally prepared in small batches.', 'type' => 'text', 'group' => 'seo', 'description' => 'Default SEO meta description.'],
            ['key' => 'meta_keywords', 'value' => 'ghee, bilona ghee, a2 cow ghee, organic, raw honey, wood pressed oil, traditional ghee', 'type' => 'string', 'group' => 'seo', 'description' => 'Default search keyword list.'],
            ['key' => 'meta_canonical', 'value' => 'https://rohidafarm.com', 'type' => 'string', 'group' => 'seo', 'description' => 'Canonical primary URL root.'],
            ['key' => 'schema_organization', 'value' => '{"@context":"https://schema.org","@type":"Organization","name":"RohidaFarm","url":"https://rohidafarm.com","logo":"https://rohidafarm.com/assets/images/logo.png"}', 'type' => 'json', 'group' => 'seo', 'description' => 'Organization structured metadata.'],
            
            // Payment Gateway Sandbox Toggles
            ['key' => 'razorpay_key_id', 'value' => 'rzp_test_mockKey123', 'type' => 'string', 'group' => 'payment', 'description' => 'Razorpay sandbox key ID.'],
            ['key' => 'razorpay_key_secret', 'value' => 'secret_mockKeySecret123', 'type' => 'string', 'group' => 'payment', 'description' => 'Razorpay client credentials.'],
            ['key' => 'stripe_key', 'value' => 'pk_test_stripeMockKey123', 'type' => 'string', 'group' => 'payment', 'description' => 'Stripe PK publishable key.'],
            ['key' => 'stripe_secret', 'value' => 'sk_test_stripeMockSecret123', 'type' => 'string', 'group' => 'payment', 'description' => 'Stripe SDK API credentials.'],
            ['key' => 'paypal_client_id', 'value' => 'paypal_client_mockId123', 'type' => 'string', 'group' => 'payment', 'description' => 'PayPal API client.'],
            
            // Delhivery Settings
            ['key' => 'delhivery_api_token', 'value' => '', 'type' => 'string', 'group' => 'delhivery', 'description' => 'Delhivery API token.'],
            ['key' => 'delhivery_pickup_location', 'value' => '', 'type' => 'string', 'group' => 'delhivery', 'description' => 'Delhivery registered pickup location name.'],
            ['key' => 'delhivery_client_name', 'value' => '', 'type' => 'string', 'group' => 'delhivery', 'description' => 'Delhivery registered client name (for waybill generation).'],
            
            // General Company Meta
            ['key' => 'contact_phone', 'value' => '+91 98765 43210', 'type' => 'string', 'group' => 'general', 'description' => 'Support phone line.'],
            ['key' => 'contact_email', 'value' => 'care@rohidafarm.com', 'type' => 'string', 'group' => 'general', 'description' => 'Customer support inbox.'],
            ['key' => 'contact_address', 'value' => 'Rohida Organic Farm, District Pune, Maharashtra - 411001', 'type' => 'string', 'group' => 'general', 'description' => 'Farming site headquarters.'],
        ];

        foreach ($settingsData as $setVal) {
            Setting::create($setVal);
        }

        // 7. Delivery Charges Config
        DeliveryCharge::create([
            'min_order_amount' => 999.00,
            'charge_amount' => 99.00,
            'pincodes' => null, // null = all pincodes
            'is_active' => true
        ]);

        // 8. FAQs
        $faqs = [
            ['question' => 'What is Bilona Ghee?', 'answer' => 'Bilona Ghee is prepared using the traditional curd-churning method. The cow milk is boiled, turned to curd, and then churned with a wooden churning staff (Bilona) to retrieve butter, which is then slow-cooked to make Ghee. This preserves nutritional integrity.', 'category' => 'Products', 'sort_order' => 1],
            ['question' => 'What is the difference between A2 and regular Ghee?', 'answer' => 'A2 Ghee is prepared exclusively from milk of indigenous cows containing only the A2 beta-casein protein, which is highly digestible, anti-inflammatory, and reduces risk factors associated with gut allergies.', 'category' => 'Products', 'sort_order' => 2],
            ['question' => 'Is Cash On Delivery (COD) available?', 'answer' => 'Yes! We offer Cash on Delivery (COD) across major pincodes in India. Select COD at checkout to pay upon delivery.', 'category' => 'Shipping', 'sort_order' => 3],
            ['question' => 'How long does shipment take?', 'answer' => 'Standard shipments are delivered within 3-5 business days across metros and 5-7 days for other regions. You will receive real-time tracking links.', 'category' => 'Shipping', 'sort_order' => 4],
        ];

        foreach ($faqs as $faqVal) {
            Faq::create($faqVal);
        }

        // 9. Slider & Banners
        Slider::create([
            'title' => 'Pure A2 Vedic Bilona Ghee',
            'subtitle' => 'Traditionally churned from grass-fed Gir Cow milk',
            'image_path' => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=800&q=80',
            'button_text' => 'Shop Premium Ghee',
            'button_url' => '/shop?category=ghee',
            'sort_order' => 1,
            'is_active' => true
        ]);
        Slider::create([
            'title' => 'Wood Pressed Cold Oils',
            'subtitle' => '100% pure, natural, and chemical-free extractions',
            'image_path' => 'https://images.unsplash.com/photo-1471193945509-9ad0617afabf?auto=format&fit=crop&w=800&q=80',
            'button_text' => 'Explore Oils',
            'button_url' => '/shop?category=cold-pressed-oil',
            'sort_order' => 2,
            'is_active' => true
        ]);
        Slider::create([
            'title' => 'Raw Forest Nectar',
            'subtitle' => 'Directly sourced from organic wild beehives',
            'image_path' => 'https://images.unsplash.com/photo-1587049352851-8d4e89134292?auto=format&fit=crop&w=800&q=80',
            'button_text' => 'Shop Forest Honey',
            'button_url' => '/shop?category=honey',
            'sort_order' => 3,
            'is_active' => true
        ]);

        Banner::create([
            'title' => 'Free Shipping on Orders Above ₹999!',
            'subtitle' => 'Limited period offer for pure and healthy farm fresh products.',
            'image_path' => 'https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&w=1600&q=80',
            'button_text' => 'Shop Now',
            'button_url' => '/shop',
            'position' => 'full_width_banner',
            'is_active' => true
        ]);

        Banner::create([
            'title' => 'Bilona Method',
            'subtitle' => 'Curd Churned traditional health secret.',
            'image_path' => '/assets/images/banners/story-banner-1.jpg',
            'button_text' => 'Read Our Story',
            'button_url' => '/about',
            'position' => 'homepage_promo',
            'is_active' => true
        ]);

        // 10. Coupons
        Coupon::create([
            'code' => 'PURE15',
            'discount_type' => 'percentage',
            'discount_value' => 15.00,
            'min_amount' => 999.00,
            'max_discount' => 300.00,
            'active_from' => now(),
            'active_until' => now()->addMonths(6),
            'usage_limit' => 500,
            'is_active' => true
        ]);
        Coupon::create([
            'code' => 'GHEE100',
            'discount_type' => 'fixed',
            'discount_value' => 100.00,
            'min_amount' => 1499.00,
            'is_active' => true
        ]);

        // 11. Blog Categories & Blogs
        $blogCategory = BlogCategory::create([
            'name' => 'Ayurveda & Health',
            'slug' => 'ayurveda-and-health',
            'description' => 'Explore the ancient roots of traditional diets and natural ingredients.',
            'is_active' => true
        ]);

        Blog::create([
            'blog_category_id' => $blogCategory->id,
            'title' => 'Why Traditional Bilona Churned Ghee is Worth It',
            'slug' => 'why-traditional-bilona-churned-ghee-is-worth-it',
            'author_name' => '',
            'excerpt' => 'Unlike industrial cream-processed ghee, Bilona Cow Ghee involves curd churning, preserving vital butyrate and gut enzymes.',
            'content' => '<p>In traditional households, Ghee was never just an ingredient or a cooking fat. It was treated as a "Rasayana"—a rejuvenator that builds cellular vitality. The magic lies in the traditional Bilona churning process. Modern factory-made ghee is prepared by boiling raw milk cream under high mechanical heat, which rips away delicate micro-nutrients. Bilona Ghee, on the other hand, starts by culturing pure cow milk into whole curd. This curd is then churned slowly using a wooden staff to yield butter, which is gently clarified to produce the rich, granular golden ghee.</p><p>This curd culturing introduces gut-friendly enzymes and generates butyric acid, a short-chain fatty acid that repairs the stomach lining and acts as an anti-inflammatory shield. Thus, investing in pure A2 Vedic Bilona Ghee is not just paying for a fat, it is securing traditional wellness for your family.</p>',
            'featured_image' => '/assets/images/blogs/blog-bilona-ghee.jpg',
            'is_published' => true,
            'published_at' => now(),
            'meta_title' => 'The Science Behind Traditional Bilona Ghee - RohidaFarm',
            'meta_description' => 'Understand the biological advantages of choosing churned curd bilona cow ghee over standard industrial cream ghee.',
            'keywords' => 'bilona ghee benefits, a2 ghee digestion, ayurvedic cooking fat'
        ]);

        // 12. Menus
        Menu::create([
            'name' => 'Main Navigation',
            'location' => 'header',
            'items' => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Shop', 'url' => '/shop', 'children' => [
                    ['title' => 'Cow Ghee', 'url' => '/shop?category=ghee&subcategory=cow-ghee'],
                    ['title' => 'Buffalo Ghee', 'url' => '/shop?category=ghee&subcategory=buffalo-ghee'],
                    ['title' => 'Cold Pressed Oils', 'url' => '/shop?category=cold-pressed-oil'],
                    ['title' => 'Wild Forest Honey', 'url' => '/shop?category=honey'],
                ]],
                ['title' => 'Our Story', 'url' => '/about'],
                ['title' => 'Blogs', 'url' => '/blogs'],
                ['title' => 'Contact Us', 'url' => '/contact']
            ]
        ]);
    }
}
