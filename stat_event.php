<?php
# 表示する指標取得
$metrics = $_GET['metrics'];
$title = $metrics_all[$metrics];

# タイトル表示
echo "<h3>$title</h3>";

# テーブル表示
if ($metrics == 'event_all'){
  $rank = array_column($cumulus, "datetime");
  arsort($rank);

  echo "<table>";
  $counter = count($rank)-1;
  foreach ($rank as $key => $value) {
    $event = $cumulus[$key];
    $date = $event["date"];
    $person = $event["person"];
    $amount = $event["amount"];
    if ($key > 0){
      echo "<tr><td>$counter</td><td>$date</td><td>$person</td><td>$amount</td><td>$new</td></tr>";
    }
    $counter -= 1;
  }
  echo "</table>";

}else if ($metrics == 'event_rank'){
  $rank = array_column($cumulus, "amount");
  arsort($rank);

  echo "<table>";
  $counter = 1;
  foreach ($rank as $key => $value) {
    $event = $cumulus[$key];
    $date = $event["date"];
    $person = $event["person"];
    $amount = $event["amount"];
    if ($key == count($cumulus) - 1){
      $new = "NEW";
    }else{
      $new = "";
    }
    if ($key != 0){
      echo "<tr><td>$counter</td><td>$date</td><td>$person</td><td>$amount</td><td>$new</td></tr>";
    }
    $counter += 1;
  }
  echo "</table>";
}

?>
