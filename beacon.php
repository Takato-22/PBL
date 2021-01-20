<html>
<head>
<title>Beacon情報管理画面</title><meta charset="UTF-8">
</head>
<center>
  <h1>Beacon情報管理画面</h1>
  <?php
  if(isset($_POST['add1']) && isset($_POST['add2'])){
  registerData();
  setInputForm();
  setDeleteForm();
  select();
}else if(isset($_POST['del'])){
  deleteData();
  setInputForm();
  setDeleteForm();
  select();
}
else{
  setInputForm();
  setDeleteForm();
  select();
}
?>
</center>
</body>
</html>

<?php
function setInputForm(){    //入力フォームの表示
  print <<< FORM
  <form action = "" method = "post">
  <div align="center" style="padding: 20px;">
  <a name="submit">Beacon情報を入力し登録して下さい</a>
  </div>
  <table border = "1" align = "center">
  <tr>
  <td align ="center" style="padding: 0px 20px;">UUID</td>
  <td><input type="text" name = "add1"></td>
  </tr>
  <tr>
  <td align ="center">位置</td>
  <td><input type="text" name = "add2"></td>
  </tr>
  <tr>
  <td colspan = "2" align ="center">
  <input type = "submit" value = "登録"></td>
  </tr>
  </table>
  <br>
  </form>
  FORM;
}

function setDeleteForm(){	//削除フォームの表示
  print <<< FORM
  <form action = "" method = "post">
  <div align="center" style="padding: 20px;">
  <a name="deleate">削除したいデータのUUIDを入力して下さい</a>
  </div>
  <table border = "1" align = "center">
  <tr>
  <td>UUID</td>
  <td><input type="text" name = "del"></td>
  </tr>
  <tr>
  <td colspan = "2" align ="center">
  <input type = "submit" value = "削除"></td>
  </tr>
  </table>
  <br>
  </form>
  FORM;
}

function registerData(){    //データの登録
  //データベースに接続
  $dsn='mysql:dbname=ile;host=localhost';
  $user='root'; //仮想サーバならroot
  $password=''; //仮想サーバなら空白
  $dbh=new PDO($dsn, $user, $password);
  $dbh->query('SET NAMES utf-8');
  //値受け取り
  $uuid=$_POST['add1'];
  $position=$_POST['add2'];
  //クエリの作成
  $sql="INSERT INTO beacon(uuid, position) values($uuid, $position) ";
  //クエリの実行
  $stmt=$dbh->prepare($sql);
  $stmt->execute();
  //コネクションの切断
  $dbh=null;
}

function deleteData(){
  //データベースに接続
  $dsn='mysql:dbname=ile;host=localhost';
  $user='root'; //仮想サーバならroot
  $password=''; //仮想サーバなら空白
  $dbh=new PDO($dsn, $user, $password);
  $dbh->query('SET NAMES utf-8');
  //値受け取り
  $uuid=$_POST['del'];
  //クエリの作成
  $sql="delete from beacon where uuid=$uuid";
  //クエリの実行
  $stmt=$dbh->prepare($sql);
  $stmt->execute();
  //コネクションの切断
  $dbh=null;
}

function select(){	//データベース内の情報の一覧表示
  //データベースに接続
  $dsn='mysql:dbname=ile;host=localhost';
  $user='root'; //仮想サーバならroot
  $password=''; //仮想サーバなら空白
  $dbh=new PDO($dsn, $user, $password);
  $dbh->query('SET NAMES utf-8');
  //クエリの作成
  $sql='SELECT uuid, position FROM beacon ';
  //クエリの実行
  $stmt=$dbh->prepare($sql);
  $stmt->execute();
  //表作成
  print "<table border = '1' align = 'center' width = '200'>";
  print "<tr align = 'center'>";
  print "<td>UUID</td><td>位置</td>";
  print "</tr>";
  while(1){
    //$stmtのデータ取り出し
    $rec=$stmt->fetch(PDO::FETCH_NUM);

    //最終行に行ったら終わり
    if($rec==false){break;}
    print "<tr align = 'center'>";
    print "<td>".$rec[0]."</td>";
    print "<td>".$rec[1]."</td>";
    print "</tr>";
  }
  print "</table>";
  print "<br>";
  print "<form action='index.html' method='post'>";
  print "<input type='submit' value='戻る' />";
  print "</form>";
  //③コネクションの切断
  $dbh=null;
}
?>
