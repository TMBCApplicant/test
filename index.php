<?php
/**
 * Index file of the application. This routes the request to the correct controller method
 */

include_once 'Controllers/CommentController.php';

if (!isset($_POST['request_type'])) {
    include 'Views/IndexView.php';
    exit;
}

$commentController = new CommentController();
switch ($_POST['request_type']) {
    case 'addComment':
        $commentController->addComment();
        break;
    case 'fetchComments':
        $commentController->fetchComments();
        break;
    case 'fetchCommentForm':
        $commentController->fetchCommentForm();
        break;
    default:
        echo 'Request type not found';
}