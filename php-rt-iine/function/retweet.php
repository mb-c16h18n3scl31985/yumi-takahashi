<?php
session_start();
require_once('dbconnect.php');

//RTした記事の有無の確認
if (isset($_POST['rt_post_id'])) {
    $retweet = $db->prepare(
        'SELECT members.name,posts.* 
        FROM members,posts
        WHERE members.id=posts.member_id AND posts.rt_post_id = ?'
    );
    $retweet->execute([$_POST['rt_post_id']]);
    $retweeted = $retweet->fetch();

    if ($retweeted) {
        //既にRTした記事がある場合、削除
        $rt_delete = $db->prepare(
            'DELETE FROM posts
            WHERE member_id=? AND rt_post_id=?'
        );
        $rt_delete->execute([$_SESSION['id'], $_POST['rt_post_id']]);
    } else {
        //投稿
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
