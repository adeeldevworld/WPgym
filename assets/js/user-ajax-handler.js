// js/admin.js
jQuery(document).ready(function($) {
    //jQuery(document).ready(function($) {
        function loadMembers() {
            $.ajax({
                url: gymMembersAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_gym_members',
                    nonce: gymMembersAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('#gym-members-list').html(response.data);
                    } else {
                        console.error('Error loading members:', response.data);
                        alert('Error loading members: ' + response.data);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', {
                        status: jqXHR.status,
                        statusText: jqXHR.statusText,
                        responseText: jqXHR.responseText,
                        textStatus: textStatus,
                        errorThrown: errorThrown
                    });
                    alert('An error occurred while loading members. Please check the console for more details.');
                }
            });
        }
    
        // Load members when the page loads
        loadMembers();
    
        // ... (rest of your existing code)
    
        $('#member-form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            formData += '&action=' + ($('#user-id').val() ? 'edit_gym_member' : 'add_gym_member');
            formData += '&nonce=' + gymMembersAdmin.nonce;
    
            $.ajax({
                url: gymMembersAdmin.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#member-modal').hide();
                        loadMembers(); // Reload the member list after adding/editing
                        alert('Member saved successfully');
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                    alert('An error occurred while processing your request. Please check the console for more details.');
                }
            });
        });
    
        // ... (rest of your existing code)
    //});

    $('#add-new-member').on('click', function() {
        $('#user-id').val('');
        $('#member-form')[0].reset();
        $('#member-modal').show();
    });
    $(document).on('change', '#membership-type', function() {
        var selectedOption = $(this).find('option:selected');
        var price = selectedOption.data('price');
        $('#price').val(price);
    });

    $(document).on('click', '.edit-member', function() {
        var userId = $(this).data('id');
        $.ajax({
            url: gymMembersAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'get_gym_member',
                nonce: gymMembersAdmin.nonce,
                user_id: userId
            },
            success: function(response) {
                if (response.success) {
                    var member = response.data;
                    $('#user-id').val(member.ID);
                    $('#username').val(member.user_login);
                    $('#email').val(member.user_email);
                    $('#unique-id').val(member.unique_id);
                    $('#membership-type').val(member.membership_type);
                    $('#status').val(member.status);
                    $('#registration-date').val(member.registration_date);
                    $('#end-date').val(member.end_date);
                    $('#price').val($('#membership-type option:selected').data('price'));
                    $('#member-modal').show();
                } else {
                    alert('Error loading member: ' + response.data);
                }
            }
        });
    });
    $(document).on('click', '.delete-member', function() {
        if (confirm('Are you sure you want to delete this member?')) {
            var userId = $(this).data('id');
            $.ajax({
                url: gymMembersAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'delete_gym_member',
                    nonce: gymMembersAdmin.nonce,
                    user_id: userId
                },
                success: function(response) {
                    if (response.success) {
                        loadMembers();
                    } else {
                        alert('Error deleting member: ' + response.data);
                    }
                }
            });
        }
    });

    $('#member-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        formData += '&action=' + ($('#user-id').val() ? 'edit_gym_member' : 'add_gym_member');
        formData += '&nonce=' + gymMembersAdmin.nonce;
    
        $.ajax({
            url: gymMembersAdmin.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#member-modal').hide();
                    loadMembers();
                    alert('Member saved successfully');
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                alert('An error occurred while processing your request. Please check the console for more details.');
            }
        });
    });
    $('#gym-registration-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        formData += '&action=register_gym_member&nonce=' + gymMembersFrontend.nonce;
    
        $.ajax({
            url: gymMembersFrontend.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Registration successful!');
                    window.location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX error:', textStatus, errorThrown);
                alert('An error occurred while processing your request. Please check the console for more details.');
            }
        });
    });
});