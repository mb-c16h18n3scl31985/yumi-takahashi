<?php
session_start();
require_once('dbconnect.php');
require_once('favorite_did.php');

if (isset($_POST['post_id'])) {
    //該当する投稿に対しいいねをしているかどうかの確認
    $favorite_record = favorite_did($db, $_POST['post_id']);

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
            SET delete_flag=true
            WHERE favorite_post_id=?'
        );
        $favorite_delete->execute([$_POST['post_id']]);
    }

    header('Location: ../index.php');
    exit();
}
