<html>
<head>
  <title>機械学習を用いた位置推定</title><meta charset="UTF-8">
</head>
<body>
  <?php
  require_once '../../vendor/autoload.php';

  use Phpml\Classification\KNearestNeighbors;
  back();
  select_learn();
  ?>
</body>
</html>

<?php
function back(){ //ホーム画面に戻るボタンの表示
  print "</table>";
  print "<br>";
  print "<form action='index.html' method='post' align = 'center'>";
  print "<input type='submit' value='戻る' />";
  print "</form>";
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

    //最終行に行ったら終わり
    if($rec1==false){break;}

    //$rec1のサンプルデータとラベルを配列に格納
    for($j=0; $j<3; $j++){
      $rec1sample[$i][$j] = (int)$rec1[$j];
    }
    $rec1target[$i] = (int)$rec1[3];
    $i++;
  }

  $i=0;
  while(1){
    //$stmtのデータ取り出し
    $rec2=$stmt2->fetch(PDO::FETCH_NUM);

    //最終行に行ったら終わり
    if($rec2==false){break;}

    //$rec2のサンプルデータとラベルを配列に格納
    for($j=0; $j<10; $j++){
      $rec2sample[$i][$j] = (int)$rec2[$j];
    }
    $rec2target[$i] = (int)$rec2[10];
    $i++;
  }

  //③コネクションの切断
  $dbh=null;

  //K近傍法を設定
  $classifier = new KNearestNeighbors();
  //学習データを機械学習
  $classifier->train($rec1sample, $rec1target);

  //テストデータの数をカウント
  echo "<center>テストデータ数 : ".count($rec2sample)."</center>";
  echo "<br />";
  $sum = 0;

  print "<table border = '1' align = 'center' width = '500'>";
  print "<tr align = 'center'>";
  print "<td>データ番号</td><td>推定位置</td><td>実際の位置</td><td>絶対誤差</td>";
  print "</tr>";

  for($i=0; $i<count($rec2sample); ++$i){
    // //最小値の表示
    // echo "最大値は".max($rec2sample[$i]);
    //$rec2sampleの中から最大値を取り出し、基準となるビーコンの位置を変数に格納
    $midindex = array_search(max($rec2sample[$i]), $rec2sample[$i]);
    //端のビーコンが最大値の場合、一つ内側の値を基準となるビーコンとする
    if($midindex == 0){
      $midindex = 1;
    }
    elseif($midindex == 9){
      $midindex = 8;
    }

    // print_r($rec2sample[$i]);
    //推定に使用する電波強度の最も強いビーコンとその両隣のRSSIのデータを格納
    $data = array_slice($rec2sample[$i], ($midindex-1), 3);

    //位置推定の実行
    $estResult = $classifier->predict($data);

    //推定結果を算出
    $estimate = (($midindex - 1) * 5) + (($estResult - 1) * 1) + 1;

    // //データの番号を表示
    // echo "[".($i+1)."]　";
    //
    // //電波強度の最も強いビーコンとその両隣のビーコンのRSSIを表示
    // print_r($data);
    // echo "<br />";
    //
    // // 中央のビーコンの位置を表示
    // echo "　中央のビーコンの位置は".$midindex."　";
    //
    // //推定結果の表示
    // echo "　　推定結果は";
    // print_r($estimate);
    // echo "m";
    //
    // // 実際の位置を表示
    // echo "　　実際の位置は";
    // print_r($rec2target[$i]);
    // echo "m";
    //
    // //絶対誤差の計算と表示
    // echo "　　絶対誤差は";
    // print_r(abs($estimate - $rec2target[$i]));
    // echo "m";
    // echo "<br />";
    // echo "<br />";

    // print "<table border = '1' align = 'center' width = '500'>";
    // print "<tr align = 'center'>";
    // print "<td>データ番号</td><td>推定位置</td><td>実際の位置</td><td>絶対誤差</td>";
    // print "</tr>";
		print "<tr align = 'center'>";
    print "<td>".($i+1)."</td>";
		print "<td>".$estimate."m</td>";
		print "<td>".$rec2target[$i]."m</td>";
		print "<td>".abs($estimate - $rec2target[$i])."m</td>";
		print "</tr>";

    //平均推定誤差の計算用に絶対誤差の合計値を計算 absは絶対値の表示
    $sum += abs($estimate - $rec2target[$i]);
  }

  //平均推定誤差の計算と表示
  $mae = $sum / count($rec2sample);

  echo "<center>平均絶対誤差は";
  print round($mae,2);
  echo "m</center>";
  echo "<br />";
}
?>
