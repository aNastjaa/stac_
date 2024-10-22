<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploads</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<h2>Existing Uploads</h2>
<ul id="uploadsList"></ul>

<h2>Upload New File</h2>
<form id="uploadForm">
    <input type="file" name="file" required>
    <select name="file_type" required>
        <option value="avatar">Avatar</option>
        <option value="brand_logo">Brand Logo</option>
    </select>
    <button type="submit">Upload</button>
</form>

<!-- Update Form (Hidden Initially) -->
<div id="updateFormContainer" style="display: none;">
    <h2>Update Upload</h2>
    <form id="updateForm">
        <input type="hidden" name="upload_id" id="updateUploadId">
        <input type="file" name="file" required>
        <button type="submit">Update</button>
    </form>
</div>

<script>
$(document).ready(function () {
    fetchUploads();

    // Handle the upload form submission
    $('#uploadForm').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const fileType = $('select[name="file_type"]').val();

        // Determine the URL based on file type
        const uploadUrl = fileType === 'avatar' ? '/api/uploads/avatar' : '/api/uploads/brand-logo';

        $.ajax({
            url: uploadUrl,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (upload) {
                alert('File uploaded successfully!');
                fetchUploads(); // Refresh the uploads list
            },
            error: function (xhr) {
                console.error('Failed to upload file:', xhr.responseText);
                alert('Failed to upload file: ' + xhr.responseText);
            }
        });
    });

    // Event listener for the update button
    $(document).on('click', '.update-btn', function () {
        const uploadId = $(this).data('id');
        $('#updateUploadId').val(uploadId);
        $('#updateFormContainer').show(); // Show the update form
    });

    // Handle the update form submission
    $('#updateForm').on('submit', function (e) {
        e.preventDefault();
        const uploadId = $('#updateUploadId').val();
        const formData = new FormData(this);

        $.ajax({
            url: `/api/uploads/${uploadId}`, // Adjust the URL according to your API
            method: 'PUT', // Use PUT for updates
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                alert('File updated successfully!');
                $('#updateFormContainer').hide(); // Hide the update form
                fetchUploads(); // Refresh the uploads list
            },
            error: function (xhr) {
                console.error('Failed to update file:', xhr.responseText);
                alert('Failed to update file: ' + xhr.responseText);
            }
        });
    });
});

// Function to fetch uploads from the API
function fetchUploads() {
    $.ajax({
        url: '/api/uploads',
        method: 'GET',
        success: function (uploads) {
            $('#uploadsList').empty(); // Clear the list before adding new uploads

            uploads.forEach(function (upload) {
                $('#uploadsList').append(`
                    <li id="upload-${upload.id}">
                        <img src="/storage/${upload.file_url}" alt="Image" width="100">
                        <p>File Type: ${upload.file_type}</p>
                        <button class="update-btn" data-id="${upload.id}">Update</button>
                        <form action="/api/uploads/${upload.id}" method="POST" style="display:inline;" class="delete-form">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit">Delete</button>
                        </form>
                    </li>
                `);
            });
        },
        error: function (xhr) {
            console.error('Failed to fetch uploads:', xhr.responseText);
            alert('Failed to fetch uploads: ' + xhr.responseText);
        }
    });
}
</script>

</body>
</html>
