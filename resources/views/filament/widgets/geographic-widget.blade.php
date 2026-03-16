<x-filament-widgets::widget>
    @php
        $data = $this->getViewData();
        $countries = $data['countries'];
        
        $mapData = [];
        foreach($countries as $c) {
            $mapData[strtoupper($c['code'])] = $c['visits'];
        }
    @endphp

    <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); padding: 1.5rem;" class="dark:bg-gray-900 dark:border-white/10" wire:key="geo-widget-{{ $this->period }}">
        <!-- Header -->
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
            <x-filament::icon icon="heroicon-m-globe-americas" style="height: 1.25rem; width: 1.25rem; color: #3b82f6;" />
            <h2 style="font-size: 1.125rem; font-weight: 700; color: #111827;" class="dark:text-white">Geographic Origin</h2>
        </div>

        <div style="display: flex; flex-direction: column; gap: 2rem;">
            
            <!-- MAP DISPLAY -->
            <div style="position: relative; width: 100%; height: 350px; background: #f8fafc; border-radius: 0.5rem; overflow: hidden;" class="dark:bg-white/5">
                <div id="traffic-map-{{ $this->period }}" style="width: 100%; height: 100%;" 
                    x-data="{
                        mapData: {{ json_encode((object)$mapData) }},
                        loadScript(src) {
                            return new Promise((resolve) => {
                                if (document.querySelector(`script[src='${src}']`)) {
                                    resolve();
                                    return;
                                }
                                let script = document.createElement('script');
                                script.src = src;
                                script.onload = resolve;
                                document.head.appendChild(script);
                            });
                        },
                        async initMap() {
                            if (typeof jsVectorMap === 'undefined') {
                                if (!document.querySelector(`link[href*='jsvectormap']`)) {
                                    let link = document.createElement('link');
                                    link.rel = 'stylesheet';
                                    link.href = 'https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css';
                                    document.head.appendChild(link);
                                }
                                await this.loadScript('https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js');
                                await this.loadScript('https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js');
                            }
                            
                            let mapEl = document.getElementById('traffic-map-{{ $this->period }}');
                            if (!mapEl) return;
                            mapEl.innerHTML = ''; 
                            
                            // Prevent black map bug if there's no data
                            let hasData = Object.keys(this.mapData).length > 0;
                            let visualData = hasData ? this.mapData : { 'XX': 0 };

                            new jsVectorMap({
                                selector: '#traffic-map-{{ $this->period }}',
                                map: 'world',
                                zoomOnScroll: true,
                                visualizeData: {
                                    scale: ['#e2e8f0', '#3b82f6'],
                                    values: visualData
                                },
                                regionStyle: {
                                    initial: { fill: '#e2e8f0', stroke: 'none' },
                                    hover: { fillOpacity: 0.8 }
                                },
                                onRegionTooltipShow: (event, tooltip, code) => {
                                    let visits = hasData ? (this.mapData[code] || 0) : 0;
                                    if (visits === 0) {
                                        event.preventDefault(); // Don't show tooltip for empty countries
                                        return;
                                    }
                                    tooltip.text(`<span>${tooltip.text()}</span><br/><span style='font-weight: 700;'>${visits} visits</span>`, true);
                                }
                            });
                        }
                    }"
                    x-init="initMap()"
                ></div>
            </div>

            <!-- DATA TABLES (Countries / Cities Toggle) -->
            <div x-data="{ tab: 'countries' }">
                <!-- Tabs -->
                <div style="display: flex; gap: 1rem; border-bottom: 1px solid #e5e7eb; margin-bottom: 1rem;" class="dark:border-white/10">
                    <button @click="tab = 'countries'" :style="tab === 'countries' ? 'color: #3b82f6; border-bottom: 2px solid #3b82f6;' : 'color: #6b7280;'" style="padding: 0.5rem 0.25rem; font-weight: 600; font-size: 0.875rem;" class="dark:text-white">
                        Countries
                    </button>
                    <button @click="tab = 'cities'" :style="tab === 'cities' ? 'color: #3b82f6; border-bottom: 2px solid #3b82f6;' : 'color: #6b7280;'" style="padding: 0.5rem 0.25rem; font-weight: 600; font-size: 0.875rem;" class="dark:text-white">
                        Cities
                    </button>
                </div>

                <!-- COUNTRIES TABLE -->
                <div x-show="tab === 'countries'">
                    <table style="width: 100%; text-align: left; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid #e5e7eb; color: #6b7280; font-size: 0.75rem; text-transform: uppercase;" class="dark:border-white/10 dark:text-gray-400">
                                <th style="padding: 0.75rem 0;">Country</th>
                                <th style="padding: 0.75rem 0; text-align: right;">Visitors</th>
                                <th style="padding: 0.75rem 0; text-align: right;">% Total</th>
                                <th style="padding: 0.75rem 0; text-align: right;">Chart</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($countries as $country)
                                <tr style="border-bottom: 1px solid #f3f4f6;" class="dark:border-white/5">
                                    <td style="padding: 0.75rem 0; display: flex; align-items: center; gap: 0.75rem;">
                                        <img src="{{ $country['flag'] }}" width="20" height="15" alt="{{ $country['name'] }}" style="border-radius: 2px; box-shadow: 0 0 2px rgba(0,0,0,0.1);">
                                        <span style="font-weight: 500; color: #111827;" class="dark:text-white">{{ $country['name'] }}</span>
                                    </td>
                                    <td style="padding: 0.75rem 0; text-align: right; color: #4b5563;" class="dark:text-gray-300">
                                        {{ number_format($country['visits']) }}
                                    </td>
                                    <td style="padding: 0.75rem 0; text-align: right; color: #4b5563;" class="dark:text-gray-300">
                                        {{ $country['percentage'] }}%
                                    </td>
                                    <td style="padding: 0.75rem 0; width: 100px;">
                                        <div style="background: #e5e7eb; border-radius: 999px; height: 6px; width: 100%; overflow: hidden;" class="dark:bg-white/10">
                                            <div style="background: #3b82f6; height: 100%; width: {{ $country['percentage'] }}%;"></div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="padding: 1.5rem 0; text-align: center; color: #6b7280;" class="dark:text-gray-400">
                                        No geographic data available for this period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- CITIES TABLE -->
                <div x-show="tab === 'cities'" style="display: none;">
                    <table style="width: 100%; text-align: left; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid #e5e7eb; color: #6b7280; font-size: 0.75rem; text-transform: uppercase;" class="dark:border-white/10 dark:text-gray-400">
                                <th style="padding: 0.75rem 0;">City</th>
                                <th style="padding: 0.75rem 0; text-align: right;">Visitors</th>
                                <th style="padding: 0.75rem 0; text-align: right;">% Total</th>
                                <th style="padding: 0.75rem 0; text-align: right;">Chart</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['cities'] as $city)
                                <tr style="border-bottom: 1px solid #f3f4f6;" class="dark:border-white/5">
                                    <td style="padding: 0.75rem 0; display: flex; align-items: center; gap: 0.75rem;">
                                        <img src="{{ $city['flag'] }}" width="20" height="15" alt="{{ $city['country_code'] }}" style="border-radius: 2px; box-shadow: 0 0 2px rgba(0,0,0,0.1);">
                                        <span style="font-weight: 500; color: #111827;" class="dark:text-white">{{ $city['name'] }} <span style="color: #9ca3af; font-size: 0.75rem;">{{ $city['country_code'] }}</span></span>
                                    </td>
                                    <td style="padding: 0.75rem 0; text-align: right; color: #4b5563;" class="dark:text-gray-300">
                                        {{ number_format($city['visits']) }}
                                    </td>
                                    <td style="padding: 0.75rem 0; text-align: right; color: #4b5563;" class="dark:text-gray-300">
                                        {{ $city['percentage'] }}%
                                    </td>
                                    <td style="padding: 0.75rem 0; width: 100px;">
                                        <div style="background: #e5e7eb; border-radius: 999px; height: 6px; width: 100%; overflow: hidden;" class="dark:bg-white/10">
                                            <div style="background: #3b82f6; height: 100%; width: {{ $city['percentage'] }}%;"></div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="padding: 1.5rem 0; text-align: center; color: #6b7280;" class="dark:text-gray-400">
                                        No city data available.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</x-filament-widgets::widget>
