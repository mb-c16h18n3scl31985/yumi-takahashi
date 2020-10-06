<?php
//ある投稿に対しリツイートしているか否かを返す
function retweet_did($db, $post_id)
{
    $retweet = $db->prepare('SELECT * FROM posts WHERE member_id=? AND rt_post_id=?');
    $retweet->execute([$_SESSION['id'], $post_id]);
    $retweeted = $retweet->fetch();
    return $retweeted;
}
