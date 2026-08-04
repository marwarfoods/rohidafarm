<?php

namespace App\Repositories\Contracts;

interface CategoryRepositoryInterface
{
    public function allActive();
    
    public function findBySlug(string $slug);
    
    public function getWithSubcategories();
}
