<?php

//RTした記事の有無の確認
if (isset($_POST['rt_post_id'])) {
    $retweet = $db->prepare(
        'SELECT members.name,posts.* 
        FROM members,posts
        WHERE members.id=posts.member_id AND posts.rt_post_id = ?'
    ); //流用しているからデータ*にしているけど減らしたほうがいいのか?
    $retweet->execute([$_POST['rt_post_id']]);
    $retweeted = $retweet->fetch();

    if ($retweeted) {
        //既にRTした記事がある場合、消去
        $rt_delete = $db->prepare(
            'UPDATE posts
            SET rt_post_id=0
            WHERE member_id=?,rt_post_id=?'
        );
        $rt_delete->execute([$_SESSION['id'], $_POST['rt_post_id']]);

        echo '削除しました';
    } else {
        //投稿
        $rt_message_body = '@' . $_POST['rt_member'] . ' ' . $_POST['rt_message'];

        $rt_do = $db->prepare(
            'INSERT INTO posts 
            SET member_id=?,message=?,rt_post_id=?,created=NOW()'
        );
        $rt_do->execute([
            $_SESSION['id'],
            $rt_message_body,
            $_POST['rt_post_id']
        ]);
        echo '投稿しました';
    }

    // header('Location: index.php');
    // exit();
}
