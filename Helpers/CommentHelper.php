<?php
/**
 * Helper file to assist with general Comment actions
 */

include_once __DIR__.'/../Models/Comment.php';

class CommentHelper {

    const COMMENT_VIEW_PATH = __DIR__.'/../Views/CommentView.php';
    const COMMENT_FORM_PATH = __DIR__.'/../Views/CommentFormView.php';

    const MAX_DEPTH_LEVEL = 3;

    private $comment;

    /**
     * CommentHelper constructor.
     */
    public function __construct()
    {
        $this->comment = new Comment();
    }

    /**
     * Renders full HTML of the comment and child comments
     *
     * @param array $comment
     *
     * @return bool
     */
    public function getCommentString(array $comment): bool
    {
        $comment['depth'] = $this->comment->getNestLevel($comment['id']);
        include self::COMMENT_VIEW_PATH;
        return true;
    }

    /**
     * Renders comment form
     *
     * @return bool
     */
    public function getCommentForm(): bool
    {
        include self::COMMENT_FORM_PATH;
        return true;
    }
}