<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['username'])) {
    session_destroy();
    header('Location:login.php');
    exit;
} elseif ($_SESSION['admin'] != 1) {
    session_destroy();
    header('Location:login.php');
}

if (!empty($_POST) && !empty($_POST['password'])) {
    $key = 'personalcolor';
    $inputPw = crypt($_POST['password'], $key);

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
                //カラー設定変更
                $colorTypes = array(1 => 'pink', 2 => 'red', 3 => 'green', 4 => 'blue');
                $count = 1;
                foreach ($_POST as $key => $value) {
                    foreach ($colorTypes as $colorType) {
                        if (strpos($key, $colorType) !== false) {
                            $content .= $value;
                            if (($count % 4) == 0) {
                                $content .= "\n";
                            } else {
                                $content .= '|';
                            }
                            $count++;
                        }
                    }
                }

                //カラー設定ファイル出力
                $createFile = 'colorlist.txt';
                $content = $content;
                file_put_contents($createFile, $content);

                $message = "カラー設定を変更しました。<br>";

            } else {
                $errorMsg = "パスワードが正しくありません。<br>";
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

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 text-right">
            <button class="btn btn-secondary my-2" onclick="location.href='logout.php'">ログアウト</button>
        </div>
        <div class="col-md-12 send-area">
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
                <h2>カラー設定</h2>
            </div>
            <div>
                <button class="btn btn-info" onclick="location.href='index.php'">戻る</button>
            </div>
        </div>
    </div>
</div>

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
        <form action="color_setting.php" class="w-100" method="post" onsubmit="return update();">
            <!-- ピンク -->
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="color-type">
                            <h2 style="background: linear-gradient(transparent 70%, #ffa7f8 70%)">Pink</h2>
                        </div>
                    </div>

                    <?php
                    $i = 1;
                    foreach ($colors[0] as $color) {
                        echo "<div class='col-md-3 text-center'>\n";
                        echo "<div class='form-group'>";
                        $label = "<label for=''>ピンク{$i}</label>\n";
                        $text = "<input type='text' class='form-control' id='pink{$i}txt' value='{$color}' onchange='setColor(this)'>\n";
                        $color = "<input type='color' class='form-control' name='pink{$i}' id='pink{$i}' value='{$color}' onchange='setColor(this)'>\n";
                        echo $label . $color . $text;
                        echo "</div>\n</div>\n";
                        $i++;
                    }
                    ?>
                </div>
            </div>

            <!-- レッド -->
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="color-type">
                            <h2 style="background: linear-gradient(transparent 70%, #ffa7a7 70%)">Red</h2>
                        </div>
                    </div>

                    <?php
                    $i = 1;
                    foreach ($colors[1] as $color) {
                        echo "<div class='col-md-3 text-center'>\n";  // style='background: {$color};
                        echo "<div class='form-group'>";
                        $label = "<label for=''>レッド{$i}</label>\n";
                        $text = "<input type='text' class='form-control' id='red{$i}txt' value='{$color}' onchange='setColor(this)'>\n";
                        $color = "<input type='color' class='form-control' name='red{$i}' id='red{$i}' value='{$color}' onchange='setColor(this)'>\n";
                        echo $label . $color . $text;
                        echo "</div>\n</div>\n";
                        $i++;
                    }
                    ?>
                </div>
            </div>

            <!-- グリーン -->
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="color-type">
                            <h2 style="background: linear-gradient(transparent 70%, #a7ffbe 70%)">Green</h2>
                        </div>
                    </div>

                    <?php
                    $i = 1;
                    foreach ($colors[2] as $color) {
                        echo "<div class='col-md-3 text-center'>\n";  // style='background: {$color};
                        $label = "<label for=''>グリーン{$i}</label>\n";
                        $text = "<input type='text' class='form-control' id='green{$i}txt' value='{$color}' onchange='setColor(this)'>\n";
                        $color = "<input type='color' class='form-control' name='green{$i}' id='green{$i}' value='{$color}' onchange='setColor(this)'>\n";
                        echo $label . $color . $text;
                        echo "</div>\n";
                        $i++;
                    }
                    ?>
                </div>
            </div>

            <!-- ブルー -->
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="color-type">
                            <h2 style="background: linear-gradient(transparent 70%, #a7d6ff 70%)">Blue</h2>
                        </div>
                    </div>

                    <?php
                    $i = 1;
                    foreach ($colors[3] as $color) {
                        echo "<div class='col-md-3 text-center'>\n";  // style='background: {$color};
                        $label = "<label for=''>ブルー{$i}</label>\n";
                        $text = "<input type='text' class='form-control' id='blue{$i}txt' value='{$color}' onchange='setColor(this)'>\n";
                        $color = "<input type='color' class='form-control form-control-color' name='blue{$i}' id='blue{$i}' value='{$color}' onchange='setColor(this)'>\n";
                        echo $label . $color . $text;
                        echo "</div>\n";
                        $i++;
                    }
                    ?>
                    <div class="col-md-3 send-area text-center">
                        <label for='password'>パスワード</label>
                        <input type="password" id="password" name="password" class="form-control my-3" required>
                        <input type="submit" class="btn btn-info my-3" value="設定変更">
                        <a href="pass_edit.php" style="display:block;">パスワード変更はこちら</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function setColor(elm) {
        colValue = elm.value;
        type = elm.type;
        if (type == 'color') {
            document.getElementById(elm.id + 'txt').value = colValue;
            //document.getElementById(elm.id + 'div').style.background = colValue;
        } else if (type == 'text') {
            //elm.id.replace('txt', '');
            document.getElementById(elm.id).value = colValue;
        }
    }

    function update() {
        var pw = document.getElementById('password');
        if (empty(pw.value)) {
            alert('パスワードを入力してください。');
            return false;
        }
    }
</script>

</html>