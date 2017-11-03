<?php
/**
 * HTML used to display a comment and child comments
 */

include_once __DIR__.'/../Helpers/CommentHelper.php';

$commentHelper = new CommentHelper();
?>

<div class="comment">
    <div class="name">
        <?php echo $comment['name']; ?>
    </div>
    <div class="text">
        <?php echo $comment['text']; ?>
    </div>
    <?php if (isset($comment['depth']) && $comment['depth'] < CommentHelper::MAX_DEPTH_LEVEL): ?>
       <div class="reply-container">
           <a href="#" class="reply-link" data-parent="<?php echo $comment['id'] ?>">Reply</a>
       </div>
    <?php endif; ?>
    <div class="comment-children">
        <?php if (!empty($comment['children'])): ?>
            <?php foreach($comment['children'] as $childComment): ?>
                <?php $commentHelper->getCommentString($childComment); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
