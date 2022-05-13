<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['username'])) {
    session_destroy();
    header('Location:login.php');
    exit;
} elseif ($_SESSION['admin'] != 1){
    session_destroy();
    header('Location:login.php');
}

if (!empty($_POST)) {
    $key = 'personalcolor';
    $inputPw = crypt($_POST['password'], $key);
    $afterPw = $_POST['after-password'];
    $afterPwCheck = $_POST['after-check-password'];

    if ($afterPw != $afterPwCheck) {
        $errorMsg = "変更後のパスワード、パスワード(確認用)が一致しません。<br>";
        exit;
    }

    try {
        $pdo = getPdb();

        $sql = "select password from cl_users where name = :name";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $_SESSION['username'], PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();

        if (!$user) {
            $errorMsg = "エラーが発生しました。<br>再ログインしてください。<br>";
        } else {
            //パスワードチェック
            if ($inputPw == $user["password"]) {
                $sql = "update cl_users set password = :password, updata_date = sysdate() where name = :name";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':password', crypt($afterPw, $key), PDO::PARAM_STR);
                $stmt->bindValue(':name', $_SESSION['username'], PDO::PARAM_STR);
                $stmt->execute();

                $message = "パスワードを変更しました。<br>";
            } else {
                $errorMsg = "現在のパスワードが正しくありません。<br>";
            }
        }
    } catch (PDOException $e) {
        print "DB Error!: " . $e->getMessage() . "<br>";
        die();
    }
}

?>



<!DOCTYPE html>
<html lang="ja-JP">

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>パーソナルカラーチェック用</title>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-144012793-1');
    </script>

    <!-- 追加 murakami -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="style.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
</head>


<?php
$filepath = 'colorlist.txt';
$fp = fopen($filepath, "r");
$i = 0;
while ($line = fgets($fp)) {
    $colors[$i] = explode('|', rtrim($line));
    $i++;
}
fclose($fp);

?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 text-right" <?echo $dispNone?>>
            <button class="btn btn-secondary my-2" onclick="location.href='logout.php'">ログアウト</button>
        </div>
        <div class="col-md-8 send-area justify-content-center">
            <div class="message-area">
                <?php
                if (isset($message)) {
                    echo "<p class='bg-success text-white rounded-sm py-2 pl-2'>";
                    echo $message;
                    echo "</p>";
                }
                ?>
                <?php
                if (isset($errorMsg)) {
                    echo "<p class='bg-danger text-white rounded-sm py-2 pl-2'>";
                    echo $errorMsg;
                    echo "</p>";
                }
                ?>
            </div>
            <div>
                <h2>パスワード変更</h2>
            </div>
            <form action="pass_edit.php" class="mt-3" method="post" onsubmit="return passCheck();">
                <label for='password'>現在のパスワード</label>
                <input type="password" name="password" id="password" class="form-control mx-3" required>
                <label for='after-password'>変更後のパスワード</label>
                <input type="password" name="after-password" id="after-password" class="form-control mx-3" required>
                <label for='after-check-password'>変更後のパスワード(確認用)</label>
                <input type="password" name="after-check-password" id="after-check-password" class="form-control mx-3" required>
                <div class="text-center">
                    <input type="submit" class="btn btn-info my-3" value="変更">
                    <br>
                    <button class="btn btn-info my-3" onclick="location.href='color_setting.php'">カラー設定に戻る</button>
                    <button class="btn btn-info my-3" onclick="location.href='login.php'">ログイン画面に戻る</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function passCheck() {
        var pw = document.getElementById('password');
        var afterPw = document.getElementById('after-password');
        var afterChkPw = document.getElementById('after-check-password');

        if (pw.value == afterChkPw.value) {
            alert('変更前と変更後パスワードが同じです。');
            return false;
        } else {
            if (afterPw.value != afterChkPw.value) {
                alert('変更後と確認用パスワードが一致しません。');
                return false;
            }
        }
    }
</script>

</html>