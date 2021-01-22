<html>
<head>
<title>機械学習を用いた位置推定</title><meta charset="UTF-8">
</head>
<body>
<?php
require_once '../../vendor/autoload.php';

use Phpml\Classification\KNearestNeighbors;
use Phpml\Dataset\CsvDataset;

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
  $sql1='SELECT rssi1, rssi2, rssi3, position  FROM learn ';
  $sql2='SELECT rssi1, rssi2, rssi3, rssi4, rssi5, rssi6, rssi7, rssi8, rssi9, rssi10, position  FROM test ';
  //クエリの実行
  $stmt1=$dbh->prepare($sql1);
  $stmt2=$dbh->prepare($sql2);
  $stmt1->execute();
  $stmt2->execute();

  $i=0;
  while(1){
    //$stmtのデータ取り出し
    $rec1=$stmt1->fetch(PDO::FETCH_NUM);
    $rec2=$stmt2->fetch(PDO::FETCH_NUM);

    for($j=0; $j<3; $j++){
      $rec1sample[$i][$j] = $rec1[$j];
    }
    for($j=0; $j<10; $j++){
      $rec2sample[$i][$j] = $rec2[$j];
    }
    $i++;
  }

  //③コネクションの切断
  $dbh=null;

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
