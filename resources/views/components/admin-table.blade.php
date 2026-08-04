@props([
    'headers' => [],
    'items' => [],
    'title' => '',
    'description' => ''
])

<div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
    @if($title || $description)
        <div class="card-header bg-white border-bottom p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    @if($title)
                        <h4 class="font-heading fw-bold text-dark m-0">{{ $title }}</h4>
                    @endif
                    @if($description)
                        <p class="text-muted m-0 mt-1" style="font-size: 0.85rem;">{{ $description }}</p>
                    @endif
                </div>
                @if(isset($actions))
                    <div>
                        {{ $actions }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle m-0">
            <thead class="bg-light text-muted" style="font-size: 0.85rem; font-family: 'DM Sans', sans-serif;">
                <tr>
                    @foreach($headers as $header)
                        <th scope="col" class="border-0 px-4 py-3">{!! $header !!}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody style="font-size: 0.9rem;">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @if(method_exists($items, 'links') && $items->hasPages())
        <div class="card-footer bg-white border-0 py-3 px-4">
            {{ $items->links() }}
        </div>
    @endif
</div>
