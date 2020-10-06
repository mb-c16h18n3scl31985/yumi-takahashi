<?php

/**
 * ある投稿に対しいいねをしたか否かを返す
 * @param object $db PDOオブジェクト
 * @param int $post_id 取得したい投稿のID
 * @return int いいねカラムの個数(有無)
 */
function favorite_did($db, $post_id)
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
