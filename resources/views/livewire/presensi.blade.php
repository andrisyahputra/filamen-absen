<div>
    <div class="container mx-auto max-w-sm">
        <div class="bg-white p-6 ronuded-lg mt-3 shadow-lg">
            <div class="grid grid-cols-1  gap-6 mb-6">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Informasi Pegawai</h2>
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <p class="mb-2"><strong>Nama Pegawai : </strong>{{ Auth::user()->name }}</p>
                        <p><strong>Kantor : </strong>{{ $jadwal->office->nama }}</p>
                        <p><strong>Shift : </strong>{{ $jadwal->shift->nama }} ({{ $jadwal->shift->mulai_waktu }} -
                            {{ $jadwal->shift->akhir_waktu }})</p>
                        @if ($jadwal->is_wfa)
                            <p class="text-green-500"><strong>Kantor : </strong>WFA</p>
                        @else
                            <p><strong>Kantor : </strong>WFO</p>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                        <div class="bg-gray-100 p-4 rounded-l">
                            <h4 class="text-l font-bold mb-2">Waktu Datang</h4>
                            <p><strong>{{ $kehadiran->start_time ?? '-' }}</strong></p>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-l">
                            <h4 class="text-l font-bold mb-2">Waktu Pulang</h4>
                            <p><strong>{{ $kehadiran->end_time ?? '-' }}</strong></p>

                        </div>
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl font-bold mb-2">Presensi</h2>
                    <div id="map" class="mb-4 rounded-lg border border-grey-300" wire:ignore></div>
                    @if(session()->has('error'))
                        <div style="color: red; padding: 10px; border: 1px solid red; background-color: white">
                            {{session('error')}}
                        </div>
                    @endif

                    <form class="row g-3 mt-3" wire:submit='store' enctype="multipart/form-data">

                        <button type="button" onclick="tagLokasi()"
                            class="px-4 py-2 bg-blue-500 text-white rounded">Tag
                            Lokasi</button>
                        @if ($insideRadius)
                            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded">Submit
                                Presensi</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        let map;
        let lat;
        let lng;
        let marker;
        let component;
        const kantor = [{{ $jadwal->office->latitude }}, {{ $jadwal->office->longitude }}];
        const radius = {{ $jadwal->office->radius }};

        document.addEventListener('livewire:initialized', function() {
            component = @this;
            map = L.map('map').setView([{{ $jadwal->office->latitude }}, {{ $jadwal->office->longitude }}], 17);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);



            const circle = L.circle(kantor, {
                color: 'red',
                fillColor: '#F03',
                fillOpacity: 0.5,
                radius: radius
            }).addTo(map);

        })


        function tagLokasi() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    lat = position.coords.latitude;
                    lng = position.coords.longitude;

                    if (marker) {
                        map.removeLayer(marker);
                    }
                    marker = L.marker([lat, lng]).addTo(map);
                    map.setView([lat, lng], 15);

                    if (isWithinRadius(lat, lng, kantor, radius)) {
                        component.set('insideRadius', true);
                        component.set('latitude', lat);
                        component.set('longitude', lng);
                    }
                })

            } else {
                alert('Tidak bisa get lokasi');
            }

        }

        function isWithinRadius(lat, lng, kantor, radius) {
             const is_wfa = {{ $jadwal->is_wfa }};
                if (is_wfa) {
                    return true;
                } else {
                     let distance = map.distance([lat, lng], kantor);
            return distance <= radius;
                }

        }
    </script>
</div>
