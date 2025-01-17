@extends('admin.layouts.app')

@section('title', 'Send Notification')

@section('header-css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Mock phone container */
    .phone-preview {
        width: 360px;
        height: 670px;
        background: #000;
        border-radius: 30px;
        margin: 20px auto;
        position: relative;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        overflow: hidden;
    }

    /* Phone screen */
    .phone-screen {
        width: 100%;
        height: 100%;
        background: #f5f5f5;
        position: relative;
        padding: 10px;
        box-sizing: border-box;
    }

    /* Notification container */
    .notification-preview {
        width: 100%;
        margin: 0 auto;
        background: #2C2C2E;
        border-radius: 12px;
        color: #ffffff;
        font-family: Arial, sans-serif;
        padding: 10px 15px;
        box-sizing: border-box;
        position: relative;
    }

    .notification-preview .app-logo {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        float: left;
        margin-right: 10px;
    }

    .notification-preview .app-details {
        display: inline-block;
        vertical-align: middle;
    }

    .notification-preview .app-name {
        font-size: 14px;
        font-weight: bold;
        margin: 0;
    }

    .notification-preview .app-time {
        font-size: 12px;
        color: #aaa;
        margin: 0;
    }

    .notification-preview .notification-content {
        clear: both;
        padding-top: 10px;
    }

    .notification-preview .notification-title {
        font-size: 14px;
        font-weight: bold;
        margin: 0;
    }

    .notification-preview .notification-body {
        font-size: 12px;
        margin: 5px 0 0 0;
    }

    .notification-preview .notification-icon {
        position: absolute;
        top: 30px;
        right: 15px;
        width: 50px;
        height: 50px;
    }
</style>
@endsection

@section('main')
<div class="pb-5 content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="mb-2 row">
                <div class="col-sm-6">
                    <h1 class="m-0">Send Notification</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="container mt-5">
                <div class="row">
                    <div class="col-7">
                        <form method="POST" action="{{ route('admin.fcm.send') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="mb-4 col-md-12">
                                    <label for="employees">Select Employees</label>
                                    <button type="button" id="select_all_employees"
                                        class="float-right btn btn-secondary btn-sm">Select All Employees</button>
                                    <select id="employees" name="employees[]" class="form-control select2" multiple>
                                        @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->emp_code }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4 col-md-12">
                                    <label for="title">Notification Title</label>
                                    <input type="text" id="title" name="title" class="form-control" required>
                                </div>

                                <div class="mb-4 col-md-12">
                                    <label for="body">Notification Body</label>
                                    <textarea id="body" name="body" class="form-control" rows="4" required></textarea>
                                </div>

                                <div class="mb-4 col-md-12">
                                    <label for="image_type">Select Image Type</label>
                                    <select id="image_type" name="image_type" class="form-control" required>
                                        <option value="no_image">No Image</option>
                                        <option value="profile_image">Profile Image</option>
                                        <option value="wedding_banquets_logo">Wedding Banquets Logo</option>
                                        <option value="wedding_banquets_favicon">Wedding Banquets Favicon</option>
                                        <option value="custom_image">Select Custom Image</option>
                                    </select>
                                </div>

                                <div class="mb-4 col-md-12" id="custom_image_upload" style="display: none;">
                                    <label for="custom_image">Upload Custom Image</label>
                                    <input type="file" id="custom_image" name="custom_image" class="form-control">
                                </div>

                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">Send Notification</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-5" style="margin-top: -7rem">
                        <div class="phone-preview">
                            <div class="phone-screen">
                                <div class="notification-preview" id="notification-preview">
                                    <img id="preview-logo" class="app-logo" src="https://cms.wbcrm.in/favicon.jpg"
                                        alt="App Logo">
                                    <div class="app-details">
                                        <p class="app-name">Your App</p>
                                        <p class="app-time">2:14 PM</p>
                                    </div>
                                    <img id="preview-icon" class="notification-icon"
                                        src="https://cms.wbcrm.in/favicon.jpg" alt="Notification Icon">
                                    <div class="notification-content">
                                        <p class="notification-title" id="preview-title">Notification Title</p>
                                        <p class="notification-body" id="preview-body">Notification Body</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('footer-script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 for dropdowns
        $('.select2').select2();

        // Hide custom image upload field by default
        $('#custom_image_upload').hide();

        // Hide notification icon initially
        $('#preview-icon').hide();

        // Toggle custom image upload field visibility
        $('#image_type').on('change', function() {
            const imageType = $(this).val();
            if (imageType === 'custom_image') {
                $('#custom_image_upload').show();
            } else {
                $('#custom_image_upload').hide();
                updatePreview(); // Update preview immediately
            }
        });

        // Select all employees on button click
        $('#select_all_employees').on('click', function() {
            $('#employees > option').prop('selected', true).trigger('change');
        });

        // Update preview on input changes
        $('#title, #body, #image_type, #custom_image').on('input change', function() {
            updatePreview();
        });

        // Function to update the notification preview
        function updatePreview() {
            const title = $('#title').val();
            const body = $('#body').val();
            const imageType = $('#image_type').val();
            const customImage = $('#custom_image')[0].files[0];

            // Set title and body
            $('#preview-title').text(title || 'Notification Title');
            $('#preview-body').text(body || 'Notification Body');

            // Default icon and logo URLs
            const defaultLogoUrl = 'https://cms.wbcrm.in/favicon.jpg';
            const wbLogoUrl = 'https://cms.wbcrm.in/wb-logo2.webp';
            let iconUrl = defaultLogoUrl;

            // Determine icon visibility and source based on image type
            if (imageType === 'wedding_banquets_logo') {
                iconUrl = wbLogoUrl;
                $('#preview-icon').show();
            } else if (imageType === 'no_image') {
                $('#preview-icon').hide();
            } else if (imageType === 'custom_image' && customImage) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview-icon').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(customImage);
                return; // Exit early since the custom image preview is async
            } else {
                $('#preview-icon').show();
            }

            // Update logo and icon sources
            $('#preview-logo').attr('src', defaultLogoUrl);
            $('#preview-icon').attr('src', iconUrl);
        }
    });
</script>
@endsection
