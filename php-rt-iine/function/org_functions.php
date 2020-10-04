<?php
//htmlspecialchars()のショートカット
function hsc($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

//本文内のURLにリンクを設定
function makeLink($value)
{
    return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", '<a href="\1\2">\1\2</a>', $value);
}

//いいね数を返す
function favorite_count($db, $post_id)
{
    $favorite_posts = $db->prepare(
        'SELECT COUNT(*) AS favorite_count
        FROM favorite 
        WHERE favorite_post_id=? AND member_id=? AND delete_flag=false;'
    );
    $favorite_posts->execute([$post_id, $_SESSION['id']]);
    $favorite_post = $favorite_posts->fetch();
    return $favorite_post['favorite_count'];
}

//ある投稿に対しリツイートしているか否かを返す
function retweet_did($db, $post_id)
{
    $retweet = $db->prepare('SELECT * FROM posts WHERE member_id=? AND rt_post_id=?');
    $retweet->execute([$_SESSION['id'], $post_id]);
    $retweeted = $retweet->fetch();
    return $retweeted;
}

//ある投稿のリツイート件数を返す
function retweet_count($db, $post_id)
{
    $retweeted_counts = $db->prepare('SELECT COUNT(rt_post_id) AS rt_count FROM posts WHERE rt_post_id=?');
    $retweeted_counts->execute([$post_id]);
    $retweeted_count = $retweeted_counts->fetch();
    return $retweeted_count['rt_count'];
}
