<?php
session_start();
require_once('dbconnect.php');

if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    //ログインしている
    $_SESSION['time'] = time();
    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    $members->execute([$_SESSION['id']]);
    $member = $members->fetch();
} else {
    //ログインしていない
    header('Location: login.php');
    exit();
}

//投稿の記録
if (!empty($_POST)) {
    if ($_POST['message'] != '') {
        $message = $db->prepare(
            'INSERT INTO posts 
            SET member_id=?,message=?,reply_post_id=?,created=NOW()'
        );
        $message->execute([
            $member['id'],
            $_POST['message'],
            $_POST['reply_post_id']
        ]);
        header('Location: index.php');
        exit();
    }
}

//投稿の取得
$page = $_REQUEST['page'];
if ($page == '') {
    $page = 1;
}
$page = max($page, 1);


//最終ページの取得
$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
$maxPage = ceil($cnt['cnt'] / 5);
$page = min($page, $maxPage);

$start = ($page - 1) * 5;
$start = max(0, $start);
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

//htmlspecialchars()のショートカット
function hsc($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

//本文内のURLにリンクを設定
function makeLink($value)
{
    return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", '<a href="\1\2">\1\2</a>', $value);
}

//いいねをした際の画像変更
$favorite_set = $db->prepare(
    'SELECT *
    FROM favorite
    WHERE member_id=?
    AND delete_flag=false'
);
$favorite_set->execute([$_SESSION['id']]);
//レコード何行か入ってる、ID自分、記事複数の可能性あり

// $search = $db->prepare('SELECT * 
// FROM posts,favorite 
// WHERE posts.id = favorite.favorite_post_id 
// AND delete_flag != true');

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
                <a href="logout.php">ログアウト</a>
            </div>
            <form action="" method="post">
                <dl>
                    <dt>
                        <?php echo hsc($member['name']); ?>さん、
                        メッセージをどうぞ
                    </dt>
                    <dd>
                        <textarea name="message" cols="50" rows="3">
                            <?php echo hsc($message); ?>
                        </textarea>
                        <input type="hidden" name="reply_post_id" value="<?php echo hsc($_REQUEST['res']); ?>">
                    </dd>
                </dl>

                <div>
                    <input type="submit" value="投稿する">
                </div>
            </form>

            <?php foreach ($posts as $post) { ?>
                <form action="post">
                    <input type="hidden" name="post_id" value="<?php echo $post['id'] ?>">
                </form>

                <div class="msg">
                    <img src="member_picture/<?php echo hsc($post['picture']); ?>" width="48" height="48" alt="<?php echo hsc($post['name']); ?>">

                    <p>
                        <?php echo makeLink(hsc($post['message'])); ?>
                        <span class="name">
                            (<?php echo hsc($post['name']); ?>)
                        </span>
                        [<a href="index.php?res=<?php echo hsc($post['id']); ?>">Re</a>]
                    </p>

                    <div class="button" style="padding-top:3px;">
                        <!-- いいねボタン -->
                        <a href="favorite.php?post_id=<?php echo hsc($post['id']); ?>">
                            <?php
                            $favorite_records = $favorite_set->fetch($post['id']); //その記事に対するレコード
                            $favorite_record = $favorite_records[0];
                            if ($favorite_record) {
                            ?>
                                <img src="images/star-yellow.png" width="17" height="17" alt="いいねしています">
                            <?php } else { ?>
                                <img src="images/star-gray.png" width="17" height="17" alt="いいねボタン">
                            <?php } ?>
                        </a>

                        <a href="retweet.php">
                            <img src="images/rt-gray.png" width="17" height="17" alt="リツイートボタン">
                        </a>
                    </div>

                    <p class="day">
                        <a href="view.php?id=<?php echo hsc($post['id']); ?>">
                            <?php echo hsc($post['created']); ?>
                        </a>

                        <?php if ($post['reply_post_id'] > 0) { ?>
                            <a href="view.php?id=<?php echo hsc($post['reply_post_id']); ?>">
                                返信元のメッセージ
                            </a>
                        <?php } ?>

                        <?php if ($_SESSION['id'] == $post['member_id']) { ?>
                            [<a href="delete.php?id=<?php echo hsc($post['id']); ?>" style="color:#F33;">削除</a>]
                        <?php } ?>
                    </p>


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