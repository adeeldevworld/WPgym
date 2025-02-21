jQuery(document).ready(function($) {
    // Media uploader
    var mediaUploader;
    function initMediaUploader(buttonId, inputId, previewId) {
        $(buttonId).on('click', function(e) {
            e.preventDefault();
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            mediaUploader = wp.media({
                title: 'Choose Membership Photo',
                button: {
                    text: 'Use this photo'
                },
                multiple: false
            });
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $(inputId).val(attachment.url);
                $(previewId).html('<img src="' + attachment.url + '" style="max-width:100px;max-height:100px;">');
            });
            mediaUploader.open();
        });
    }

    initMediaUploader('#upload_photo_button', '#photo_url', '#photo_preview');
    initMediaUploader('#edit-upload_photo_button', '#edit-photo_url', '#edit-photo_preview');

    // Add new membership
    $('#add-membership-form').on('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted');
        var formData = new FormData(this);
        
        // Add the action parameter
        formData.append('action', 'add_membership');
        
        // Log each form field
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        $.ajax({
            url: membershipManager.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('AJAX response:', response);
                if (response.success) {
                    alert('Membership added successfully!');
                    window.location.href = 'admin.php?page=membership-manager';
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    readyState: xhr.readyState,
                    statusText: xhr.statusText
                });
                alert('An error occurred while adding the membership. Status: ' + status + ', Error: ' + error + ', Response: ' + xhr.responseText);
            }
        });
    });

    // Edit membership
    $('.edit-membership').on('click', function() {
        var id = $(this).data('id');
    
        $.ajax({
            url: membershipManager.ajax_url,
            type: 'POST',
            data: {
                action: 'get_membership',
                nonce: membershipManager.nonce,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    var membership = response.data;
                    
                    // Set form values based on the retrieved membership details
                    $('#edit-photo_url').val(membership.photo_url);
                    $('#edit-photo_preview').html('<img src="' + membership.photo_url + '" style="max-width:100px;max-height:100px;">');
                    $('#edit-membership-id').val(membership.id);
                    $('#edit-name').val(membership.name);
                    $('#edit-type').val(membership.type);
                    $('#edit-billing_type').val(membership.billing_type);
                    $('#edit-price').val(membership.price);
                    $('#edit-dashboard_access').val(membership.dashboard_access);
                    $('#edit-access_hours').val(membership.access_hours);
                    $('#edit-benefits').val(membership.benefits);
    
                    // Show the modal
                    $('#edit-membership-modal').show();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });
    

    $('#edit-membership-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        
        $.ajax({
            url: membershipManager.ajax_url,
            type: 'POST',
            data: {
                action: 'edit_membership',
                nonce: membershipManager.nonce,
                ...formData
            },
            success: function(response) {
                if (response.success) {
                    alert('Membership updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });

    $('#edit-cancel').on('click', function() {
        $('#edit-membership-modal').hide();
    });

    // Delete membership
    $('.delete-membership').on('click', function() {
        if (confirm('Are you sure you want to delete this membership?')) {
            var id = $(this).data('id');
            
            $.ajax({
                url: membershipManager.ajax_url,
                type: 'POST',
                data: {
                    action: 'delete_membership',
                    nonce: membershipManager.nonce,
                    id: id
                },
                success: function(response) {
                    if (response.success) {
                        alert('Membership deleted successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + response.data);
                    }
                }
            });
        }
    });
});