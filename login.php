<?php

require_once 'db_connect.php';
session_start();

$key = 'personalcolor';
$inputUname = $_POST['username'];
$inputPw = $_POST['password'];

//入力チェック
if (!empty($inputUname) && !empty($inputPw)) {
    try {
        $pdo = getPdb();

        $sql = "select * from cl_users where name = :name";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':name', $inputUname, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();

        if (!$user) {
            $errorMsg = "ユーザ名が正しくありません。<br>";
        } else {
            //パスワードチェック
            $inputPw = crypt($inputPw, $key);
            if ($inputPw == $user["password"]) {
                $_SESSION['username'] = $user["name"];
                $_SESSION['admin'] = $user["type"];
                header('Location:index.php');
                exit;
            } else {
                $errorMsg = "パスワードが正しくありません。<br>";
            }
        }
    } catch (PDOException $e) {
        print "DB Con Error!: " . $e->getMessage() . "<br>";
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

<div class="container-fluid my-5">
    <div class="row justify-content-center">
        <div class="col-md-12 text-center my-3">
            <h2>ログイン画面</h2>
        </div>
        <form action="login.php" class="mt-3 text-center" method="post">
            <div class="message-area">
                <?php
                if (isset($errorMsg)) {
                    echo "<p class='bg-danger text-white rounded-sm py-2 pl-2'>";
                    echo $errorMsg;
                    echo "</p>";
                }
                ?>
            </div>
            <div class="form-group">
                <label for='username'>ユーザ名</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for='password'>パスワード</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <input type="submit" class="btn btn-secondary my-3" value="ログイン">
            <!-- <a href="pass_edit.php" style="display:block;">パスワード変更はこちら</a> -->
        </form>
    </div>
</div>
</div>

<script>
</script>



</html>