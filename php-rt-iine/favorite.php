<?php
session_start();
require_once('dbconnect.php');

if (isset($_POST['post_id'])) {
    //該当する投稿に対しいいねをしているかどうかの確認
    $favorite_set = $db->prepare(
        'SELECT *
        FROM favorite
        WHERE favorite_post_id=?
        AND member_id=?
        AND delete_flag=false'
    );
    $favorite_set->execute([$_POST['post_id'], $_SESSION['id']]);
    $favorite_record = $favorite_set->fetch();

    if (empty($favorite_record)) {
        //いいねをまだしていない場合
        $favorite_do = $db->prepare(
            'INSERT INTO favorite
            (favorite_post_id, member_id, created) 
            VALUES (?, ?, NOW())'
        );
        $favorite_do->execute([
            $_POST['post_id'],
            $_SESSION['id']
        ]);
    } else {
        //既にいいねを押している場合
        $favorite_delete = $db->prepare(
            'UPDATE favorite
            SET delete_flag=true, deleted=NOW()
            WHERE favorite_post_id=?'
        );
        $favorite_delete->execute([$_POST['post_id']]);
    }

    header('Location: index.php');
    exit();
}
