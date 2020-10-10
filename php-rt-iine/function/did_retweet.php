<?php

/**
 * ある投稿に対しリツイートをしたか否かを返す
 * @param object $db PDOオブジェクト
 * @param int $post_id 取得したい投稿のID
 * @return boolean リツイートデータ(カラム)の件数の有無
 */
function did_retweet($db, int $post_id)
{
    $retweet = $db->prepare('SELECT COUNT(*) as retweet_count FROM posts WHERE member_id=? AND rt_post_id=?');
    $retweet->execute([$_SESSION['id'], $post_id]);
    $retweeted = $retweet->fetch();
    return $retweeted['retweet_count'] > 0;
}
