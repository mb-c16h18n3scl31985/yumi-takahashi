<?php
session_start();
require_once('dbconnect.php');
require_once('retweet_did.php');

if (isset($_POST['rt_post_id'])) {
    //ある投稿に対しRTした記事が既にあるかどうかの確認
    $retweeted = retweet_did($db, $_POST['rt_post_id']);

    if ($retweeted) {
        //既にRTした記事がある場合、削除
        $rt_delete = $db->prepare(
            'DELETE FROM posts
            WHERE member_id=? AND rt_post_id=?'
        );
        $rt_delete->execute([$_SESSION['id'], $_POST['rt_post_id']]);
    } else {
        //まだRT記事がない場合は投稿
        $rt_message_body = 'RT@' . $_POST['rt_member'] . ' ' . $_POST['rt_message'];

        $rt_do = $db->prepare(
            'INSERT INTO posts 
            SET member_id=?,message=?,rt_post_id=?,created=NOW()'
        );
        $rt_do->execute([
            $_SESSION['id'],
            $rt_message_body,
            $_POST['rt_post_id']
        ]);
    }

    header('Location: ../index.php');
    exit();
}
