<?php
session_start();
require_once('../function/dbconnect.php');
require_once('../function/shortcut_htmlspecialchars.php');

//アップロードファイルのmime_typeを検査
$config['ALLOW_MIME'] = ['image/jpeg', 'image/png', 'image/gif'];
function checkMime($filename)
{
    global $config;
    $mime = mime_content_type($filename);
    return in_array($mime, $config['ALLOW_MIME']);
}

if (!empty($_POST)) {
    //エラー項目の確認
    if ($_POST['name'] == '') {
        $error['name'] = 'blank';
    }
    if ($_POST['email'] == '') {
        $error['email'] = 'blank';
    }
    if (strlen($_POST['password']) < 4) {
        $error['password'] = 'length';
    }
    if ($_POST['password'] == '') {
        $error['password'] = 'blank';
    }
    $fileName = $_FILES['image']['name'];
    if (!empty($fileName)) {
        $ext = substr($fileName, -3);
        if ($ext != 'jpg' && $ext != 'gif') {
            $error['image'] = 'type';
        }
        if (!checkMime($_FILES['image']['tmp_name'])) {
            $error['image'] = 'mime-error';
        }
    }


    //重複アカウントのチェック
    if (empty($error)) {
        $member = $db->prepare('SELECT COUNT(*) AS member-count
        FROM members
        WHERE email=?');
        $member->execute([$_POST['email']]);
        $record = $member->fetch();
        if ($record['member-count'] > 0) {
            $error['email'] = 'duplicate';
        }
    }

    if (empty($error)) {
        //画像をアップロードする
        $image = date('YmdHis') . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], 'member_picture/' . $image);
        $_SESSION['join'] = $_POST;
        $_SESSION['join']['image'] = $image;
        header('Location: check.php');
        exit();
    }
}

//書き直し
if ($_REQUEST['action'] == 'rewrite') {
    $_POST = $_SESSION['join'];
    $error['rewrite'] = true;
}
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
            <h1>ログインする</h1>
        </div>
        <div id="content">
            <p>次のフォームに必要事項をご記入下さい。</p>
            <form action="" method="post" enctype="multipart/form-data">
                <dl>
                    <dt>
                        ニックネーム<span class="required">必須</span>
                    </dt>
                    <dd>
                        <input type="text" name="name" size="35" maxlength="255" value="<?php echo h($_POST['name']); ?>" />
                        <?php if ($error['name'] == 'blank') : ?>
                            <p class="error">※ ニックネームを入力して下さい</p>
                        <?php endif; ?>
                    </dd>
                    <dt>
                        メールアドレス<span class="required">必須</span>
                    </dt>
                    <dd>
                        <input type="text" name="email" size="35" maxlength="255" value="<?php echo h($_POST['email']); ?>" />
                        <?php if ($error['email'] == 'blank') : ?>
                            <p class="error">※ メールアドレスを入力して下さい</p>
                        <?php endif; ?>
                        <?php if ($error['email'] == 'duplicate') : ?>
                            <p class="error">※ 指定されたメールアドレスは既に登録されています</p>
                        <?php endif; ?>
                    </dd>
                    <dt>
                        パスワード<span class="required">必須</span>
                    </dt>
                    <dd>
                        <input type="password" name="password" size="10" maxlength="20" value="<?php echo h($_POST['password']); ?>" />
                        <?php if ($error['password'] == 'blank') : ?>
                            <p class="error">※ パスワードを入力して下さい</p>
                        <?php endif; ?>
                        <?php if ($error['password'] == 'length') : ?>
                            <p class="error">※ パスワードは4文字以上で入力してください</p>
                        <?php endif; ?>
                    </dd>
                    <dt>
                        写真など<span class="required">必須</span>
                        <?php if ($error['image'] == 'type') : ?>
                            <p class="error">※ 写真などは「.gif」または「.jpg」の画像を指定してください</p>
                        <?php endif; ?>
                        <?php if (!empty($error)) : ?>
                            <p class="error">※ 恐れ入りますが、画像を改めて指定してください</p>
                        <?php endif; ?>
                    </dt>
                    <dd>
                        <input type="file" name="image" size="35" />
                    </dd>
                </dl>
                <div>
                    <input type="submit" value="入力内容を確認する">
                </div>
            </form>
        </div>

    </div>
</body>

</html>