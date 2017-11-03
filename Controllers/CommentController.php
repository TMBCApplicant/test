<?php
/**
 * Conntrols comment requests and manages database calls for comments
 */

include_once __DIR__.'/../Models/Comment.php';
include_once __DIR__.'/../Helpers/CommentHelper.php';

class CommentController {

    /**
     * Fetches comments and echos HTML formatted comments.
     * This accepts parent ID as a post value.
     *
     * @return bool
     */
    public function fetchComments(): bool
    {
        $commentHelper = new CommentHelper();
        $parentId = $_POST['parent_id'] ?? null;
        $commentObject = new Comment();
        foreach($commentObject->fetchComments($parentId) as $commentData) {
            $commentHelper->getCommentString($commentData);
        }

        return true;
    }

    /**
     * Echos an HTML form used to insert comments.
     *
     * @return bool
     */
    public function fetchCommentForm(): bool
    {
        $commentHelper = new CommentHelper();
        $commentHelper->getCommentForm();

        return true;
    }

    /**
     * Handles the insertion of comments.
     *
     * @return bool
     */
    public function addComment(): bool
    {
        $name = $_POST['name'] ?? null;
        $text = $_POST['text'] ?? null;
        $parentId = $_POST['parent_id'] ?? null;

        $commentObject = new Comment();
        $commentObject->setName($name);
        $commentObject->setText($text);

        if (!empty($parentId)) {
            $commentObject->setParentId($parentId);
        }

        $isSaved = $commentObject->save();
        echo json_encode(['success' => $isSaved]);
        return true;
    }
}