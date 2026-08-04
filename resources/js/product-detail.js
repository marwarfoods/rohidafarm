// =================================================
// Product Detail Page – Main Entry Point
// Imports and bootstraps all feature modules.
// resources/js/product-detail.js
// =================================================

import { initGallery  } from './product-detail/gallery.js';
import { initSwipers  } from './product-detail/swipers.js';
import { initVariants } from './product-detail/variants.js';
import { initCart     } from './product-detail/cart.js';
import { initReviews  } from './product-detail/reviews.js';
import { initCoupons  } from './product-detail/coupons.js';
import { initDirectBuy } from './product-detail/direct-buy.js';

document.addEventListener('DOMContentLoaded', function () {
    initGallery();
    initSwipers();
    initVariants();
    initCart();
    initReviews();
    initCoupons();
    initDirectBuy();
});
