<?php
session_start();
require_once('dbconnect.php');

if (isset($GET['post_id'])) {
    //該当する投稿に対しいいねをしているかどうかの確認
    $favorite_set = $db->prepare(
        'SELECT *
        FROM favorite
        WHERE favorite_post_id=?
        AND member_id=?
        AND delete_flag=false'
    );
    $favorite_set->execute([$_GET['post_id'], $_SESSION['id']]);
    $favorite_records = $favorite_set->fetch();
    $favorite_record = $favorite_records[0];

    if (!($favorite_record)) {
        //いいねをまだしていない場合
        $favorite_do = $db->prepare(
            'INSERT INTO favorite
            SET favorite_post_id=?
            AND member_id=?
            AND created=NOW()'
        );
        $favorite_do->execute([
            $_GET['post_id'],
            $_SESSION['id']
        ]);
    } elseif ($favorite_record) {
        //既にいいねを押している場合
        $favorite_delete = $db->prepare(
            'UPDATE favorite
            SET delete_flag=true
            AND deleted=NOW()
            WHERE favorite_post_id=?'
        );
        $favorite_delete->execute([$_GET['post_id']]);
    }

    header('Location: index.php');
    exit();
}
