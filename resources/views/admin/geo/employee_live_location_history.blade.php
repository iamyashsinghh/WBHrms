@extends('admin.layouts.app')

@section('header-css')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
@endsection

@section('title', $page_heading)

@section('main')
<div id="map" style="height: 85vh; width: 100%;"></div>
<div class="content-wrapper">
    <div style="position: relative; z-index: 1000; padding: 10px; background: #fff;">
        <!-- Date Picker -->
        <input type="date" id="datePicker" class="form-control" style="width: 200px; display: inline-block;" />

        <!-- Play/Pause Button -->
        <button id="playPauseBtn" class="btn btn-primary">Play</button>

        <!-- Progress Bar -->
        <div style="width: 50%; display: inline-block; margin-left: 10px; vertical-align: middle;">
            <input type="range" id="progressBar" class="form-range" min="0" max="100" value="0" style="width: 100%;" />
        </div>

        <!-- Speed Select (1x to 10x) -->
        <select id="speedSelect" class="form-control" style="width: 100px; display: inline-block; margin-left: 10px;">
            <option value="1">1x</option>
            <option value="2">2x</option>
            <option value="3">3x</option>
            <option value="4">4x</option>
            <option value="5">5x</option>
            <option value="6">6x</option>
            <option value="7">7x</option>
            <option value="8">8x</option>
            <option value="9">9x</option>
            <option value="10">10x</option>
        </select>
    </div>
</div>
@endsection

@section('footer-script')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<script>
    const empCode = "{{ $user->emp_code }}";
    let map = null;
    let marker = null;
    let locations = [];
    let currentIndex = 0;
    let isPlaying = false;
    let playbackInterval = null;
    let playbackSpeed = 1;
    let routeLine = null;

    const customIcon = L.divIcon({
        className: '',
        html: `
            <div style="position: relative; width: 40px; height: 60px;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="60" height="90" fill="#891010">
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

    const fetchLocationsByDate = (date) => {
        $.ajax({
            url: "{{ route('admin.geo.get_location_history_ajax', ':emp_code') }}".replace(':emp_code', empCode),
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                date: date
            },
            success: function (response) {
                if (routeLine) {
                    map.removeLayer(routeLine);
                    routeLine = null;
                }

                locations = response;

                if (locations.length) {
                    initializeMap(locations[0]);
                    drawFullRoute(locations);
                }

                resetPlayback();
            },
            error: function (error) {
                console.error('Error fetching locations:', error);
            }
        });
    };

    const initializeMap = (firstLocation) => {
        if (!map) {
            if (!firstLocation.latitude || !firstLocation.longitude) {
                console.error("Invalid location coordinates:", firstLocation);
                return;
            }
            map = L.map('map').setView([firstLocation.latitude, firstLocation.longitude], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://weddingbanquets.in" target="_blank">Wedding Banquets</a>'
            }).addTo(map);

            marker = L.marker([firstLocation.latitude, firstLocation.longitude], { icon: customIcon }).addTo(map);
        }
    };

    const drawFullRoute = (locationData) => {
        let routeCoordinates = locationData.map(loc => [loc.latitude, loc.longitude]);

        routeLine = L.polyline(routeCoordinates, {
            color: '#891010',
            weight: 2,
            opacity: 0.8,
        }).addTo(map);

        map.fitBounds(routeLine.getBounds());
    };

    const updateMarker = (location) => {
        const formattedTime = moment(location.recorded_at).format('MMMM Do YYYY, h:mm:ss a');
        if (marker) {
            marker.setLatLng([location.latitude, location.longitude])
                .bindPopup(`
                    Updated location for {{ $user->name }}
                    <br> Battery: 45%
                    <br> Recorded at ${formattedTime}
                `)
                .openPopup();
        }
    };

    const playLocations = () => {
    isPlaying = true;
    clearInterval(playbackInterval);

    const animateMarker = (start, end, duration) => {
        let startTime = null;

        const step = (timestamp) => {
            if (!startTime) startTime = timestamp;

            const progress = Math.min((timestamp - startTime) / duration, 1); // Calculate progress (0 to 1)

            // Interpolate latitude and longitude
            const lat = parseFloat(start.latitude) + progress * (parseFloat(end.latitude) - parseFloat(start.latitude));
            const lng = parseFloat(start.longitude) + progress * (parseFloat(end.longitude) - parseFloat(start.longitude));

            // Validate lat and lng before updating the marker
            if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                console.error('Invalid LatLng object:', lat, lng);
                pausePlayback();
                return;
            }

            marker.setLatLng([lat, lng]);

            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                currentIndex++;
                if (currentIndex < locations.length - 1 && isPlaying) {
                    animateMarker(locations[currentIndex], locations[currentIndex + 1], duration);
                } else {
                    pausePlayback();
                }
            }
        };

        requestAnimationFrame(step);
    };

    if (locations.length > 1) {
        animateMarker(locations[currentIndex], locations[currentIndex + 1], 1000 / playbackSpeed);
    }
};



    const pausePlayback = () => {
        isPlaying = false;
        clearInterval(playbackInterval);
    };

    const resetPlayback = () => {
        pausePlayback();
        currentIndex = 0;
        updateProgressBar();
        if (locations.length > 0) {
            updateMarker(locations[0]);
        }
    };

    const updateProgressBar = () => {
    if (locations.length > 0) {
        const progress = (currentIndex / (locations.length - 1)) * 100; // Calculate progress percentage
        $('#progressBar').val(progress);
    } else {
        $('#progressBar').val(0); // Reset progress bar if no locations
    }
};


    $('#datePicker').on('change', (e) => {
        const selectedDate = e.target.value;
        fetchLocationsByDate(selectedDate);
    });

    $('#playPauseBtn').on('click', () => {
        if (isPlaying) {
            pausePlayback();
            $('#playPauseBtn').text('Play');
        } else {
            playLocations();
            $('#playPauseBtn').text('Pause');
        }
    });

    $('#progressBar').on('input', (e) => {
        const progress = e.target.value;
        currentIndex = Math.floor((progress / 100) * locations.length);
        if (locations[currentIndex]) {
            updateMarker(locations[currentIndex]);
        }
    });

    $('#speedSelect').on('change', function() {
        playbackSpeed = parseInt($(this).val(), 10) || 1;
        if (isPlaying) {
            playLocations();
        }
    });
</script>
@endsection
