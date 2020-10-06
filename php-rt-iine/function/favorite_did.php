<?php
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
