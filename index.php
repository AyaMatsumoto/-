<?php

session_start();
if (!isset($_SESSION['username'])) {
    header('Location:login.php');
    exit;
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

<div class="container-fluid">
    <div class="row justify-content-center text-center">
        <div class="col-md-12 text-right">
            <button class="btn btn-secondary my-2" onclick="location.href='logout.php'">ログアウト</button>
        </div>

        <div class="col-md-12">
            <input type="file" id="select-file" class="btn btn-info">
            <textarea id="deta-url" readonly style="display: none;"></textarea>
        </div>

        <!-- 連続切り替え表示 -->
        <div class="col-md-12">
            <div id="image_movie">
                <img src="noimage.png" id="img-disp" class="img-responsive inside-img_movie" style="border-color: #ccc;">
            </div>
        </div>
        <div class="col-md-12">
            <button class="btn btn-info my-2" onclick="changeColor(this);">連続</button>
            <? if ($_SESSION['admin'] == 1) { ?>
                <button class="btn btn-info my-2" onclick="location.href='color_setting.php'">カラー設定変更</button>
                <a href="pass_edit.php" style="display:block;">パスワード変更はこちら</a>
            <? } ?>

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

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="color-type">
                <h2 style="background: linear-gradient(transparent 70%, #ffa7f8 70%)">Pink</h2>
            </div>
        </div>
        <?php
        foreach ($colors[0] as $color) {
            $data = "<div class='color-box col-md-3 col-12 image_fixed' style='background-color: {$color};'></div>\n";
            echo $data;
        }
        ?>
        <div class="col-md-12">
            <div class="color-type">
                <h2 style="background: linear-gradient(transparent 70%, #ffa7a7 70%)">Red</h2>
            </div>
        </div>
        <?php
        foreach ($colors[1] as $color) {
            $data = "<div class='color-box col-md-3 col-12 image_fixed' style='background-color: {$color};'></div>";
            echo $data;
        }
        ?>
        <div class="col-md-12">
            <div class="color-type">
                <h2 style="background: linear-gradient(transparent 70%, #a7ffbe 70%)">Green</h2>
            </div>
        </div>
        <?php
        foreach ($colors[2] as $color) {
            $data = "<div class='color-box col-md-3 col-12 image_fixed' style='background-color: {$color};'></div>";
            echo $data;
        }
        ?>
        <div class="col-md-12">
            <div class="color-type">
                <h2 style="background: linear-gradient(transparent 70%, #a7d6ff 70%)">Blue</h2>
            </div>
        </div>
        <?php
        foreach ($colors[3] as $color) {
            $data = "<div class='color-box col-md-3 col-12 image_fixed' style='background-color: {$color};'></div>";
            echo $data;
        }
        ?>
    </div>
</div>
</div>

<script>
    //連続切り替え表示
    colors = new Array();

    function changeColor(btn) {
        btn.textContent = '停止';
        btn.disabled = true;
        var img = document.getElementById('img-disp');
        if (!colors.length) {
            <?php
            foreach ($colors as $color) {
                foreach ($color as $value) {
                    echo "colors.push('{$value}');";
                }
            }
            //最後の色
            echo "colors.push('#ccc');";
            ?>
        }

        let interval = 1000; //one second
        colors.forEach((color, index) => {
            setTimeout(() => {
                console.log(color)
                img.style.borderColor = color;
                img.style.backgroundColor = color;
            }, index * interval)
        })


        btn.textContent = '連続';
        btn.disabled = false;

    }

    //選択画像表示
    var obj = document.getElementById("select-file");
    obj.addEventListener("change", function(evt) {
        var file = evt.target.files;
        var reader = new FileReader();

        //dataURL形式でファイルを読み込む
        reader.readAsDataURL(file[0]);

        //ファイルの読込が終了した時の処理
        reader.onload = function() {
            var dataUrl = reader.result;
            var imgDisp = document.getElementById("img-disp");
            imgDisp.src = dataUrl;

            // 指定した要素にimg要素を挿入
            var imageFixed = document.getElementsByClassName('image_fixed');
            for (var i = 0; i < imageFixed.length; i++) {
                child = imageFixed[i].children;
                //要素がある場合は削除
                for (var j = child.length; j >= 0; j--) {
                    removeElm = imageFixed[i].children[j];
                    if (removeElm) {
                        imageFixed[i].removeChild(removeElm);
                    }
                }

                setColor = imageFixed[i].style.backgroundColor;
                // img要素を作成
                var imageElm = document.createElement('img');
                imageElm.src = dataUrl;
                imageElm.alt = ''; // 代替テキスト
                imageElm.className = 'inside-img';
                // imageElm.style.border = setColor + ' 80px solid';
                imageFixed[i].appendChild(imageElm);
            }

        }
    }, false);
</script>


</html>