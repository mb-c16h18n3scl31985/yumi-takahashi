<?php
session_start();
require_once('function/dbconnect.php');
require_once('function/retweet_did.php');
require_once('function/favorite_did.php');
require_once('function/shortcut_htmlspecialchars.php');

if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    //ログインしている
    $_SESSION['time'] = time();
    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    $members->execute([$_SESSION['id']]);
    $member = $members->fetch();
} else {
    //ログインしていない
    header('Location: login_out/login.php');
    exit();
}

//投稿の記録
if (!empty($_POST)) {
    if ($_POST['message'] != '') {
        $message = $db->prepare(
            'INSERT INTO posts 
            (member_id,message,reply_post_id,created)
            VALUES(?,?,?,NOW())'
        );
        $message->execute([
            $member['id'],
            $_POST['message'],
            $_POST['reply_post_id']
        ]);
    }
}

//最終ページの取得
$page = $_REQUEST['page'] ?? 1;
$page = max($page, 1);

$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
$maxPage = ceil($cnt['cnt'] / 5);
$page = min($page, $maxPage);

$start = ($page - 1) * 5;
$start = max(0, $start);


//投稿の取得
$posts = $db->prepare(
    'SELECT members.name,members.picture,posts.*
    FROM members,posts
    WHERE members.id=posts.member_id
    ORDER BY posts.created DESC
    LIMIT ?,5'
);
$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();


//返信の場合
if (isset($_REQUEST['res'])) {
    $response = $db->prepare(
        'SELECT members.name,members.picture,posts.*
        FROM members,posts
        WHERE members.id=posts.member_id
        AND posts.id = ?
        ORDER BY posts.created DESC'
    );
    $response->execute([$_REQUEST['res']]);
    $table = $response->fetch();
    $message = '@' . $table['name'] . ' ' . $table['message'];
}

/**
 * URLをリンクに変換
 */
function makeLink($value)
{
    return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", '<a href="\1\2">\1\2</a>', $value);
}

/**
 * ある投稿におけるリツイート件数を返す
 * @param object $db PDOオブジェクト
 * @param int $post_id 投稿ID
 * @return int リツイート件数
 */
function retweet_count($db, $post_id)
{
    $retweeted_counts = $db->prepare('SELECT COUNT(rt_post_id) AS rt_count FROM posts WHERE rt_post_id=?');
    $retweeted_counts->execute([$post_id]);
    $retweeted_count = $retweeted_counts->fetch();
    return $retweeted_count['rt_count'];
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ひとこと掲示板</title>

    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div id="wrap">
        <div id="head">
            <h1>ひとこと掲示板</h1>
        </div>
        <div id="content">

            <div style="text-align: right;">
                <a href="login_out/logout.php">ログアウト</a>
            </div>
            <form action="" method="post">
                <dl>
                    <dt>
                        <?php echo h($member['name']); ?>さん、
                        メッセージをどうぞ
                    </dt>
                    <dd>
                        <textarea name="message" cols="50" rows="3"><?php echo h($message ?? ''); ?></textarea>
                        <input type="hidden" name="reply_post_id" value="<?php echo h($_REQUEST['res'] ?? ''); ?>">
                    </dd>
                </dl>

                <div>
                    <input type="submit" value="投稿する">
                </div>
            </form>

            <?php foreach ($posts as $post) { ?>

                <div class="msg">
                    <img src="join/member_picture/<?php echo h($post['picture']); ?>" width="48" height="48" alt="<?php echo h($post['name']); ?>">

                    <p>
                        <?php echo h(makeLink($post['message'])); ?>
                        <span class="name">
                            (<?php echo h($post['name']); ?>)
                        </span>
                        [<a href="index.php?res=<?php echo h($post['id']); ?>">Re</a>]
                    </p>

                    <div style="display:flex;">
                        <div class="button" style="margin-right:10px; display:flex;">
                            <!-- いいねボタン -->
                            <form action="function/favorite.php" method="post" style="margin-right:5px">
                                <input type="hidden" name="post_id" value="<?php echo h($post['id']); ?>">

                                <?php if (favorite_did($db, $post['id']) > 0) { ?>
                                    <input type="image" name="submit" src="images/star-yellow.png" width="17" height="17" alt="いいねしています">
                                <?php } else { ?>
                                    <input type="image" name="submit" src="images/star-gray.png" width="17" height="17" alt="いいねボタン">
                                <?php } ?>
                            </form>

                            <div style="display:flex;">
                                <!-- リツイートボタン -->
                                <form action="function/retweet.php" method="post" style="display:inline;">
                                    <input type="hidden" name="rt_post_id" value="<?php echo h($post['id']); ?>">
                                    <input type="hidden" name="rt_message" value="<?php echo h($post['message']); ?>">
                                    <input type="hidden" name="rt_member" value="<?php echo h($post['name']); ?>">
                                    <?php if (retweet_did($db, $post['id'])) { ?>
                                        <input type="image" name="submit" src="images/rt-blue.png" width="17" height="17" alt="リツイートボタン">
                                    <?php } else { ?>
                                        <input type="image" name="submit" src="images/rt-gray.png" width="17" height="17" alt="リツイートボタン">
                                    <?php } ?>
                                </form>
                                <!-- リツイート件数 -->
                                <p>
                                    <?php echo h(retweet_count($db, $post['id'])); ?>
                                </p>
                            </div>
                        </div>

                        <p class="day">
                            <a href="function/view.php?post_id=<?php echo h($post['id']); ?>">
                                <?php echo h($post['created']); ?>
                            </a>

                            <?php if ($post['reply_post_id'] > 0) { ?>
                                <a href="function/view.php?post_id=<?php echo h($post['reply_post_id']); ?>">
                                    返信元のメッセージ
                                </a>
                            <?php } ?>

                            <?php if ($_SESSION['id'] == $post['member_id']) { ?>
                                [<a href="function/delete.php?post_id=<?php echo h($post['id']); ?>" style="color:#F33;">削除</a>]
                            <?php } ?>
                        </p>
                    </div>
                </div>
            <?php } ?>

            <ul class="paging">
                <?php if ($page > 1) { ?>
                    <li><a href="index.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
                <?php } else { ?>
                    <li>前のページへ</li>
                <?php } ?>

                <?php if ($page < $maxPage) { ?>
                    <li><a href="index.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
                <?php } else { ?>
                    <li>次のページへ</li>
                <?php } ?>
            </ul>

        </div>

    </div>
</body>

</html>