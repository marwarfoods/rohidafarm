<!-- Skeleton Loader Overlay (for real-time updates) -->
<div id="cartOffcanvasSkeleton" class="position-absolute top-0 start-0 w-100 h-100 bg-white d-none flex-column p-4" style="z-index: 10; transition: opacity 0.2s ease;">
    <!-- Items Skeleton Container -->
    <div class="cart-items-skeleton flex-grow-1 overflow-y-auto mb-3">
        @for($s = 0; $s < 3; $s++)
            <!-- Skeleton Row for single Cart Item -->
            <div class="d-flex gap-3 mb-3 pb-3 border-bottom align-items-center w-100">
                <!-- Thumbnail image placeholder -->
                <div class="skeleton-block" style="width: 65px; height: 65px; border-radius: 8px; flex-shrink: 0;"></div>
                
                <!-- Info placeholders -->
                <div class="flex-grow-1">
                    <!-- Title line -->
                    <div class="skeleton-block mb-2" style="height: 14px; width: 85%;"></div>
                    <!-- Attribute line (e.g. weight) -->
                    <div class="skeleton-block mb-2" style="height: 10px; width: 35%;"></div>
                    
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <!-- Price line -->
                        <div class="skeleton-block" style="height: 12px; width: 30%;"></div>
                        <!-- Quantity selector box -->
                        <div class="skeleton-block" style="height: 24px; width: 75px; border-radius: 20px;"></div>
                    </div>
                </div>
                
                <!-- Delete icon placeholder (Trash Bin icon layout) -->
                <div class="skeleton-block ms-1" style="width: 24px; height: 24px; border-radius: 6px; flex-shrink: 0;"></div>
            </div>
        @endfor
    </div>

    <!-- Totals Summary & Actions Skeleton Footer (Matches real footer structure) -->
    <div class="border-top pt-3 bg-white p-3 rounded-4 border shadow-sm mt-auto w-100">
        <!-- Subtotal line -->
        <div class="d-flex justify-content-between mb-2">
            <div class="skeleton-block" style="height: 12px; width: 35%;"></div>
            <div class="skeleton-block" style="height: 12px; width: 25%;"></div>
        </div>
        <!-- Estimated total line -->
        <div class="d-flex justify-content-between mb-3">
            <div class="skeleton-block" style="height: 16px; width: 45%;"></div>
            <div class="skeleton-block" style="height: 16px; width: 30%;"></div>
        </div>
        <!-- View Cart & Checkout Button placeholders -->
        <div class="d-flex gap-2 mt-2">
            <div class="skeleton-block" style="flex: 1; height: 38px; border-radius: 50px;"></div>
            <div class="skeleton-block" style="flex: 1; height: 38px; border-radius: 50px;"></div>
        </div>
    </div>
</div>
