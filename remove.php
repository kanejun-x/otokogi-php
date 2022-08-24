<?php
require_once('./lib.php');
$season = get_current_season();

include('./header.html');
if (!isset($season)){
  echo "シーズン情報が設定されていません。";
  exit(0);
}

echo "<p>1項目ずつ消せます。</p>";
echo "<form action='./posted.php' method='post' onsubmit='return submitConfirm()'>";
echo "<table class='remove'>";
$record = read_record($season);
foreach ($record as $i => $otokogi) {
  $date = $otokogi[1];
  $person = $otokogi[2];
  $amount = $otokogi[3];
  $attendees = $otokogi[4];
  $guest_num = $otokogi[5];
  echo "<tr><td><input id='radio-$i' type='radio' name='remove' value='$i' onclick='onClickRadio()' required></td><td>$date</td><td>$person</td><td>$amount</td><td>$attendees</td><td>$guest_num</td></tr>";
}
echo "</table>";
echo "<button id='rmv_btn' type='submit' name='action' value='remove' disabled='true'>削除</button>";
echo "</form>";

?>
<script>
function onClickRadio(){
  var elements = document.getElementsByName('remove');
  var count = 0;
  for(var i = 0 ; i < elements.length ; i ++){
    if(elements[i].checked == true){
      count++;
    }
  }
  if (count > 0){
    document.getElementById("rmv_btn").disabled = false;
  }else{
    document.getElementById("rmv_btn").disabled = true;
  }
}

function submitConfirm () {
  var flag = confirm ( "選択された登録情報を削除します。");
  return flag;
}
</script>
