<?php
echo "<h3>シーズン累計</h3>";
echo "<div class='chart' id='stepchart_season'></div>";
echo "<table>";
# 総漢気
$str = number_format($stats["total_amount"]);
echo "<tr><td>総漢気</td><td>$str</td></tr>";
# 開催数
$str = number_format($stats["total_count"]);
echo "<tr><td>開催数</td><td>$str</td></tr>";
# 平均額
if ($stats["total_count"] !=0 ){
  $str = number_format($stats["total_amount"]/$stats["total_count"]);
  echo "<tr><td>平均額</td><td>$str</td></tr>";
}
# 平均参加人数
if ($stats["total_count"] !=0 ){
  $str = number_format($stats["total_attendee"]/$stats["total_count"],2);
  echo "<tr><td>平均参加人数</td><td>$str</td></tr>";
}
# 有効参加数
$str = number_format($stats["regulation"]);
echo "<tr><td>有効参加数</td><td>$str</td></tr>";

# あつお
echo "<tr><td>あつお</td><td>$atsuo</td></tr>";

echo "</table>";

?>

<h3>金額系指標</h3>
<ul>
  <?php
  foreach ($metrics_amount as $title => $metrics) {
    echo "<li><a href='./stat.php?season=$season&metrics=$metrics'>$title</a></li>";
  }
  ?>
</ul>

<h3>回数系指標</h3>
<ul>
  <?php
  foreach ($metrics_count as $title => $metrics) {
    echo "<li><a href='./stat.php?season=$season&metrics=$metrics'>$title</a></li>";
  }
  ?>
</ul>

<h3>撃破系指標</h3>
<ul>
  <?php
  foreach ($metrics_defeat as $title => $metrics) {
    echo "<li><a href='./stat.php?season=$season&metrics=$metrics'>$title</a></li>";
  }
  ?>
</ul>

<h3>参加系指標</h3>
<ul>
  <?php
  foreach ($metrics_attend as $title => $metrics) {
    echo "<li><a href='./stat.php?season=$season&metrics=$metrics'>$title</a></li>";
  }
  ?>
</ul>

<h3>熱度系指標</h3>
<ul>
  <?php
  foreach ($metrics_fever as $title => $metrics) {
    echo "<li><a href='./stat.php?season=$season&metrics=$metrics'>$title</a></li>";
  }
  ?>
</ul>

<h3>イベント</h3>
<ul>
  <?php
  foreach ($metrics_event as $title => $metrics) {
    echo "<li><a href='./stat.php?season=$season&metrics=$metrics'>$title</a></li>";
  }
  ?>
</ul>

<h3>思い出</h3>
<ul>
<?php
$omoides = read_omoide($season);
$omoides = array_reverse($omoides);
foreach ($omoides as $omoide) {
  $name = $omoide[1];
  $url = $omoide[2];
  echo"<li><a href='$url' target='_blank'>$name</a></li>";
}
?>
</ul>

<h3>過去の統計</h3>
<ul>
  <li><a href='./stat.php?season=2018-2'>2018年後期</a></li>
</ul>
<?php
$charts[] = 'season';
include('./stat_chart.php');
?>
