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
            <option value="4">4x</option>
            <option value="6">6x</option>
            <option value="8">8x</option>
            <option value="10">10x</option>
            <option value="12">12x</option>
            <option value="15">15x</option>
            <option value="20">20x</option>
            <option value="25">25x</option>
        </select>
    </div>
</div>
@endsection

@section('footer-script')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<script>
    // Global Variables
    const empCode = "{{ $user->emp_code }}";
    let map = null;
    let marker = null;
    let locations = [];
    let currentIndex = 0;
    let isPlaying = false;
    let playbackInterval = null;
    let playbackSpeed = 1; // default to 1x
    let routeLine = null;  // polyline to show full route

    // Custom Icon
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
        <div style="display: flex; justify-content: space-between; align-items: center; width: 300px;">
        <!-- Employee Information -->
        <div style="flex: 1; padding-right: 10px; border-right: 1px solid #ddd;">
            <b>${location.employee_name}</b>
            <br> Recorded at: ${formattedTime}
            <br> Attendance: ${location.attendance_status ? location.attendance_status : 'N/A'}
            <br> Punch In at: ${location.punch_in_time ? moment(location.punch_in_time, 'HH:mm:ss').format('h:mm:ss a') : 'N/A'}
            <br> Punch Out at: ${location.punch_out_time ? moment(location.punch_out_time, 'HH:mm:ss').format('h:mm:ss a') : 'N/A'}
            <br> Zoom: <button
            style="background: none; border: none; color: #891010; cursor: pointer; margin-left: 10px;"
            onclick="zoomToLocation(${location.latitude}, ${location.longitude})"
            title="Zoom to Location">
            <i class="fas fa-search-plus"></i>
        </button>
        </div>
        <!-- Battery Information -->
        <div style="flex: 1; text-align: center; padding-left: 10px;">
            <div style="position: relative; width: 60px; height: 120px; background: #ddd; border-radius: 10px; overflow: hidden; margin: auto;">
                <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: ${location.battery_level * 100}%; background: ${getBatteryColor(location.battery_level, location.battery_status)};"></div>
            </div>
            <div style="margin-top: 10px; font-size: 14px; display: flex; align-items: center; justify-content: center; gap: 5px;">
                ${getBatteryIcon(location.battery_status)}
                ${Math.round(location.battery_level * 100)}% (${location.battery_status})
            </div>
        </div>
    </div>
    `).openPopup();
        }
    };

    const playLocations = () => {
        isPlaying = true;
        clearInterval(playbackInterval);

        const intervalDuration = 1000 / playbackSpeed;

        playbackInterval = setInterval(() => {
            if (currentIndex < locations.length) {
                updateMarker(locations[currentIndex]);
                currentIndex++;
                updateProgressBar();
            } else {
                pausePlayback();
            }
        }, intervalDuration);
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
        const progress = locations.length ? (currentIndex / locations.length) * 100 : 0;
        $('#progressBar').val(progress);
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

    function getBatteryColor(level, status) {
    if (status === 'charging') {
        return '#76c7c0';
    } else if (status === 'full') {
        return '#4caf50';
    } else if (status === 'unplugged') {
        if (level > 0.8) return '#4caf50';
        else if (level > 0.5) return '#ffeb3b';
        else if (level > 0.2) return '#ffa726';
        else return '#f44336';
    } else {
        return '#9e9e9e';
    }
}

function getBatteryIcon(status) {
    const iconStyles = 'width: 20px; height: 20px;';
    if (status === 'charging') {
        return `<img src="https://cdn-icons-png.flaticon.com/512/833/833472.png" style="${iconStyles}" alt="Charging Icon" />`; // Charging icon
    } else if (status === 'full') {
        return `<img src="https://cdn-icons-png.flaticon.com/512/833/833480.png" style="${iconStyles}" alt="Full Battery Icon" />`; // Full icon
    } else if (status === 'unplugged') {
        return `<img src="https://cdn-icons-png.flaticon.com/512/833/833482.png" style="${iconStyles}" alt="Unplugged Icon" />`; // Unplugged icon
    } else {
        return `<img src="https://cdn-icons-png.flaticon.com/512/833/833484.png" style="${iconStyles}" alt="Unknown Icon" />`; // Unknown icon
    }
}

</script>
@endsection
