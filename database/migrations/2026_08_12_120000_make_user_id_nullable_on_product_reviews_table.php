<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Guest reviews are stored with user_id = null, but the column was created
        // as NOT NULL with a foreign key, causing a 500 error on every guest review
        // submission. Drop the FK, make the column nullable, and re-add the FK with
        // ON DELETE SET NULL so deleting a user no longer wipes out their reviews.
        $foreignKey = $this->getForeignKeyName('product_reviews', 'user_id');

        if ($foreignKey) {
            DB::statement("ALTER TABLE `product_reviews` DROP FOREIGN KEY `{$foreignKey}`");
        }

        DB::statement('ALTER TABLE `product_reviews` MODIFY `user_id` BIGINT UNSIGNED NULL');

        DB::statement('ALTER TABLE `product_reviews` ADD CONSTRAINT `product_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `product_reviews` DROP FOREIGN KEY `product_reviews_user_id_foreign`');
        DB::statement('DELETE FROM `product_reviews` WHERE `user_id` IS NULL');
        DB::statement('ALTER TABLE `product_reviews` MODIFY `user_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `product_reviews` ADD CONSTRAINT `product_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE');
    }

    private function getForeignKeyName(string $table, string $column): ?string
    {
        $database = DB::getDatabaseName();

        $row = DB::selectOne(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL
             LIMIT 1',
            [$database, $table, $column]
        );

        return $row->CONSTRAINT_NAME ?? null;
    }
};
