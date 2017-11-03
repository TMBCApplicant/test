<?php
/**
 * Comment form used to input comments
 */
?>

<form id="comment-form">
    <input type="hidden" id="parent_id" name="parent_id" />

    <div>
        Name: <input type="text" id="name" name="name" />
    </div>

    <div>
        Text: <input type="text" id="text" name="text" />
    </div>
    <input id="submit-button" type="button" Value="Submit Comment">
</form>