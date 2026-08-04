<?php

namespace App\Repositories\Contracts;

interface ProductRepositoryInterface
{
    public function all();
    
    public function find(int $id);
    
    public function findByUuid(string $uuid);
    
    public function findBySlug(string $slug);
    
    public function getFeatured(int $limit = 4);
    
    public function getTrending(int $limit = 4);
    
    public function getBestSellers(int $limit = 4);
    
    public function getNewArrivals(int $limit = 4);
    
    public function getRelated(int $categoryId, int $excludeId, int $limit = 4);
    
    public function filterAndPaginate(array $filters, int $perPage = 12);
}
