<div wire:ignore class="w-full h-[500px] rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 relative z-0"
     x-data="mapComponent()"
     x-init="initMap()"
>
    <div id="map" class="absolute inset-0 rounded-xl"></div>
</div>

@script
<script>
    Alpine.data('mapComponent', () => ({
        map: null,
        initMap() {
            // Beograd centar
            this.map = L.map('map').setView([44.7866, 20.4489], 13);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(this.map);

            let mapLayers = L.layerGroup().addTo(this.map);

            // Nacrtaj sve podatke na mapi
            this.drawAll(mapLayers);

            // Prati izmene u podacima
            $wire.$watch('issues', () => this.drawAll(mapLayers));
            $wire.$watch('assets', () => this.drawAll(mapLayers));
            $wire.$watch('roads', () => this.drawAll(mapLayers));
            $wire.$watch('accidents', () => this.drawAll(mapLayers));

            // Klik na mapu za dodavanje pribadače (prijavu kvara)
            this.map.on('click', (e) => {
                let lat = e.latlng.lat;
                let lng = e.latlng.lng;
                
                $wire.dispatch('map-clicked', { lat: lat, lng: lng });
            });
        },
        drawAll(layerGroup) {
            layerGroup.clearLayers();

            // 1. CRTANJE DEONICA PUTEVA (POLYLINES)
            let roads = $wire.roads || [];
            roads.forEach(road => {
                if (!road.start_lat || !road.start_lng || !road.end_lat || !road.end_lng) return;

                let roadColor = '#10b981'; // prohodno
                if (road.status === 'radovi') roadColor = '#f59e0b';
                else if (road.status === 'zatvoreno') roadColor = '#ef4444';
                else if (road.status === 'osteceno') roadColor = '#8b5cf6';
                
                let polyline = L.polyline([
                    [road.start_lat, road.start_lng],
                    [road.end_lat, road.end_lng]
                ], {
                    color: roadColor,
                    weight: 6,
                    opacity: 0.8
                });
                
                polyline.bindPopup(`
                    <div class="p-2">
                        <h4 class="font-bold text-sm text-gray-800 dark:text-white">${road.name}</h4>
                        <table class="w-full text-xs mt-2 text-gray-600 dark:text-gray-400">
                            <tr><td class="font-semibold pr-2">Kategorija:</td><td>${road.category === 'autoput' ? 'Autoput' : 'Lokalni put'}</td></tr>
                            <tr><td class="font-semibold pr-2">Dužina:</td><td>${road.length_km} km</td></tr>
                            <tr><td class="font-semibold pr-2">Tip asfalta:</td><td>${road.asphalt_type || 'Nepoznat'}</td></tr>
                            <tr><td class="font-semibold pr-2">Status:</td><td><span class="font-bold uppercase" style="color: ${roadColor}">${road.status}</span></td></tr>
                        </table>
                    </div>
                `);
                polyline.addTo(layerGroup);
            });

            // 2. CRTANJE EVIDENTIRANE IMOVINE (ASSETS)
            let assets = $wire.assets || [];
            assets.forEach(asset => {
                let emoji = '🛑';
                let assetColor = '#3b82f6';
                if (asset.type === 'Semafor') { emoji = '🚦'; assetColor = '#ec4899'; }
                else if (asset.type === 'Most') { emoji = '🌉'; assetColor = '#8b5cf6'; }
                else if (asset.type === 'Horizontalna signalizacija') { emoji = '🛣️'; assetColor = '#14b8a6'; }

                let marker = L.marker([asset.gps_lat, asset.gps_lng], {
                    icon: L.divIcon({
                        html: `<div class="flex items-center justify-center w-8 h-8 rounded-full bg-white shadow-md border-2" style="border-color: ${assetColor}; font-size: 16px;">${emoji}</div>`,
                        className: 'custom-div-icon',
                        iconSize: [32, 32],
                        iconAnchor: [16, 16]
                    })
                });

                let propsHtml = '';
                if (asset.properties) {
                    for (const [key, value] of Object.entries(asset.properties)) {
                        propsHtml += `<tr><td class="font-semibold pr-2">${key}:</td><td>${value}</td></tr>`;
                    }
                }

                marker.bindPopup(`
                    <div class="p-2">
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="text-lg">${emoji}</span>
                            <h4 class="font-bold text-sm text-gray-800 dark:text-white">${asset.type} (#${asset.id})</h4>
                        </div>
                        <table class="w-full text-xs text-gray-600 dark:text-gray-300">
                            <tr><td class="font-semibold pr-2">Status:</td><td class="font-bold text-indigo-600">${asset.status}</td></tr>
                            <tr><td class="font-semibold pr-2">Godina ugradnje:</td><td>${asset.installed_at}</td></tr>
                            <tr><td class="font-semibold pr-2">Garancija do:</td><td>${asset.warranty_until}</td></tr>
                            ${propsHtml}
                        </table>
                    </div>
                `);
                marker.addTo(layerGroup);
            });

            // 3. CRTANJE SAOBRAĆAJNIH NEZGODA (ACCIDENTS / CRNE TAČKE)
            let accidents = $wire.accidents || [];
            accidents.forEach(accident => {
                let severityColor = '#eab308'; // laka
                if (accident.severity === 'teska') severityColor = '#f97316';
                else if (accident.severity === 'fatalna') severityColor = '#ef4444';

                let marker = L.marker([accident.gps_lat, accident.gps_lng], {
                    icon: L.divIcon({
                        html: `<div class="flex items-center justify-center w-8 h-8 rounded-full bg-red-100 shadow-lg border-2 animate-bounce" style="border-color: ${severityColor}; font-size: 16px;">💥</div>`,
                        className: 'custom-div-icon',
                        iconSize: [32, 32],
                        iconAnchor: [16, 16]
                    })
                });

                marker.bindPopup(`
                    <div class="p-2">
                        <div class="flex items-center gap-1.5 mb-2 text-red-600 font-bold text-sm">
                            <span>💥 Saobraćajna nezgoda</span>
                        </div>
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-200 mb-2">${accident.description}</p>
                        <table class="w-full text-xs text-gray-600 dark:text-gray-300">
                            <tr><td class="font-semibold pr-2">Težina:</td><td class="font-bold uppercase" style="color: ${severityColor}">${accident.severity}</td></tr>
                            <tr><td class="font-semibold pr-2">Vreme nezgode:</td><td>${accident.reported_at}</td></tr>
                        </table>
                    </div>
                `);
                marker.addTo(layerGroup);
            });

            // 4. CRTANJE PRIJAVLJENIH PROBLEMA GRAĐANA (ISSUES)
            let issues = $wire.issues || [];
            issues.forEach(issue => {
                let markerColor = '#ef4444'; // prijavljeno
                if (issue.status === 'verifikovano') markerColor = '#f59e0b';
                else if (issue.status === 'nalog_izdat') markerColor = '#eab308';
                
                let marker = L.circleMarker([issue.gps_lat, issue.gps_lng], {
                    color: '#ffffff',
                    fillColor: markerColor,
                    fillOpacity: 0.9,
                    radius: 8,
                    weight: 2
                });
                
                marker.bindPopup(`
                    <div class="p-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">${issue.status}</span>
                        <h4 class="font-bold text-sm text-gray-800 dark:text-white mt-1">${issue.type}</h4>
                        <p class="text-xs text-gray-650 dark:text-gray-350 mt-1">${issue.description}</p>
                    </div>
                `);
                marker.addTo(layerGroup);
            });
        }
    }));
</script>
@endscript
