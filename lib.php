<?php
# 現在のシーズン取得
function get_current_season(){
  return '2019-1';
}

# 現在のシーズン情報取得
function get_season_info($season){
  # season.csvにseasonが存在しない場合は終了
  if ($season == '2018-2'){
    $season_info = array('begining_date' => '2018-07-01');
  }else{
    $season_info = array('begining_date' => '2019-01-01');
  }

  return $season_info;
}

# メンバーリスト取得
function get_personlist($season){
  try{
    $path = "./data/$season/member.csv";
    $file =  new SplFileObject($path, 'r');
    $file->setFlags(SplFileObject::READ_CSV);
  }catch (Exception $e) {
    echo $e;
  }
  $person_list = array();
  foreach ($file as $i => $line) {
    if (count($line) == 1){
      continue;
    }
    $person_list[] = $line[0];
  }
  return $person_list;
}
# 新規メンバーを書き込む
function add_person($season, $name){
  $list = _get_personlist_with_attendance_($season);
  $list[$name] = 'not_yet_attended';
  _write_personlist_with_attendance_($season, $list);
}
#既存メンバーを削除する
function remove_person($season, $name){
 $list = _get_personlist_with_attendance_($season);
 unset($list[$name]);
 _write_personlist_with_attendance_($season, $list);
}
#参加情報を返す
function has_attended($season, $name){
  $list = _get_personlist_with_attendance_($season);
  if ($list[$name] == 'attended'){
    return true;
  }else{
    return false;
  }
}
#メンバーを参加済にする
function be_attended($season, $namelist){
  $list = _get_personlist_with_attendance_($season);
  foreach ($namelist as $name) {
    if ($list[$name] != 'attended'){
      $list[$name] = 'attended';
    }
  }
  _write_personlist_with_attendance_($season, $list);
}
#メンバーを未参加にする
function be_not_yet_attended($season, $namelist){
  $list = _get_personlist_with_attendance_($season);
  foreach ($namelist as $name) {
    if ($list[$name] == 'attended'){
      $list[$name] = 'not_yet_attended';
    }
  }
  _write_personlist_with_attendance_($season, $list);
}
#参加情報を付け直す
function reset_attended($season){
  $person_list = get_personlist($season);
  be_not_yet_attended($season, $person_list);
  $record =  read_record($season);
  foreach ($record as $otokogi) {
    $attendees = explode(",",$otokogi[4]);
    be_attended($season, $attendees);
  }
}

# 参加情報つきメンバーリスト取得
function _get_personlist_with_attendance_($season){
  try{
    $path = "./data/$season/member.csv";
    $file =  new SplFileObject($path, 'r');
    $file->setFlags(SplFileObject::READ_CSV);
  }catch (Exception $e) {
    echo $e;
  }
  $person_list = array();
  foreach ($file as $i => $line) {
    if (count($line) == 1){
      continue;
    }
    $person_list[$line[0]] = $line[1];

  }
  return $person_list;
}

# 参加情報つきメンバーリストを書き込む
function _write_personlist_with_attendance_($season, $list){
  try{
    $path = "./data/$season/member.csv";
    $file =  new SplFileObject($path, 'w');
  }catch (Exception $e) {
    echo $e;
  }
  foreach ($list as $person => $attended) {
    $file->fputcsv(array($person, $attended));
  }
}

# rawdata.csv読み込み
function read_record($season){
  $record = array();
  try{
    $path = "./data/$season/rawdata.csv";
    $file =  new SplFileObject($path, 'r');
    $file->setFlags(SplFileObject::READ_CSV);
  }catch (Exception $e) {
    echo $e;
  }
  foreach ($file as $i => $otokogi) {
    if (count($otokogi) == 1){
      continue;
    }
    $record[] = $otokogi;
  }
  return $record;
}

# rawdata.csv追記
function add_record($season, $csvline){
  try{
    $path = "./data/$season/rawdata.csv";
    $file =  new SplFileObject($path, 'a');
  }catch (Exception $e) {
    echo $e;
  }
  $file->fputcsv($csvline);
}

# rawdata.csvゼロ書き込み
function write_record($season, $csvlines){
  try{
    $path = "./data/$season/rawdata.csv";
    $file =  new SplFileObject($path, 'w');
  }catch (Exception $e) {
    echo $e;
  }
  foreach ($csvlines as $line) {
    $file->fputcsv($line);
  }
  reset_attended($season);
}

# omoide.csv読み込み
function read_omoide($season){
  $record = array();
  try{
    $path = "./data/$season/omoide.csv";
    $file =  new SplFileObject($path, 'r');
    $file->setFlags(SplFileObject::READ_CSV);
  }catch (Exception $e) {
    echo $e;
  }
  foreach ($file as $i => $omoide) {
    if (count($omoide) == 1){
      continue;
    }
    $record[] = $omoide;
  }
  return $record;
}

# omoide.csv追記
function add_omoide($season, $csvline){
  try{
    $path = "./data/$season/omoide.csv";
    $file =  new SplFileObject($path, 'a');
  }catch (Exception $e) {
    echo $e;
  }
  $file->fputcsv($csvline);
}

function create_massage($time,$who,$howmuch,$attendee,$guest_num){
  $form_url = 'http://www.anarg.jp/personal/j-kaneda/otokogi/';
  $stat_url = 'http://www.anarg.jp/personal/j-kaneda/otokogi/stat.php';

  $money = number_format($howmuch);
  $member = implode(",",$attendee);
  $format = "%sさんが漢気を刻みました。\n時刻: %s\n金額: %s\n参加者: %s\nゲスト数: %s\n\n統計: %s\n\n新規回答フォーム: %s";
  $message = sprintf($format, $who, $time, $money, $member, $guest_num, $stat_url, $form_url);
  return $message;
}

function notify($message){
  $url = 'https://notify-api.line.me/api/notify';
  $token = 'MtA1nvOZyqzlwceybGNwP3EhdCbIAhN69sxpJbC2h8W';
  $message = "\n".$message;

  $data = array(
                      "message" => $message
                   );
  $data = http_build_query($data, "", "&");

  $options = array(
      'http'=>array(
          'method'=>'POST',
          'header'=>"Authorization: Bearer " . $token . "\r\n"
                    . "Content-Type: application/x-www-form-urlencoded\r\n"
                    . "Content-Length: ".strlen($data)  . "\r\n" ,
          'content' => $data
      )
  );
  $context = stream_context_create($options);
  $response = file_get_contents($url,FALSE,$context);
  return json_decode($response,TRUE);
}
?>
