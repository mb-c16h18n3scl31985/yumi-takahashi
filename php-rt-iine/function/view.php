<?php
session_start();
require_once('dbconnect.php');
require_once('../function/shortcut_htmlspecialchars.php');

if (empty($_REQUEST['post_id'])) {
    header('Location: ../index.php');
}

//投稿の取得
$posts = $db->prepare('SELECT members.name,members.picture,posts.*
    FROM members,posts
    WHERE members.id=posts.member_id
    AND posts.id=?
    ORDER BY posts.created DESC');
$posts->execute([$_REQUEST['post_id']]);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ひとこと掲示板</title>

    <link rel="stylesheet" href="../style.css" />
</head>

<body>
    <div id="wrap">
        <div id="head">
            <h1>ひとこと掲示板</h1>
        </div>
        <div id="content">
            <p>&laquo;<a href="../index.php">一覧に戻る</a></p>
            <?php if ($post = $posts->fetch()) { ?>
                <div class="msg">
                    <img src="../join/member_picture/<?php echo h($post['picture']); ?>" width="48" height="48" alt="<?php echo h($post['name']); ?>">
                    <p>
                        <?php echo h($post['message']); ?>
                        <span class="name">
                            (<?php echo h($post['name']); ?>)
                        </span>
                    </p>
                    <p class="day">
                        <?php echo h($post['created']); ?>
                    </p>
                </div>
            <?php } else { ?>
                <p>その投稿は削除されたか、URLを間違えています。</p>
            <?php } ?>
        </div>

    </div>
</body>

</html>