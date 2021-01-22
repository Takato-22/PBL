<html>
<head>
<title>機械学習を用いた位置推定</title><meta charset="UTF-8">
</head>
<body>
<?php
require_once '../../vendor/autoload.php';

use Phpml\Classification\KNearestNeighbors;
use Phpml\Dataset\CsvDataset;

$rec1;
$rec2;

if (isset($_POST['learn'])){
  k1();
}
else{
  setlearnForm();
	select_learn();
  select_test();
}
?>
</body>
</html>

<?php

function setlearnForm(){	//学習ボタンの表示
  print <<< FORM
  <form action = "" method = "post">
  <table border = "0" align = "center">
  <tr>
  <td align ="center">
  <input type = "submit" name = "learn" value = "機械学習"></td>
  </tr>
  </table>
  <br>
  </form>
  FORM;
}

function select_learn(){	//データベース内の情報の一覧表示
  //データベースに接続
  $dsn='mysql:dbname=ile;host=localhost';
  $user='root'; //仮想サーバならroot
  $password=''; //仮想サーバなら空白
  $dbh=new PDO($dsn, $user, $password);
  $dbh->query('SET NAMES utf-8');
  //クエリの作成
  $sql='SELECT rssi1, rssi2, rssi3, position  FROM learn ';
  //クエリの実行
  $stmt=$dbh->prepare($sql);
  $stmt->execute();
  //表作成
  print "<table border = '1' align = 'center' width = '300'>";
  print "<tr align = 'center'>";
  print "<td>RSSI_1</td><td>RSSI_2</td><td>RSSI_3</td><td>位置</td>";
  print "</tr>";
  while(1){
    //$stmtのデータ取り出し
    $rec1=$stmt->fetch(PDO::FETCH_NUM);

    //最終行に行ったら終わり
    if($rec1==false){break;}
    print "<tr align = 'center'>";
    print "<td>".$rec1[0]."</td>";
    print "<td>".$rec1[1]."</td>";
		print "<td>".$rec1[2]."</td>";
		print "<td>".$rec1[3]."</td>";
    print "</tr>";
  }
  print "</table>";
  print "<br>";
  //③コネクションの切断
  $dbh=null;
}

function select_test(){	//データベース内の情報の一覧表示
  //データベースに接続
  $dsn='mysql:dbname=ile;host=localhost';
  $user='root'; //仮想サーバならroot
  $password=''; //仮想サーバなら空白
  $dbh=new PDO($dsn, $user, $password);
  $dbh->query('SET NAMES utf-8');
  //クエリの作成
  $sql='SELECT rssi1, rssi2, rssi3, rssi4, rssi5, rssi6, rssi7, rssi8, rssi9, rssi10, position  FROM test ';
  //クエリの実行
  $stmt=$dbh->prepare($sql);
  $stmt->execute();
  //表作成
  print "<table border = '1' align = 'center' width = '300'>";
  print "<tr align = 'center'>";
  print "<td>RSSI_1</td><td>RSSI_2</td><td>RSSI_3</td><td>RSSI_4</td><td>RSSI_5</td><td>RSSI_6</td><td>RSSI_7</td><td>RSSI_8</td><td>RSSI_9</td><td>RSSI_10</td><td>位置</td>";
  print "</tr>";
  while(1){
    //$stmtのデータ取り出し
    $rec2=$stmt->fetch(PDO::FETCH_NUM);

    //最終行に行ったら終わり
    if($rec2==false){break;}
    print "<tr align = 'center'>";
    print "<td>".$rec2[0]."</td>";
    print "<td>".$rec2[1]."</td>";
		print "<td>".$rec2[2]."</td>";
		print "<td>".$rec2[3]."</td>";
		print "<td>".$rec2[4]."</td>";
		print "<td>".$rec2[5]."</td>";
		print "<td>".$rec2[6]."</td>";
		print "<td>".$rec2[8]."</td>";
		print "<td>".$rec2[7]."</td>";
		print "<td>".$rec2[9]."</td>";
		print "<td>".$rec2[10]."</td>";

    print "</tr>";
  }
  print "</table>";
  print "<br>";
	print "<form action='index.html' method='post' align = 'center'>";
  print "<input type='submit' value='戻る' />";
  print "</form>";
  //③コネクションの切断
  $dbh=null;
}

function k1(){

  $classifier = new KNearestNeighbors();
  $classifier->train($rec1->getSamples(), $rec1->getTargets());

  echo "テストデータ数 : ".count($rec2->getSamples());
  echo "<br />";
  for($i=0; $i<count($rec2->getSamples()); ++$i){
  echo "[".($i+1)."]　";
  echo "最小値は".min($rec2->getSamples()[$i]);
  $minindex = array_search(min($rec2->getSamples()[$i]), $rec2->getSamples()[$i]);
  echo "　最小値の位置は".$minindex."　";
  if($minindex == 0){
    $minindex = 1;
  }
  elseif($minindex == 9){
    $minindex = 8;
  }
  $data = array_slice($rec2->getSamples()[$i], ($minindex-1), 3);
  print_r($data);
  echo "　　推定結果は".$classifier->predict($data);
  echo "<br />";
}
}
?>
