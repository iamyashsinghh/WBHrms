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
        let markersGroup = null;
        const customIcon = (profileImg, isOnline) => L.divIcon({
            className: '',
            html: `
            <div style="position: relative; width: 40px; height: 60px;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="60" height="90" fill="${isOnline ? '#891010' : '#A06B14'}">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 4.25 4.77 10.17 6.44 12.38a1 1 0 0 0 1.56 0C14.23 19.17 19 13.25 19 9c0-3.87-3.13-7-7-7zm0 15c-1.76-2.16-5-6.5-5-8 0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.5-3.24 5.84-5 8zm0-10.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5z"></path>
                </svg>
                <div style="position: absolute; top: 22px; left: 15px; width: 30px; height: 30px; border-radius: 50%; overflow: hidden;">
                    <img src="${profileImg}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                ${isOnline
                    ? `<div style="position: absolute; top: 65px;
                     left: 40%; transform: translateX(-50%);
                      width: 30px; height: 10px; background: rgba(0, 255, 0, 0.5);
                       border-radius: 50%; animation: pulse 1.5s infinite;
                        z-index: -1;"></div>
                        <div style="position: absolute; top: 65px;
                     left: 40%; transform: translateX(-50%);
                      width: 30px; height: 10px; background: rgba(0, 255, 0, 0.5);
                       border-radius: 50%; animation: pulses 1.5s infinite;
                        z-index: -1;"></div>`
                    : ''}
            </div>
            <style>
                @keyframes pulse {
                    0% {
                        transform: scale(1);
                        opacity: 0.8;
                    }
                    50% {
                        transform: scale(1.5);
                        opacity: 0.4;
                    }
                    100% {
                        transform: scale(1);
                        opacity: 0.8;
                    }
                }
                @keyframes pulses {
                    0% {
                        transform: scale(2);
                        opacity: 0.4;
                    }
                    50% {
                        transform: scale(1.5);
                        opacity: 0.8;
                    }
                    100% {
                        transform: scale(2);
                        opacity: 0.4;
                    }
                }
            </style>`,
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
                        markersGroup = L.layerGroup().addTo(map);
                    }
                    markersGroup.clearLayers();
                    response.forEach(location => {
                        if (location.latitude && location.longitude) {
                            const lastRecorded = moment(location.recorded_at);
                            const currentTime = moment();
                            const isOnline = currentTime.diff(lastRecorded, 'minutes') <= 1;
                            const formattedTime = lastRecorded.format('MMMM Do YYYY, h:mm:ss a');
                            const marker = L.marker(
                                [location.latitude, location.longitude], {
                                    icon: customIcon(location.profile_img, isOnline)
                                }
                            );
                            let history_url = "{{ route('admin.geo.index_history', ':emp_code') }}"
                                .replace(':emp_code', location.emp_code);
                                marker.bindPopup(`
    <div style="display: flex; justify-content: space-between; align-items: center; width: 300px;">
        <!-- Employee Information -->
        <div style="flex: 1; padding-right: 10px; border-right: 1px solid #ddd;">
            <b>${location.employee_name}</b>
            <br> Status: ${isOnline ? 'Online' : 'Offline'}
            <br> Recorded at: ${formattedTime}
            <br> Attendance: ${location.attendance_status ? location.attendance_status : 'N/A'}
            <br> Punch In at: ${location.punch_in_time ? moment(location.punch_in_time, 'HH:mm:ss').format('h:mm:ss a') : 'N/A'}
            <br> Punch Out at: ${location.punch_out_time ? moment(location.punch_out_time, 'HH:mm:ss').format('h:mm:ss a') : 'N/A'}
            <br>   <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>History: <a href="${history_url || '#'}" target="_blank" rel="noopener noreferrer">View</a></div>
        <button
            style="background: none; border: none; color: #891010; cursor: pointer; margin-left: 10px;"
            onclick="zoomToLocation(${location.latitude}, ${location.longitude})"
            title="Zoom to Location">
            <i class="fas fa-search-plus"></i>
        </button>
        </div>
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
`);
                            markersGroup.addLayer(marker);
                        }
                    });
                },
                error: function(error) {
                    console.error('Error fetching locations:', error);
                }
            });
        };
        function zoomToLocation(lat, lng) {
    if (map) {
        map.setView([lat, lng], 19);
    }
}
        fetchAllLocations();
        setInterval(fetchAllLocations, 5000);
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
