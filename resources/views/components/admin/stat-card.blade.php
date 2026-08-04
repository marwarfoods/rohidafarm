{{--
  Admin Stat Card Component
  Usage:
    <x-admin.stat-card
        label="Total Sales"
        value="₹1,25,000"
        icon="bi-currency-rupee"
        variant="success"
        sub="+12% this month"
        sub-up="true"
    />
  Variants: success | primary | warning | danger | info | purple
--}}
@props([
    'label'   => 'Stat Label',
    'value'   => '0',
    'icon'    => 'bi-bar-chart',
    'variant' => 'success',
    'sub'     => null,
    'subUp'   => null,
    'subDown' => null,
    'href'    => null,
])

@php
    $tag = $href ? 'a' : 'div';
    $attrs = $href ? "href=\"{$href}\"" : '';
@endphp

<div class="admin-stat-card stat-{{ $variant }}" {{ $href ? 'onclick=window.location.href=\''.$href.'\' style=cursor:pointer' : '' }}>
    <div class="stat-info">
        <span class="stat-label">{{ $label }}</span>
        <div class="stat-value">{{ $value }}</div>
        @if($sub)
            <div class="stat-sub {{ $subUp ? 'up' : ($subDown ? 'down' : '') }}">
                @if($subUp)   <i class="bi bi-arrow-up-right-circle-fill"></i> @endif
                @if($subDown) <i class="bi bi-arrow-down-right-circle-fill"></i> @endif
                {{ $sub }}
            </div>
        @endif
    </div>
    <div class="stat-icon-wrap">
        <i class="bi {{ $icon }}"></i>
    </div>
</div>
