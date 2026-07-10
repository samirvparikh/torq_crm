@php
    $user = auth()->user();
    $initials = collect(explode(' ', $user->name))->map(fn ($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
@endphp
<header class="crm-header">
    <a href="{{ route('dashboard') }}" class="crm-brand">
        <div class="crm-brand-icon"><i class="bi bi-infinity"></i></div>
        <span>{{ config('app.name') }}</span>
    </a>

    <div class="crm-header-meta">
        <div class="crm-header-meta-item">
            <span>Company</span>
            <strong>{{ \App\Models\Setting::getValue('company', 'name', config('app.name')) }}</strong>
        </div>
        <div class="crm-header-meta-item">
            <span>Branch</span>
            <strong>Head Office</strong>
        </div>
        <div class="crm-header-meta-item">
            <span>Period</span>
            <strong><span class="crm-period-badge">FY {{ now()->format('Y') }}-{{ now()->addYear()->format('y') }}</span></strong>
        </div>
    </div>

    <div class="crm-header-search">
        <i class="bi bi-search"></i>
        <input type="text" placeholder="Quick Find..." id="crm-quick-find">
    </div>

    <div class="crm-header-actions">
        <a href="#" class="crm-icon-btn" title="Notifications">
            <i class="bi bi-bell"></i>
            <span class="crm-badge-dot">0</span>
        </a>
        <a href="#" class="crm-icon-btn" title="Messages">
            <i class="bi bi-chat-dots"></i>
            <span class="crm-badge-dot">0</span>
        </a>
        <a href="{{ route('tasks.index') }}" class="crm-icon-btn" title="Tasks">
            <i class="bi bi-check2-square"></i>
        </a>
        <a href="{{ route('profile.edit') }}" class="crm-user-avatar" title="{{ $user->name }}">{{ $initials }}</a>
    </div>
</header>
