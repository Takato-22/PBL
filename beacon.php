<html>
<head>
  <title>Beacon情報管理画面</title><meta charset="UTF-8">
</head>
<center>
  <h1>Beacon情報管理画面</h1>
  <?php
  //uuidとpositionが入力されたときにそのデータをデータベースに登録
  if(isset($_POST['uuid']) && isset($_POST['position'])){
    registerData();
  //削除したいuuidが入力されたときにそのデータベースから削除
  }else if(isset($_POST['del'])){
    deleteData();
  }
  registerForm();
  setDeleteForm();
  select();
  ?>
</center>
</body>
</html>

<?php
function registerForm(){    //uuidとpositionをデータベースに登録するフォームの表示
  print <<< FORM
  <form action = "" method = "post">
  <div align="center" style="padding: 20px;">
  <a name="submit">Beacon情報を入力し登録して下さい</a>
  </div>
  <!--uuidの入力フォーム-->
  <table border = "1" align = "center">
  <tr>
  <td align ="center" style="padding: 0px 20px;">UUID</td>
  <td><input type="text" name = "uuid"></td>
  </tr>
  <!--positionの入力フォーム-->
  <tr>
  <td align ="center">位置</td>
  <td><input type="text" name = "position"></td>
  </tr>
  <!--登録ボタン-->
  <tr>
  <td colspan = "2" align ="center">
  <input type = "submit" value = "登録"></td>
  </tr>
  </table>
  <br>
  </form>
  FORM;
}

function setDeleteForm(){	//入力されたuuidに対応するデータをデータベースから削除するフォームの表示
  print <<< FORM
  <form action = "" method = "post">
  <div align="center" style="padding: 20px;">
  <a name="deleate">削除したいデータのUUIDを入力して下さい</a>
  </div>
  <!--uuidの入力フォーム-->
  <table border = "1" align = "center">
  <tr>
  <td>UUID</td>
  <td><input type="text" name = "del"></td>
  </tr>
  <!--削除ボタン-->
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
  $uuid=$_POST['uuid'];
  $position=$_POST['position'];
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
  //ホーム画面に戻るボタンの表示
  print "<form action='index.html' method='post'>";
  print "<input type='submit' value='戻る' />";
  print "</form>";
  //③コネクションの切断
  $dbh=null;
}
?>
