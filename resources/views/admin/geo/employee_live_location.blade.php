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

    const customIcon = L.divIcon({
        className: '',
        html: `
            <div style="position: relative; width: 40px; height: 60px;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="60" height="90" fill="red">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 4.25 4.77 10.17 6.44 12.38a1 1 0 0 0 1.56 0C14.23 19.17 19 13.25 19 9c0-3.87-3.13-7-7-7zm0 15c-1.76-2.16-5-6.5-5-8 0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.5-3.24 5.84-5 8zm0-10.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5z"></path>
                </svg>
                <div style="position: absolute; top: 22px; left: 15px; width: 30px; height: 30px; border-radius: 50%; overflow: hidden;">
                    <img src="{{ $user->profile_img }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            </div>
        `,
        iconSize: [40, 60],
        iconAnchor: [20, 60],
        popupAnchor: [0, -60]
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
