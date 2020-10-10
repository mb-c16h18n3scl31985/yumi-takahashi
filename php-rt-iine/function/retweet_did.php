<?php

/**
 * ある投稿に対しリツイートをしたか否かを返す
 * @param object $db PDOオブジェクト
 * @param int $post_id 取得したい投稿のID
 * @return int リツイートデータ(カラム)の個数(有無)
 */
function retweet_did($db, $post_id)
{
    $retweet = $db->prepare('SELECT * FROM posts WHERE member_id=? AND rt_post_id=?');
    $retweet->execute([$_SESSION['id'], $post_id]);
    $retweeted = $retweet->fetch();
    if ($retweeted > 0) {
        return true;
    } else {
        return false;
    }
}
