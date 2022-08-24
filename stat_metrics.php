<?php
# グラフごとの指標
$stepchart_list = array(
  'offset',
  'myself',
  'sum',
  'amae',
  'ave',
  'amae_ave',
  'sum_monopoly',
  'count',
  'hatsudou',
  'hatsudou_low',
  'hatsudou_mid',
  'hatsudou_high',
  'expected',
  'count_monopoly',
  'defeat',
  'defeat_ave',
  'isiki',
  'fever',
  'atsuo',
);

$columnchart_list = array(
  'offset',
  'myself',
  'sum',
  'amae',
  'ave',
  'amae_ave',
  'count',
  'hatsudou',
  'hatsudou_low',
  'hatsudou_mid',
  'hatsudou_high',
  'expected',
  'defeat',
  'defeat_ave',
  'isiki',
  'fever',
);

$donutchart_list = array(
  'sum_monopoly',
  'count_monopoly'
);

# 小数点以下を出力する指標
$float_list = array(
  'amae_ave',
  'sum_monopoly',
  'hatsudou',
  'hatsudou_low',
  'hatsudou_mid',
  'hatsudou_high',
  'expected',
  'count_monopoly',
  'defeat_ave',
  'isiki',
  'fever',
);

# パーセント記号を出力する指標
$percent_list = array(
  'sum_monopoly',
  'hatsudou',
  'hatsudou_low',
  'hatsudou_mid',
  'hatsudou_high',
  'count_monopoly',
  'isiki',
);

# 値変動を出力する指標
$diffdisplay_list = array(
  'offset',
  'myself',
  'sum',
  'amae',
  'ave',
  'amae_ave',
  'expected',
  'count',
  'defeat',
  'defeat_ave',
  'fever',
);

# 表示する指標取得
$metrics = $_GET['metrics'];
$title = array_search($metrics, $metrics_all);
$charts[] = $metrics;

# タイトルとグラフエリア表示
echo "<h3>$title</h3>";
if ($metrics == 'atsuo' || $metrics == 'fever'){
  echo "<a href='./atsuo.pdf' target='_blank'>定義</a>";
}
if($metrics == 'offset'){
  echo "<div class='chart offset' id='stepchart_$metrics'></div>";
}else{
  echo "<div class='chart' id='stepchart_$metrics'></div>";
}
echo "<div class='chart' id='columnchart_$metrics'></div>";
echo "<div class='chart' id='donutchart_$metrics'></div>";
if ($metrics == 'atsuo'){
  $add_metrics = array('fever','count','sum','defeat');
  $charts = array_merge($charts, $add_metrics);
  $columnchart_list = array_diff($columnchart_list, $add_metrics);
  foreach ($add_metrics as $m) {
    $t = array_search($m, $metrics_all);
    echo "<h5>$t</h5>";
    echo "<div class='chart' id='stepchart_$m'></div>";
  }
}

# ランキングを取得し、降順ソート
$rank = array_column($stats, $metrics, "name");
arsort($rank);

# 順位変動を計算
$before2index = max(0, count($cumulus)-2);
$before2_rank = array_column($cumulus[$before2index], $metrics, "name");
arsort($before2_rank);
$current_rank = array_keys($rank);
$previous_rank = array_keys($before2_rank);
$updown_list = array();
$diff_list = array();
foreach ($person_list as $p) {
  $updown_list[$p] = array_search($p, $previous_rank) - array_search($p, $current_rank);
  $diff_list[$p] = $rank[$p] - $before2_rank[$p];
}

# ランキングテーブル表示
echo "<table>";
# ラベル (必要な場合のみ)
if (strpos($metrics,'hatsudou') !== false){
  echo "<tr><td></td><td></td><td>発動率</td><td>漢気</td><td>参加</td><td></td></tr>";
}elseif ($metrics == 'isiki') {
  echo "<tr><td></td><td></td><td>意識</td><td>参加</td><td>開催</td><td></td></tr>";
}
foreach ($rank as $person => $value) {
  # 名前
  $person_str = $person;
  # 値
  if($value == -1){
    $value_str = 'NAN';
  }else{
    in_array($metrics, $float_list) ? $value_str = number_format($value,2) :  $value_str = number_format($value);
    in_array($metrics, $percent_list) ? $value_str = $value_str . '%':$value_str;
  }
  # 分母分子 (必要な場合のみ)
  if ($metrics == 'hatsudou'){
    $nume = $stats[$person]["count"];
    $domi = $stats[$person]["attend"];
  }elseif ($metrics == 'hatsudou_low'){
      $nume = $stats[$person]["count_low"];
      $domi = $stats[$person]["attend_low"];
  }elseif ($metrics == 'hatsudou_mid'){
      $nume = $stats[$person]["count_mid"];
      $domi = $stats[$person]["attend_mid"];
  }elseif ($metrics == 'hatsudou_high'){
      $nume = $stats[$person]["count_high"];
      $domi = $stats[$person]["attend_high"];
  }elseif ($metrics == 'isiki') {
    $nume = $stats[$person]["attend"];
    $domi = $stats["total_count"];
  }
  # 順位変動
  $updown = $updown_list[$person];
  if($updown == 0){
    $updown_str = "";
  }else if($updown > 0){
    $updown_str = "▲".number_format(abs($updown));
  }else{
    $updown_str = "▼".number_format(abs($updown));
  }

  # 値変動
  if (in_array($metrics, $diffdisplay_list)){
    $diff = $diff_list[$person];
    if (in_array($metrics, $float_list)){
      $diff == 0 ? $diff_str = "" : $diff_str = sprintf('%+.2f', $diff);
    }else{
      $diff == 0 ? $diff_str = "" : $diff_str = sprintf('%+d', $diff);
    }
  }

  # 有効参加数以上なら太字
  if($stats[$person]["attend"] >= $stats["regulation"]){
    $person_str = "<b>$person_str</b>";
    $value_str = "<b>$value_str</b>";
  }

  # 出力
  if (strpos($metrics,'hatsudou') !== false || $metrics == 'isiki'){
    echo "<tr><td>$updown_str</td><td>$person_str</td><td>$value_str</td><td>$nume</td><td>$domi</td></tr>";
  }else if ($metrics != 'atsuo'){
    echo "<tr><td>$updown_str</td><td>$person_str</td><td>$value_str</td><td>$diff_str</td></tr>";
  }
}
if ($metrics == 'atsuo'){
  krsort($atsuo_history);
  echo "<tr><td></td><td>あつお</td><td>期間</td></tr>";
  foreach ($atsuo_history as $num => $info) {
    $name_str = $info['name'];
    $length_str = $info['length'];
    if($num == count($atsuo_history) && $name_str == $atsuo){
      $num_str = "今上";
    }else{
      $num_str = "第".$num."代";
    }
    echo "<tr><td>$num_str</td><td>$name_str</td><td>$length_str</td></tr>";
  }
}
echo "</table>";

include('./stat_chart.php');
?>
