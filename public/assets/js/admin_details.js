function handleProfilePictureChange(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file type
        if (!file.type.startsWith('image/')) {
            toastr.error('Please select a valid image file');
            return;
        }
        
        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            toastr.error('File size must be less than 5MB');
            return;
        }
        
        // Create preview
        const reader = new FileReader();
        reader.onload = function(e) {
            $("#profile_picture_display").attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
        
        // Upload the file
        uploadProfilePicture(file);
    }
}

// Upload profile picture to server
function uploadProfilePicture(file) {
    const formData = new FormData();
    formData.append('profile_picture', file);
    
    // Show loading state
    const originalSrc = $("#profile_picture_display").attr('src');
    
    $.ajax({
        url: '/settings/account/update-profile-picture',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                toastr.success('Profile picture updated successfully');
                // Update the image source with the new URL
                if (response.profile_picture_url) {
                    $("#profile_picture_display").attr('src', response.profile_picture_url);
                }
            } else {
                toastr.error(response.message || 'Failed to update profile picture');
                // Revert to original image on error
                $("#profile_picture_display").attr('src', originalSrc);
            }
        },
        error: function(xhr) {
            toastr.error('Failed to update profile picture');
            console.error('Upload error:', xhr);
            // Revert to original image on error
            $("#profile_picture_display").attr('src', originalSrc);
        }
    });
}

// Call loadUserData when page loads
// $(document).ready(function() {
//     loadUserData();
// });