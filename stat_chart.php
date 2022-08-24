<script type="text/javascript">

google.charts.load('current', {packages: ['corechart']});
google.charts.setOnLoadCallback(drawCurveTypes);

function drawCurveTypes() {

  var options = {
    fontSize: 6,
    chartArea:{
      left: 40,
      width: '70%',
      height:'80%',
    },
    areaOpacity: '0.0',
    hAxis:{
      textPosition:'out',
      format: 'yyyy/MM/dd',
    },
    vAxis:{
      viewWindow: {
        min:0
      },
    },
    series: {
      0: {targetAxisIndex:0},
      1: {targetAxisIndex:1},
    },
    legend:{
      position: 'top',
    },
  };
  var data = new google.visualization.DataTable();
  data.addColumn('date', 'X');
  data.addColumn('number', '総漢気');
  data.addColumn('number', '開催数');
  <?php
  foreach ($cumulus as $stats) {
    $date = $stats['date'];
    $total_amount = $stats['total_amount'];
    $total_count = $stats['total_count'];
    $data_str = "new Date('$date'), $total_amount, $total_count";
    echo "data.addRows([[$data_str],]);";
  }
  if(in_array('season',$charts)){
    echo "
    var chart = new google.visualization.SteppedAreaChart(document.getElementById('stepchart_season'));
    chart.draw(data, options);";
  }
  ?>

  /* ---  --- ---*/

  var options = {
    fontSize: 6,
    chartArea:{
      left: 40,
      top:5,
      width: '70%',
      height:'80%',
    },
    areaOpacity: '0.0',
    hAxis:{
      textPosition:'out',
    },
    vAxis:{
      viewWindow: {
        min:0
      },
    },
    legend:{
      position: 'right',
    },
  };

  var options_atsuo = {
    fontSize: 6,
    chartArea:{
      left: 40,
      top:5,
      width: '70%',
      height:'80%',
    },
    areaOpacity: '1.0',
    hAxis:{
      textPosition:'out',
    },
    vAxis:{
      textPosition: 'none',
      viewWindow: {
        min:0
      },
    },
    legend:{
      position: 'right',
    },
  };

  var options_minus = {
    fontSize: 6,
    chartArea:{
      left: 40,
      top:5,
      width: '70%',
      height:'80%',
    },
    areaOpacity: '0.0',
    hAxis:{
      textPosition:'out',
    },
    legend:{
      position: 'right',
    },
  };
  <?php
  foreach ($charts as $metrics) {
    if(isset($stepchart_list) && in_array($metrics, $stepchart_list)){
      echo "var data = new google.visualization.DataTable();";
      echo "data.addColumn('string', 'X');";
      foreach ($person_list as $person) {
        echo "data.addColumn('number', '$person');";
      }
      foreach ($cumulus as $stats) {
        $date = $stats['date'];
        $data_list = array_column($stats, $metrics);
        $data_str = "'$date'," . implode(",", $data_list);
        echo "data.addRows([[$data_str],]);";
      }
      echo "var chart = new google.visualization.SteppedAreaChart(document.getElementById('stepchart_$metrics'));";
      if($metrics == 'offset'){
        echo "chart.draw(data, options_minus);";
      }else if($metrics == 'atsuo'){
        echo "chart.draw(data, options_atsuo);";
      }else{
        echo "chart.draw(data, options);";
      }
    }
  }
  ?>


  /* ---  --- ---*/


  var options_minus = {
    fontSize: 6,
    chartArea:{
      left: 40,
      top:5,
      width: '70%',
      height:'80%',
    },
    areaOpacity: '0.0',
    hAxis:{
      textPosition:'out',
    },
    legend:{
      position: 'none',
    },
  };
  var options = {
    fontSize: 6,
    chartArea:{
      left: 40,
      top:5,
      width: '70%',
      height:'80%',
    },
    areaOpacity: '0.0',
    hAxis:{
      textPosition:'out',
    },
    vAxis:{
      viewWindow: {
        min:0
      },
    },
    legend:{
      position: 'none',
    },
  };
  <?php
  foreach ($charts as $metrics) {
    if(isset($columnchart_list) && in_array($metrics, $columnchart_list)){
      $data_str = "['名前', '値'],";
      $rank = array_column($stats, $metrics, "name");
      arsort($rank);
      foreach ($rank as $person => $value) {
        $data_str .= "['$person', $value], ";
      }
      echo "var data = new google.visualization.arrayToDataTable([$data_str]);";
      echo "var view = new google.visualization.DataView(data);";
      echo "view.setColumns([0, 1]);";
      echo "var chart = new google.visualization.ColumnChart(document.getElementById('columnchart_$metrics'));";
      if($metrics == 'offset'){
        echo "chart.draw(data, options_minus);";
      }else{
        echo "chart.draw(data, options);";
      }
    }
  }
  ?>

  /* ---  --- ---*/


  var options = {
    pieHole: 0.4,
    fontSize: 6,
    chartArea:{
      left: 40,
      width: '100%',
      height:'100%',
    },
    areaOpacity: '0.0',
    legend:{
      position: 'right',
    },
  };
  <?php
  foreach ($charts as $metrics) {
    if(isset($donutchart_list) && in_array($metrics, $donutchart_list)){
      $rank = array_column($stats, $metrics, "name");
      $data_str = "['名前', '値'],";
      foreach ($rank as $person => $value) {
        $data_str .= "['$person', $value], ";
      }
      echo "var data = new google.visualization.arrayToDataTable([$data_str]);";
      echo "var chart = new google.visualization.PieChart(document.getElementById('donutchart_$metrics'));";
      echo "chart.draw(data, options);";
    }
  }
  ?>
}
</script>
