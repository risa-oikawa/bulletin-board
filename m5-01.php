<?php
// ・データベース名：tb220924db
// ・ユーザー名：tb-220924
// ・パスワード：nQx3kNambu：

// DB接続設定 //
// $dsn = 'mysql:dbname=tb220924db;host=localhost';
// $user = 'tb-220924';
// $password = 'nQx3kNambu';

$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
//PDO: これを使えばデータベース(DB)に接続できる
//「 array(PDO:〜_WARNING) 」: 
//データベース操作でエラーが発生した場合に警告（Worning: ）
//として表示するために設定するオプション

//DB内にアクセスするために，PODクラス（設計図）をインスタンス化(実体化（設計図から家を作り出す）)する
//($変数名 = new クラス名（引数）)
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//各種変数への代入
error_reporting(0);
$yourname = $_POST["yourname"];
$txt = $_POST["txt"];
$del = $_POST["delete"];
$edit = $_POST["edit"];
$edit_num = $_POST["edit_num"];
$trigger = 0;
$pw_form = $_POST["pw"];
$cfm_pw_del = $_POST["cfm_pw_del"];
$cfm_pw_edit = $_POST["cfm_pw_edit"];

//送信フォーム処理　　// 4-5  データの挿入(ブラウザには表示はされない)
//新規投稿モード
if(!empty($yourname) && !empty($txt) && empty($edit_num) && !empty($pw_form)){
    // INSERT文：データを入力（データレコードの挿入）//
    //bindparam: 変数への参照をバインド(バインド：AとBを結びつける，AをBに割り当てる) / 指定された変数名にパラメータをバインドする
    $sql = $pdo -> prepare("INSERT INTO tbtest_abc (name, comment, date, pw) VALUES (:name, :comment, now(), :pw)");
    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql -> bindParam(':pw', $pw, PDO::PARAM_STR);
    $name = $yourname;
    $comment = $txt; //好きな名前、好きな言葉は自分で決めること
    $pw = $pw_form;
    $sql -> execute(); //プリペアドステートメントを実行する
}

//編集モード(if(empty($edit_num))の時
if(!empty($yourname) && !empty($txt) && !empty($edit_num) && !empty($pw_form)){
    //編集したい投稿番号と取り出した要素の投稿番号が一致する場合，書き換える
    $id = $edit_num; //変更する投稿番号
    $name = $yourname;
    $comment = $txt; //変更したい名前、変更したいコメントは自分で決めること
    $sql = 'UPDATE tbtest_abc SET name=:name,comment=:comment,date=now() WHERE id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute(); //プリペアドステートメントを実行する
    $trigger = 1;
}


//削除フォーム処理　　// 4-8  データの削除(ブラウザには表示されない4-6を合わせることで確認)
if(!empty($del) && !empty($cfm_pw_del)){
    //if(投稿番号$delのパスワード == $cfm_pw_del){
    $sql = 'SELECT * FROM tbtest_abc';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();  //全ての結果行を含む配列を返す
    foreach ($results as $row){
        if($row['pw'] == $cfm_pw_del){
            $id = $del;
            $sql = 'delete from tbtest_abc where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
    } 
}        


//編集番号読み込み・フォームへの書き込み処理　　// 4-7  データの更新(ブラウザには表示されないため4-6を合わせることで確認)
if(!empty($edit) && !empty($cfm_pw_edit)){
    // if($cfm_pw == $pw_form){
        $sql = 'SELECT * FROM tbtest_abc';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();  //全ての結果行を含む配列を返す
        foreach ($results as $row){
            if($row['pw'] == $cfm_pw_edit){
                if($row['id'] == $edit){
                    //$rowの中にはテーブルのカラム名が入る
                    $edit_num = $row['id'];
                    $edit_name = $row['name'];
                    $edit_comment = $row['comment'];
                }
            }
        }
    // }
}

?>
<!-- //投稿フォーム　　// そのまま使う -->
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>Mission5-01</title>
    </head>
    <body>
        <!--入力フォーム-->
        <form action=""method="post">
            <!--type属性: <input>要素の見た目や動作の指定-->
            <!--name属性: データに名前をつける-->
            <!--編集番号欄の入力-->
            <input type="hidden" name="edit_num" placeholder="編集投稿番号" value="<?php if($trigger == 0){echo $edit_num;}?>"><br>
            <!--名前の入力-->
            <input type="text" name="yourname" placeholder="名前" value="<?php if(isset($edit)){echo $edit_name;}?>"><br>
            <!--コメント欄の入力-->
            <input type="text" name="txt" placeholder="コメント" value="<?php if(isset($edit)){echo $edit_comment;}?>"><br>
            <!--パスワードの入力-->
            <input type="text" name="pw" placeholder="パスワード"> 
            <!--送信ボタン-->
            <button type="submit">送信</button>
        </form>
        <!--削除番号指定用フォーム-->
        <form action=""method="post">
            <!--削除対象番号の入力-->
            <input type="text" name="delete" placeholder="削除対象番号">
            <!--パスワードの入力-->
            <input type="text" name="cfm_pw_del" placeholder="パスワード" value="">
            <!--送信ボタン-->
            <button type="submit">削除</button>
        </form>
        <!--編集番号指定用フォーム-->
        <form action=""method="post">
            <!--編集対象番号の入力-->
            <input type="text" name="edit" placeholder="編集対象番号">
            <!--パスワードの入力-->
            <input type="text" name="cfm_pw_edit" placeholder="パスワード" value="">
            <!--送信ボタン-->
            <button type="submit">編集</button>
        </form>
        </body>
</html>

<?php
//投稿表示　　// 4-6  データの表示

//SELECT文：入力したデータレコードを抽出し、表示する
$sql = 'SELECT * FROM tbtest_abc';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();  //全ての結果行を含む配列を返す
foreach ($results as $row){
    //$rowの中にはテーブルのカラム名が入る
    echo $row['id'].',';
    echo $row['name'].',';
    echo $row['comment'].',';
    echo $row['date'].'<br>';
echo "<hr>";
}
?>