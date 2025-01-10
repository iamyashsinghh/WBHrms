@section('footer-script')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    const empCode = "{{ $user->emp_code }}";
    let map = null;
    let marker = null;

    const fetchLatestLocation = () => {
        $.ajax({
            url: "{{ route('admin.geo.get_last_location_ajax', ':emp_code') }}".replace(':emp_code', empCode),
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                const { latitude, longitude } = response;

                if (!map) {
                    // Initialize the map only on the first successful fetch
                    map = L.map('map').setView([latitude, longitude], 13);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://weddingbanquets.in" target="_blank">Wedding Banquets</a>'
                    }).addTo(map);
                }

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

    // Fetch location every 5 seconds
    setInterval(fetchLatestLocation, 5000);
</script>
@endsection
