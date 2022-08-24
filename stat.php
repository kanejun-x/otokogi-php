<?php
require_once('./lib.php');
if(isset($_GET['season'])){
  $season = $_GET['season'];
}else{
  $season = get_current_season();
}
$season_begining_date = get_season_info($season)['begining_date'];

include('./header.html');
if (!isset($season)){
  echo "シーズン情報が設定されていません。";
  exit(0);
}

# ==========
# 統計処理
# ==========

# 情報取得
$person_list = get_personlist($season);
$record = read_record($season);

# 定数
$low_thresh = 1500;
$high_thresh = 3000;

# 統計格納用変数の初期化
$stats = array(
  "datetime" => $season_begining_date . "T00:00:00+0900",
  "date" => $season_begining_date,
  "person" => '',
  "amount" => 0,
  "total_count" => 0,
  "total_amount" => 0,
  "total_attendee" => 0,
  "regulation" => 0,
);
foreach ($person_list as $i => $person) {
  $stats[$person] = array(
    "name" => $person,
    "latest" => 0,
    "attend" => 0,
    "attend_low" => 0,
    "attend_mid" => 0,
    "attend_high" => 0,
    "fever" => 0,
    "atsuo" => 0,
    "sum" => 0,
    "myself" => 0,
    "amae" => 0,
    "offset" => 0,
    "count" => 0,
    "count_low" => 0,
    "count_mid" => 0,
    "count_high" => 0,
    "defeat" => 0,
    "ave" => 0,
    "amae_ave" => 0,
    "defeat_ave" => 0,
    "isiki" => 0,
    "hatsudou" => 0,
    "hatsudou_low" => 0,
    "hatsudou_mid" => 0,
    "hatsudou_high" => 0,
    "expected" => 0,
    "sum_monopoly" => 0,
    "count_monopoly" => 0,
  );
}

# 積立統計格納用変数の初期化
$cumulus = array();
$cumulus[] = $stats;

# 歴代あつおリスト
$atsuo_history = array();

# 解析
foreach ($record as $i => $otokogi) {
  # 変数コピー
  $datetime = $otokogi[0];
  $date = $otokogi[1];
  $person = $otokogi[2];
  $amount = $otokogi[3];
  $attendees = explode(",", $otokogi[4]);
  $guest_num = $otokogi[5];
  $defeat = count($attendees) + $guest_num - 1;

  # 統計格納
  $stats["datetime"] = $datetime;
  $stats["date"] = $date;
  $stats["person"] = $person;
  $stats["amount"] = $amount;
  $stats[$person]["latest"] = count($cumulus);

  # あつおポイント更新
  if ($stats["total_count"] == 0){
    $amount_bonus = 0;
    $defeat_bonus = 0;
  }else{
    $amount_ave = $stats["total_amount"]/$stats["total_count"];
    $amount_bonus = ($amount - $amount_ave)*20/$amount_ave;
    $defeat_ave = array_sum(array_column($stats, "defeat"))/$stats["total_count"];
    $defeat_bonus = ($defeat - $defeat_ave)*20/$defeat_ave;

  }
  foreach ($person_list as $p) {
    $base = $stats[$p]["fever"];
    if ($p == $person){
      $base += 100 + $amount_bonus + $defeat_bonus;
    }
    $p_latest = $stats[$p]["latest"];
    $x = count($cumulus) - $p_latest;
    $y = $base / (1 + 0.01 * exp($x));
    $stats[$p]["fever"] = $y;
  }
  $rank = array_column($stats, "fever", "name");
  arsort($rank);
  $no1fever = array_keys($rank)[0];
  $no2fever  = array_keys($rank)[1];
  foreach ($person_list as $p) {
    $stats[$p]["atsuo"] = 0;
  }
  # あつお更新
  if($rank[$no1fever] >= $rank[$no2fever]*1.8){
    if (isset($atsuo) && $atsuo != $no1fever){
      $atsuo_history[count($atsuo_history)+1] = array('name'=>$no1fever, 'length'=>1);
    }else{
      $atsuo_history[count($atsuo_history)]['length'] += 1;
    }
    $stats[$no1fever]["atsuo"] = 1;
    $atsuo = $no1fever;
  }else{
    $atsuo = '';
  }

  # 全体指標更新
  $stats["total_count"] += 1;
  $stats["total_amount"] += $amount;
  $stats["total_attendee"] += count($attendees) + $guest_num;
  $stats["regulation"] = floor($stats["total_count"]*0.2);

  # 発揮者の指数更新
  $stats[$person]["count"] += 1;
  if ($amount < $low_thresh){$stats[$person]["count_low"] += 1;}
  elseif ($amount < $high_thresh) {$stats[$person]["count_mid"] += 1;}
  else {$stats[$person]["count_high"] += 1;}
  $stats[$person]["sum"] += $amount;
  $stats[$person]["defeat"] += $defeat;
  $stats[$person]["myself"] += round($amount/(count($attendees) + $guest_num));

  # 参加者の参加数更新
  foreach ($attendees as $attendee) {
    $stats[$attendee]["attend"] += 1;
    if ($amount < $low_thresh){$stats[$attendee]["attend_low"] += 1;}
    elseif ($amount < $high_thresh) {$stats[$attendee]["attend_mid"] += 1;}
    else {$stats[$attendee]["attend_high"] += 1;}
    if($attendee != $person){
      $stats[$attendee]["amae"] += round($amount/(count($attendees) + $guest_num));
    }
  }

  # 全員の指標更新
  foreach ($person_list as $person) {
    # 実質
    $stats[$person]["offset"] = $stats[$person]["sum"] - $stats[$person]["amae"] - $stats[$person]["myself"];

    if ($stats["total_count"] == 0){
      # 回数独占率
      $stats[$person]["count_monopoly"] = -1;
      # 意識
      $stats[$person]["isiki"] = -1;
    }else{
      # 回数独占率
      $stats[$person]["count_monopoly"] = $stats[$person]["count"]*100/$stats["total_count"];
      # 意識
      $stats[$person]["isiki"] = $stats[$person]["attend"]*100/$stats["total_count"];
    }
    if ($stats["total_amount"] == 0){
      # 金額独占率
      $stats[$person]["sum_monopoly"] = -1;
    }else{
      # 金額独占率
      $stats[$person]["sum_monopoly"] = $stats[$person]["sum"]*100/$stats["total_amount"];
    }
    if ($stats[$person]["count"] == 0){
      # 平均額
      $stats[$person]["ave"] = -1;
      # 平均撃破人数
      $stats[$person]["defeat_ave"] = -1;
    }else{
      # 平均額
      $stats[$person]["ave"] = $stats[$person]["sum"]/$stats[$person]["count"];
      # 平均撃破人数
      $stats[$person]["defeat_ave"] = $stats[$person]["defeat"]/$stats[$person]["count"];
    }
    $stage_list = array('', '_low', '_mid', '_high');
    foreach ($stage_list as $stage) {
      if ($stats[$person]["attend$stage"] == 0){
        # 発動率
        $stats[$person]["hatsudou$stage"] = -1;
      }else{
        # 発動率
        $stats[$person]["hatsudou$stage"] = $stats[$person]["count$stage"]*100/$stats[$person]["attend$stage"];
      }
    }

    # 漢気期待値
    $stats[$person]["expected"] = ($stats[$person]["hatsudou"]/100) * $stats[$person]["ave"];

    if ($stats[$person]["attend"]-$stats[$person]["count"] == 0){
      # 平均甘え額
      $stats[$person]["amae_ave"] = -1;
    }else{
      # 平均甘え額
      $stats[$person]["amae_ave"] = $stats[$person]["amae"]/($stats[$person]["attend"]-$stats[$person]["count"]);
    }
  }
  # 積立変数に格納
  $cumulus[] = $stats;
}

# ==========
# 指標をジャンルごとにまとめる
# ==========
$metrics_amount = array(
  '実質漢気額' => 'offset',
  '合計金額' => 'sum',
  '合計甘え額' => 'amae',
  '自分へのお買い物額' => 'myself',
  '漢気期待値' => 'expected',
  '平均金額' => 'ave',
  '平均甘え額' => 'amae_ave',
  '合計金額独占率' => 'sum_monopoly',
);
$metrics_count = array(
  '合計回数' => 'count',
  '発動率 (全体)' => 'hatsudou',
  "発動率 ($low_thresh 円未満)" => 'hatsudou_low',
  "発動率 ($low_thresh 円以上 $high_thresh 円未満)" => 'hatsudou_mid',
  "発動率 ($high_thresh 円以上)" => 'hatsudou_high',
  '合計回数独占率' => 'count_monopoly',
);
$metrics_defeat = array(
  '合計撃破人数' => 'defeat',
  '平均撃破人数' => 'defeat_ave',
  #'合計撃破人数' => 'defeat_monopoly',
);
$metrics_attend = array(
  '意識' => 'isiki',
);
$metrics_fever = array(
  '歴代あつお' => 'atsuo',
  'あつおポイント' => 'fever',
);
$metrics_event = array(
  '一覧' => 'event_all',
  'ランキング' => 'event_rank',
);
$metrics_all = array_merge($metrics_amount, $metrics_count, $metrics_defeat, $metrics_attend, $metrics_fever, $metrics_event);

# ==========
# 表示処理
# ==========
if (!isset($_GET['metrics'])){
  include('stat_outline.php');
}else if(in_array($_GET['metrics'], $metrics_event)){
  include('stat_event.php');
}else{
  include('stat_metrics.php');
}
include('./footer.html');
?>
