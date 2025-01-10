@extends('admin.layouts.app')

@section('header-css')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
@endsection

@section('title', $page_heading)

@section('main')
<div id="map" style="height: 89vh; width: 100%;"></div>
@endsection

@section('footer-script')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<script>
    const empCode = "{{ $user->emp_code }}";
    let map = null;
    let marker = null;

    const customIcon = L.icon({
        iconUrl: '{{ $user->profile_img }}',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
    });

    const fetchLatestLocation = () => {
        $.ajax({
            url: "{{ route('admin.geo.get_last_location_ajax', ':emp_code') }}".replace(':emp_code', empCode),
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                const { latitude, longitude, recorded_at } = response;

                // Format the recorded_at timestamp using Moment.js
                const formattedTime = moment(recorded_at).format('MMMM Do YYYY, h:mm:ss a');

                if (!map) {
                    map = L.map('map').setView([latitude, longitude], 13);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://weddingbanquets.in" target="_blank">Wedding Banquets</a>'
                    }).addTo(map);

                    marker = L.marker([latitude, longitude], { icon: customIcon }).addTo(map)
                        .bindPopup(`Updated location for {{ $user->name }} <br> Battery: 45% <br> Last location at ${formattedTime}`)
                        .openPopup();
                } else {
                    if (marker) {
                        marker.setLatLng([latitude, longitude])
                            .bindPopup(`Updated location for {{ $user->name }} <br> Battery: 45% <br> Last location at ${formattedTime}`)
                            .openPopup();
                    } else {
                        marker = L.marker([latitude, longitude], { icon: customIcon }).addTo(map)
                            .bindPopup(`Updated location for {{ $user->name }} <br> Battery: 45% <br> Last location at ${formattedTime}`)
                            .openPopup();
                    }
                }
            },
            error: function(error) {
                console.error('Error fetching location:', error);
            }
        });
    };

    fetchLatestLocation();
    setInterval(fetchLatestLocation, 5000);
</script>
@endsection
