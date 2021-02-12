<html>
<head>
	<title>テスト用データアップロード</title><meta charset="UTF-8">
</head>
<body>
	<?php
	//登録ボタンが押されたときに選択されたcsvファイルのデータをデータベースに登録
	if (isset($_POST['register'])){
		registerData();
		//全データ削除ボタンが押されたときにデータベースに登録されているデータを削除
	}elseif (isset($_POST['trun'])){
		truncateData();
	}
	fileuploader();
	setTruncateForm();
	back();
	estimation();
	select();
	?>
</body>
</html>

<?php
function fileuploader() //ファイルをアップロード
{
	print <<<FORM
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>PHP_uploader</title>
	</head>
	<body>
	<center>
	<!--ファイルをアップロードするフォーム-->
	<form action="" method="post" enctype="multipart/form-data">
	<br />
	<br />
	アップロードするログデータを選択してください。<br /><br />
	<input type="file" name="csv" size="30" /><br />
	<br />
	<input type="submit" name="register" value="アップロード" />
	</form>
	<br />
	</center>
	</body>
	</html>
	FORM;
}

function setTruncateForm(){	//全データ削除ボタンの表示
	print <<< FORM
	<form action = "" method = "post">
	<table border = "0" align = "center">
	<tr>
	<td align ="center">
	<input type = "submit" name = "trun" value = "全データ削除"></td>
	</tr>
	</table>
	<br>
	</form>
	FORM;
}

function back(){ //ホーム画面に戻るボタンの表示
	print "</table>";
	print "<form action='index.html' method='post' align = 'center'>";
	print "<input type='submit' value='戻る' />";
	print "</form>";
}

function estimation(){ //位置推定結果のページにジャンプするボタンの表示
	print "</table>";
	print "<br>";
	print "<form action='estimate.php' method='post' align = 'center'>";
	print "<input type='submit' value='位置推定結果を表示' />";
	print "</form>";
}

function registerData(){    //データの登録
	$csv = array();
	//エラーがないことを確認
	if($_FILES['csv']['error'] == 0){
		$name = $_FILES['csv']['name'];
		$exploded_text = explode('.', $_FILES['csv']['name']);
		$ext = strtolower(end($exploded_text));
		$type = $_FILES['csv']['type'];
		$tmpName = $_FILES['csv']['tmp_name'];

		// ファイルがcsvファイルであることを確認
		if($ext === 'csv'){
			if(($handle = fopen($tmpName, 'r')) !== FALSE) {
				//ファイルサイズが大きい場合、実行時間を無制限にする
				set_time_limit(0);
				$row = 0;
				while(($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
					// 取得したデータの数をカウント
					$col_count = count($data);
					//取得したデータを配列に格納
					$csv[$row]['col1'] = $data[0];
					$csv[$row]['col2'] = $data[1];
					$csv[$row]['col3'] = $data[2];
					$csv[$row]['col4'] = $data[3];
					$csv[$row]['col5'] = $data[4];
					$csv[$row]['col6'] = $data[5];
					$csv[$row]['col7'] = $data[6];
					$csv[$row]['col8'] = $data[7];
					$csv[$row]['col9'] = $data[8];
					$csv[$row]['col10'] = $data[9];
					$csv[$row]['col11'] = $data[10];

					$row++;
				}
				fclose($handle);
			}
		}
	}

	//データベースに接続
	$dsn='mysql:dbname=ile;host=localhost';
	$user='root'; //仮想サーバならroot
	$password=''; //仮想サーバなら空白
	$dbh=new PDO($dsn, $user, $password);
	$dbh->query('SET NAMES utf-8');
	//値受け取り
	for($row=0; $row < count($csv); $row++){
		$rssi1=$csv[$row]['col1'];
		$rssi2=$csv[$row]['col2'];
		$rssi3=$csv[$row]['col3'];
		$rssi4=$csv[$row]['col4'];
		$rssi5=$csv[$row]['col5'];
		$rssi6=$csv[$row]['col6'];
		$rssi7=$csv[$row]['col7'];
		$rssi8=$csv[$row]['col8'];
		$rssi9=$csv[$row]['col9'];
		$rssi10=$csv[$row]['col10'];
		$position=$csv[$row]['col11'];
		//クエリの作成
		$sql="INSERT INTO test(rssi1, rssi2, rssi3, rssi4, rssi5, rssi6, rssi7, rssi8, rssi9, rssi10,position) values($rssi1,  $rssi2, $rssi3, $rssi4, $rssi5, $rssi6, $rssi7, $rssi8, $rssi9, $rssi10, $position) ";
		//クエリの実行
		$stmt=$dbh->prepare($sql);
		$stmt->execute();
	}
	//コネクションの切断
	$dbh=null;
}

function truncateData(){
	//データベースに接続
	$dsn='mysql:dbname=ile;host=localhost';
	$user='root'; //仮想サーバならroot
	$password=''; //仮想サーバなら空白
	$dbh=new PDO($dsn, $user, $password);
	$dbh->query('SET NAMES utf-8');
	//クエリの作成
	$sql="TRUNCATE test";
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
		$rec=$stmt->fetch(PDO::FETCH_NUM);

		//最終行に行ったら終わり
		if($rec==false){break;}
		print "<tr align = 'center'>";
		print "<td>".$rec[0]."</td>";
		print "<td>".$rec[1]."</td>";
		print "<td>".$rec[2]."</td>";
		print "<td>".$rec[3]."</td>";
		print "<td>".$rec[4]."</td>";
		print "<td>".$rec[5]."</td>";
		print "<td>".$rec[6]."</td>";
		print "<td>".$rec[8]."</td>";
		print "<td>".$rec[7]."</td>";
		print "<td>".$rec[9]."</td>";
		print "<td>".$rec[10]."</td>";

		print "</tr>";
	}
	print "</table>";
	print "<br>";
	//③コネクションの切断
	$dbh=null;
}
?>
