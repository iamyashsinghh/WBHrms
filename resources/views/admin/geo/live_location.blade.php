@extends('admin.layouts.app')

@section('header-css')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
@endsection

@section('title', 'All Employees Live Location')

@section('main')
<div id="map" style="height: 89vh; width: 100%;"></div>
@endsection

@section('footer-script')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<script>
    let map = null;
    const customIcon = (profileImg) => L.divIcon({
        className: '',
        html: `
            <div style="position: relative; width: 40px; height: 60px;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="60" height="90" fill="#891010">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 4.25 4.77 10.17 6.44 12.38a1 1 0 0 0 1.56 0C14.23 19.17 19 13.25 19 9c0-3.87-3.13-7-7-7zm0 15c-1.76-2.16-5-6.5-5-8 0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.5-3.24 5.84-5 8zm0-10.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5z"></path>
                </svg>
                <div style="position: absolute; top: 22px; left: 15px; width: 30px; height: 30px; border-radius: 50%; overflow: hidden;">
                    <img src="${profileImg}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            </div>
        `,
        iconSize: [40, 60],
        iconAnchor: [20, 60],
        popupAnchor: [0, -60]
    });

    const fetchAllLocations = () => {
        $.ajax({
            url: "{{ route('admin.geo.get_all_locations_ajax') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (!map) {
                    map = L.map('map').setView([28.6139, 77.2090], 5);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://weddingbanquets.in" target="_blank">Wedding Banquets</a>'
                    }).addTo(map);
                }

                response.forEach(location => {
                    if (location.latitude && location.longitude) {
                        const formattedTime = moment(location.recorded_at).format('MMMM Do YYYY, h:mm:ss a');
                        const marker = L.marker([location.latitude, location.longitude], { icon: customIcon(location.profile_img) }).addTo(map);
                        marker.bindPopup(`
                            <b>${location.emp_code}</b>
                            <br> Battery: 45%
                            <br> Recorded at: ${formattedTime}
                        `);
                    }
                });
            },
            error: function(error) {
                console.error('Error fetching locations:', error);
            }
        });
    };

    fetchAllLocations();
    setInterval(fetchAllLocations, 5000);
</script>
@endsection
