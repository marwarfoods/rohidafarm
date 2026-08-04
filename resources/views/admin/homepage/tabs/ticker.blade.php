<div class="row g-4">
    <!-- Add New Ticker point -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold font-heading text-dark mb-3">Add New Ticker Point</h5>
            <p class="text-muted mb-4" style="font-size: 0.85rem;">Create a text announcement for the top sliding ticker bar, choosing from curated premium icons.</p>

            <form action="{{ route('admin.homepage.ticker.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Ticker Text <span class="text-danger">*</span></label>
                    <input type="text" name="text" class="form-control" placeholder="e.g. Free Shipping above ₹999" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Select Ticker Icon <span class="text-danger">*</span></label>
                    <select name="icon_class" class="form-select font-heading fw-medium" required>
                        <option value="bi bi-truck">🚚 Shipping (Truck Icon)</option>
                        <option value="bi bi-patch-check">✅ Verified (Patch Check Icon)</option>
                        <option value="bi bi-wallet2">💳 Cash On Delivery (Wallet Icon)</option>
                        <option value="bi bi-award">🏅 Premium Quality (Award Icon)</option>
                        <option value="bi bi-shield-check">🛡️ Secure (Shield Icon)</option>
                        <option value="bi bi-flower1">🌱 Organic/Pure (Flower Icon)</option>
                        <option value="bi bi-check-circle">✔️ Quality Check (Check Circle Icon)</option>
                        <option value="bi bi-star">⭐ Top Rated (Star Icon)</option>
                        <option value="bi bi-lightning">⚡ Fast Delivery (Lightning Icon)</option>
                        <option value="bi bi-tag">🏷️ Special Offers (Tag Icon)</option>
                        <option value="bi bi-box2-heart">🎁 Handmade/Gifts (Box Heart Icon)</option>
                        <option value="bi bi-percent">٪ Discounts (Percent Icon)</option>
                        <option value="bi bi-cart">🛒 Shopping (Cart Icon)</option>
                        <option value="bi bi-heart">❤️ Love (Heart Icon)</option>
                        <option value="bi bi-arrow-repeat">🔄 Easy Returns (Arrow Repeat Icon)</option>
                        <option value="bi bi-chat-right-heart">💬 Help & Support (Chat Icon)</option>
                        <option value="bi bi-globe">🌍 Eco Friendly (Globe Icon)</option>
                        <option value="bi bi-tree">🌲 Natural Nature (Tree Icon)</option>
                        <option value="bi bi-egg-fried">🍳 Traditional Method (Egg/Cook Icon)</option>
                        <option value="bi bi-hand-thumbs-up">👍 Trusted Choice (Thumb Icon)</option>
                        <option value="bi bi-cash-coin">🪙 Best Prices (Coins Icon)</option>
                        <option value="bi bi-gem">💎 Premium Quality (Gem Icon)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2.5 rounded-3 fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px; background-color: var(--primary-green) !important; border-color: var(--primary-green) !important;">
                    <i class="bi bi-plus-circle me-1"></i> Add Ticker Point
                </button>
            </form>
        </div>
    </div>

    <!-- Existing Tickers List -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold font-heading text-dark m-0">Active Ticker Announcements</h5>
                <span class="badge bg-success-subtle text-success py-1 px-2.5 rounded-pill" style="font-size: 0.7rem;"><i class="bi bi-info-circle me-1"></i> Drag to Reorder</span>
            </div>
            <p class="text-muted mb-4" style="font-size: 0.85rem;">These announcements slide horizontally in a continuous loop at the absolute top of the website. Drag to rearrange their priority.</p>

            @if($tickers->isEmpty())
                <div class="text-center py-5 border rounded-4 bg-light" style="border-style: dashed !important; border-color: #ECE7DD !important;">
                    <i class="bi bi-megaphone text-muted display-4 d-block mb-3"></i>
                    <h6 class="fw-bold text-dark m-0">No ticker announcements found</h6>
                    <p class="text-muted mb-0" style="font-size: 0.85rem;">Use the left panel to add your first ticker announcement.</p>
                </div>
            @else
                <div id="sortable-ticker-list" class="list-group rounded-4 overflow-hidden border" style="border-color: #ECE7DD !important;">
                    @foreach($tickers as $ticker)
                        <div class="list-group-item list-group-item-action ticker-item p-3 border-bottom d-flex align-items-center justify-content-between" data-id="{{ $ticker->id }}" style="cursor: grab; border-color: #ECE7DD !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="drag-handle text-muted" style="font-size: 1.25rem;">
                                    <i class="bi bi-grip-vertical"></i>
                                </div>
                                <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="{{ $ticker->icon_class }} fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark m-0" style="font-size: 0.9rem;">{{ $ticker->text }}</h6>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;"><i class="bi bi-tag-fill me-0.5"></i> Icon class: {{ $ticker->icon_class }}</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <form action="{{ route('admin.homepage.ticker.delete', $ticker->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this ticker point?');" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Ticker">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
