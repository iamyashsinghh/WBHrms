@extends('admin.layouts.app')

@section('header-css')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
@endsection

@section('title', $page_heading)

@section('main')
<div id="map" style="height: 87vh; width: 100%;"></div>
@endsection

@section('footer-script')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> <!-- Include jQuery -->

<script>
    const empCode = "{{ $user->emp_code }}";
    let map, marker;
    const initializeMap = (latitude, longitude) => {
        map = L.map('map').setView([latitude, longitude], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://weddingbanquets.in" target="_blank">Wedding Banquets</a>'
        }).addTo(map);
        marker = L.marker([latitude, longitude]).addTo(map)
            .bindPopup(`Location for {{ $user->name }}`)
            .openPopup();
    };
    const fetchLatestLocation = () => {
        $.ajax({
            url: "{{ route('admin.geo.get_last_location_ajax', ':emp_code') }}".replace(':emp_code', empCode),
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                const { latitude, longitude } = response;

                if (marker) {
                    map.removeLayer(marker);
                }

                marker = L.marker([latitude, longitude]).addTo(map)
                    .bindPopup(`Updated location for {{ $user->name }}`)
                    .openPopup();

                map.setView([latitude, longitude], 20);
            },
            error: function(error) {
                console.error('Error fetching location:', error);
            }
        });
    };
    const initialLatitude = {{ $user->latitude ?? 51.505 }};
    const initialLongitude = {{ $user->longitude ?? -0.09 }};
    initializeMap(initialLatitude, initialLongitude);
    setInterval(fetchLatestLocation, 1000);
</script>
@endsection
