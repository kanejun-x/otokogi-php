<?php
require_once('./lib.php');

$season = get_current_season();
$season_begining_date = get_season_info($season)['begining_date'];

include('./header.html');
if (!isset($season)){
  echo "シーズン情報が設定されていません。";
  exit(0);
}

# メンバーリスト取得
$person_list = get_personlist($season);

# ==========
# 登録処理
# ==========
if(!isset($_POST['action'])){
  echo "正しく送信されていません。";
  exit(0);
}

if($_POST['action'] == 'otokogi'){
  # フォーム取得
  date_default_timezone_set('Asia/Tokyo');
  $time = date('Y-m-d H:i:s');
  $csvline = array();
  $csvline[] = date('Y-m-d\TH:i:s\+0900');
  $csvline[] = $_POST['when'];
  $csvline[] = $_POST['who'];
  $csvline[] = $_POST['howmuch'];
  $csvline[] = implode(",",$_POST['attendee']);
  $csvline[] = $_POST['guest_num'];
  $csvline[] = $_SERVER['REMOTE_ADDR'];
  $csvline[] = gethostbyaddr($_SERVER['REMOTE_ADDR']);

  add_record($season,$csvline);
  be_attended($season, $_POST['attendee']);
  $message = create_massage($time,$_POST['who'],$_POST['howmuch'],$_POST['attendee'],$_POST['guest_num']);
  echo "<h3>記録完了</h3>";
  echo "<p>漢気を記録しました。</>";

}else if($_POST['action'] == 'omoide'){
  $csvline = array();
  $csvline[] = date('Y-m-d\TH:i:s\+0900');
  $csvline[] = $_POST['name'];
  $csvline[] = $_POST['url'];
  add_omoide($season,$csvline);
  $name = $_POST['name'];
  $url = $_POST['url'];
  $message = "新しい思い出が記録されました。\n思い出: $name\nURL: $url";
  echo "<h3>記録完了</h3>";
  echo "<p>思い出を記録しました。</>";

}else if($_POST['action'] == 'remove'){
  $record = read_record($season);
  $index = (int)$_POST['remove'];
  $date = $record[$index][1];
  $person = $record[$index][2];
  $amount = $record[$index][3];
  $attendees = explode(",", $v[4]);
  $guest_num = $record[$index][5];
  array_splice($record,$index,1);
  write_record($season, $record);
  $message = "以下の漢気を削除しました。\n\n";
  $message .= create_massage($date,$person,$amount,$attendees,$guest_num);
  echo "<h3>削除完了</h3>";
  echo "<p>漢気を削除しました。</>";
}

$response = notify($message);
$status = $response['status'];
$msg = $response['message'];
echo "<h3>LINE Notify</h3>";
echo "<p>Status: $status</p><p>Message: $msg</p>";
echo "<p>送信内容:</p>";
$message = str_replace("\n",'<br/>',$message);
echo "<p>$message</p>";

echo "<h3><a href='./stat.php'>統計をみる</a></h3>";

?>
