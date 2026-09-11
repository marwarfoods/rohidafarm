<!-- Left Sidebar: Folder tree -->
<div class="media-sidebar">
    <h5 class="sidebar-title">Categories</h5>
    <ul class="folder-list">
        <li class="folder-item">
            <a href="{{ route('admin.media.index', ['folder' => 'all']) }}" class="folder-link {{ $folder === 'all' ? 'active' : '' }}">
                <span><i class="bi bi-images"></i>All Media</span>
                <span class="folder-badge">{{ $counts['all'] }}</span>
            </a>
        </li>
        <li class="folder-item">
            <a href="{{ route('admin.media.index', ['folder' => 'product-images']) }}" class="folder-link {{ $folder === 'product-images' ? 'active' : '' }}">
                <span><i class="bi bi-folder-fill"></i>Product Images</span>
                <span class="folder-badge">{{ $counts['product-images'] }}</span>
            </a>
        </li>
        <li class="folder-item">
            <a href="{{ route('admin.media.index', ['folder' => 'sliders']) }}" class="folder-link {{ $folder === 'sliders' ? 'active' : '' }}">
                <span><i class="bi bi-folder-fill"></i>Sliders</span>
                <span class="folder-badge">{{ $counts['sliders'] }}</span>
            </a>
        </li>
        <li class="folder-item">
            <a href="{{ route('admin.media.index', ['folder' => 'reviews']) }}" class="folder-link {{ $folder === 'reviews' ? 'active' : '' }}">
                <span><i class="bi bi-folder-fill"></i>Reviews</span>
                <span class="folder-badge">{{ $counts['reviews'] }}</span>
            </a>
        </li>
        <li class="folder-item">
            <a href="{{ route('admin.media.index', ['folder' => 'settings']) }}" class="folder-link {{ $folder === 'settings' ? 'active' : '' }}">
                <span><i class="bi bi-folder-fill"></i>Settings</span>
                <span class="folder-badge">{{ $counts['settings'] }}</span>
            </a>
        </li>
        <li class="folder-item">
            <a href="{{ route('admin.media.index', ['folder' => 'direct-urls']) }}" class="folder-link {{ $folder === 'direct-urls' ? 'active' : '' }}">
                <span><i class="bi bi-globe2 text-primary"></i>Direct URLs</span>
                <span class="folder-badge">{{ $counts['direct-urls'] ?? 0 }}</span>
            </a>
        </li>
        <li class="folder-item">
            <a href="{{ route('admin.media.index', ['folder' => 'unsplash']) }}" class="folder-link {{ $folder === 'unsplash' ? 'active' : '' }}">
                <span><i class="bi bi-folder-fill"></i>From Unsplash</span>
                <span class="folder-badge">{{ $counts['unsplash'] }}</span>
            </a>
        </li>
        <li class="folder-item">
            <a href="{{ route('admin.media.index', ['folder' => 'videos']) }}" class="folder-link {{ $folder === 'videos' ? 'active' : '' }}">
                <span><i class="bi bi-folder-fill"></i>Videos</span>
                <span class="folder-badge">{{ $counts['videos'] }}</span>
            </a>
        </li>
        <li class="folder-item">
            <a href="{{ route('admin.media.index', ['folder' => 'built-in']) }}" class="folder-link {{ $folder === 'built-in' ? 'active' : '' }}" title="Built-in app images shipped with the site — read only">
                <span><i class="bi bi-box-seam"></i>Built-in Images</span>
                <span class="folder-badge">{{ $counts['built-in'] }}</span>
            </a>
        </li>
        <li class="folder-item mt-4">
            <a href="{{ route('admin.media.index', ['folder' => 'trash']) }}" class="folder-link {{ $folder === 'trash' ? 'active' : '' }}">
                <span><i class="bi bi-trash-fill text-muted"></i>Trash</span>
                <span class="folder-badge">0</span>
            </a>
        </li>
    </ul>
</div>
