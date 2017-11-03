/**
 * commentApp object to manage comment form, comment display, and comment submission
 */

var commentApp = {
    /**
     * Submits a comment to be saved to the database
     */
    submitForm: function () {
        var name = $('#name'),
            text = $('#text'),
            parentId = $('#parent_id');

        if (!name.val().length) {
            throw 'Name is a required value';
            return;
        }

        if (!text.val().length) {
            throw 'Text is a required value';
            return;
        }

        var formData = {
            request_type: 'addComment',
            name: name.val(),
            text: text.val()
        };

        if (parentId.val()) {
            formData['parent_id'] = parentId.val();
        }

        $.post('index.php', formData, function (data) {
            data = JSON.parse(data);
            if (!data['success']) {
                $('.error').html('An error has occurred. Please try again later');
            } else {
                $('.error').html('').hide();
            }
        });
    },

    /**
     * Displays error message with the given text
     * @param text
     */
    triggerError: function (text) {
        $('.error').html(text).show();
        $('html, body').animate({scrollTop: '0px'}, 200);
    },

    /**
     * Fetches comments and places them in the provided container selector.
     * If no parent Id is specified then all comments are returned.
     *
     * @param containerSelector
     * @param parentId
     */
    fetchComments: function (containerSelector, parentId) {
        $.post('index.php', {request_type: 'fetchComments', parent_id: parentId}, function (data) {
            containerSelector.html(data);
        });
    },

    /**
     * Returns form HTML
     */
    fetchFormHtml: function() {
        $.post('index.php', {request_type: 'fetchCommentForm'}, function (data) {
            $('.form-container').html(data);
        })
    },

    /**
     * Returns form HTML back to its original state
     */
    resetFormHtml: function() {
        $('#name').val('');
        $('#text').val('');
        $('#parent_id').val('');
        $('.form-container').first().append($('#comment-form'));

    }
};

$(document).ready(function() {
    /**
     * Load comments and form on page ready
     */
   commentApp.fetchComments($('.comment-list'), 0);
   commentApp.fetchFormHtml();

    /**
     * Handle clicks on reply links
     */
    $('body').on('click', 'a.reply-link', function(e) {
        e.preventDefault();

        var thisObject = $(this);
        thisObject.parent().append($('#comment-form'));
        $('#parent_id').val(thisObject.data('parent'))
    });

    /**
     * Handle form submission containing comments
     */
    $('body').on('click', 'input#submit-button', function(e) {
        var commentContainer = $('.comment-list'),
            parentId = $('#parent_id').val()
        ;

        // Attempt to submit form. Show user an error if required.
        try {
            commentApp.submitForm();
        } catch(e) {
            commentApp.triggerError(e);
            return;
        }

        if (parentId) {
            commentContainer = $(this).closest('.comment').children('.comment-children').first();
        }

        // Fetch comments and reset form
        commentApp.fetchComments(commentContainer, (parentId.length > 0 ? parentId : 0));
        commentApp.resetFormHtml();
    });
});