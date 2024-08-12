<?php

// DB接続設定

$dsn = 'mysql:dbname=データベース名;host=localhost';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

$sql = "CREATE TABLE IF NOT EXISTS pswkeijiban"
." ("
. "id INT AUTO_INCREMENT PRIMARY KEY,"
. "name CHAR(32),"
. "comment TEXT,"
. "date TEXT,"
. "psw_id TEXT"
.");"; 

$stmt = $pdo->query($sql);

//投稿機能
if(isset($_POST['submit1'])){
    //新規投稿モード
    
    if(empty($_POST["id"])&&!empty($_POST["name"])&&!empty($_POST["comment"])&&!empty($_POST["psw_id"])){
        $name=$_POST["name"];
        $comment=$_POST["comment"];
        $date=date("Y/m/d H:i:s");
        $psw_id=$_POST["psw_id"];

        $sql = "INSERT INTO pswkeijiban (name, comment, date, psw_id) VALUES (:name, :comment, :date, :psw_id)";
        $stmt = $pdo->prepare($sql);
        //プレースホルダに変数を宛がう
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':psw_id', $psw_id, PDO::PARAM_STR); 
    
        //実行
        $stmt->execute();
    }
    //編集投稿モード
    elseif(!empty($_POST["id"])&&!empty($_POST["name"])&&!empty($_POST["comment"])&&!empty($_POST["psw_id"])){
        
        $id=$_POST["id"];
        $name=$_POST["name"];
        $comment=$_POST["comment"];
        $date=date("Y/m/d H:i:s");
        $psw_id=$_POST["psw_id"];

        $sql = 'UPDATE pswkeijiban SET name=:name,comment=:comment,psw_id=:psw_id WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        //プレースホルダに変数を宛がう
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':psw_id', $psw_id, PDO::PARAM_INT); 

        //実行
        $stmt->execute();

    }

}

//削除機能
elseif(isset($_POST['submit2'])){
    $del_id=$_POST["d-id"];
    $del_psw=$_POST["d-psw"];

    //該当の名前とコメントを取得
    $sql = 'SELECT * FROM pswkeijiban WHERE id=:id ';
    $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
    $stmt->bindParam(':id', $del_id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定し（数値なので  PDO::PARAM_INT）、
    $stmt->execute();                             // ←SQLを実行する。
    $results = $stmt->fetchAll();

    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        $psw=$row['psw_id'];
    }

    if($psw==$del_psw){
        $sql = 'delete from pswkeijiban where id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $del_id, PDO::PARAM_INT); 

        //実行
        $stmt->execute();
    }

    else{
        echo "パスワードが一致していません";
    }
    

}

//編集機能
elseif(isset($_POST['submit3'])){
    $edi_id=$_POST["e-id"];
    $edi_psw=$_POST["e-psw"];

    //該当の名前とコメントを取得
    $sql = 'SELECT * FROM pswkeijiban WHERE id=:id ';
    $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
    $stmt->bindParam(':id', $edi_id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定し（数値なので  PDO::PARAM_INT）、
    $stmt->execute();                             // ←SQLを実行する。
    $results = $stmt->fetchAll();

    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        $psw_id=$row['psw_id'];
    }

    
    if($edi_psw==$psw_id){
        //該当の名前とコメントを取得
        $sql = 'SELECT * FROM pswkeijiban WHERE id=:id ';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $edi_id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定し（数値なので  PDO::PARAM_INT）、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetchAll();

        foreach ($results as $row){
            //$rowの中にはテーブルのカラム名が入る
            $edi_name=$row['name'];
            $edi_comment=$row['comment'];

        }

    }

    else{
        echo "パスワードが一致していません";
        $edi_id="";
        $edi_name="";
        $edi_comment="";
        $edi_psw="";
    }



}


?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>簡易掲示板</title>
<style>
div{
    border:dashed 2px #A8B2B1;
    border-radius: 5px;
    padding: 5px;
    margin: 3px;
    text-align: left;
}
</style>
</head>
<body>
<form action="" method="post">
<!-- 投稿の送信 -->
<input type="hidden" name="id" 
        value="<?php 
        if (!empty($_POST["e-id"])) {echo $edi_id;} ?>"
        style="width:30px; height:15px">
<input type="text" name="name" placeholder="名前" 
        value="<?php 
        if (!empty($_POST["e-id"])) {echo $edi_name;} ?>" 
        style="width:150px; height:15px">
<input type="text" name="psw_id" placeholder="password" 
        value="<?php 
        if (!empty($_POST["e-id"])) {echo $edi_psw;} ?>" 
        style="width:150px; height:15px"><br>
<input type="text" name="comment" placeholder="コメント" 
        value="<?php if (!empty($_POST["e-id"])) {echo $edi_comment;} ?>"
        style="width:500px; height:40px"><br>
<input type="submit" name="submit1"><br><br>

<!-- 削除番号の送信 -->
<input type="number" name="d-id" placeholder="削除したい行番号" style="width:130px; height:15px">
<input type="text" name="d-psw" placeholder="password" 
        style="width:150px; height:15px">
<input type="submit" name="submit2"><br><br>

<!-- 編集番号の送信 -->
<input type="number" name="e-id" placeholder="編集対象番号" style="width:130px; height:15px">
<input type="text" name="e-psw" placeholder="password" 
        style="width:150px; height:15px">
<input type="submit" name="submit3" value="編集">


</form>
</body>
</html>

<?php

echo "<br>";
echo "【投稿履歴】<br>";

$sql = 'SELECT * FROM pswkeijiban';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll(); 

//ループして、取得したデータを表示
foreach ($results as $row){
    //$rowの中にはテーブルのカラム名が入る
    echo "<div style='fontsize: 12px;'>".
    $row['id'].
    " <strong>".$row['name']."</strong> | ".
    $row['date']."<br>".
    $row['comment'].' <br>'.
    "</div>";

}


?>