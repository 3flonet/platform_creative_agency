<x-filament-widgets::widget>
    @php
        $data = $this->getStats();
    @endphp

    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); padding: 1.5rem;" class="dark:bg-gray-900 dark:border-white/10">
        <!-- Header -->
        <div style="display: flex; align-items: center; justify-content: justify-between; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-grow: 1;">
                <x-filament::icon
                    icon="heroicon-m-chart-bar-square"
                    style="height: 1.25rem; width: 1.25rem; color: #3b82f6;"
                />
                <h2 style="font-size: 1.125rem; font-weight: 700; color: #111827;" class="dark:text-white">Traffic Insights</h2>
            </div>
            
            <div class="flex items-center gap-2">
                <span style="font-size: 0.75rem; font-weight: 500; color: #6b7280;">Timeframe:</span>
                <select 
                    wire:model.live="period" 
                    style="border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.25rem 2rem 0.25rem 0.75rem; font-size: 0.875rem; background-color: #f9fafb;"
                    class="dark:bg-white/5 dark:text-white dark:border-white/10"
                >
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="7">Last 7 Days</option>
                    <option value="30">Last 30 Days</option>
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="all">All Time</option>
                </select>
                <a href="{{ route('admin.export.traffic', ['period' => $period]) }}" 
                   target="_blank"
                   style="display:inline-flex;align-items:center;gap:0.35rem;padding:0.3rem 0.75rem;background:#2563eb;color:white;border-radius:0.5rem;font-size:0.8rem;font-weight:600;text-decoration:none;"
                   title="Export as CSV">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    CSV
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div style="display: flex; flex-wrap: wrap; gap: 1.5rem;">
            <!-- Unique Visitors Card -->
            <div style="flex: 1; min-width: 250px; background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 1rem; padding: 1.25rem; display: flex; align-items: center; gap: 1rem;" class="dark:bg-white/5 dark:border-white/5">
                <div style="height: 3.5rem; width: 3.5rem; border-radius: 0.75rem; background: #dbeafe; color: #2563eb; display: flex; align-items: center; justify-content: center;" class="dark:bg-blue-500/20 dark:text-blue-400">
                    <x-filament::icon icon="heroicon-s-user-group" style="height: 1.75rem; width: 1.75rem;" />
                </div>
                <div>
                    <div style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Unique Visitors</div>
                    <div style="font-size: 1.875rem; font-weight: 800; color: #0f172a;" class="dark:text-white">{{ number_format($data['visitors']) }}</div>
                    <div style="font-size: 0.625rem; color: #94a3b8; font-weight: 500;">DURING {{ strtoupper($data['label']) }}</div>
                </div>
            </div>

            <!-- Page Views Card -->
            <div style="flex: 1; min-width: 250px; background: #f0fdf4; border: 1px solid #dcfce7; border-radius: 1rem; padding: 1.25rem; display: flex; align-items: center; gap: 1rem;" class="dark:bg-white/5 dark:border-white/5">
                <div style="height: 3.5rem; width: 3.5rem; border-radius: 0.75rem; background: #dcfce7; color: #059669; display: flex; align-items: center; justify-content: center;" class="dark:bg-emerald-500/20 dark:text-emerald-400">
                    <x-filament::icon icon="heroicon-s-eye" style="height: 1.75rem; width: 1.75rem;" />
                </div>
                <div>
                    <div style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Total Page Views</div>
                    <div style="font-size: 1.875rem; font-weight: 800; color: #0f172a;" class="dark:text-white">{{ number_format($data['views']) }}</div>
                    <div style="font-size: 0.625rem; color: #94a3b8; font-weight: 500;">DURING {{ strtoupper($data['label']) }}</div>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
