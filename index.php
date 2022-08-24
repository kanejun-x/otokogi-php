<?php
include('./header.html');


require_once('./lib.php');
$season = get_current_season();

if (!isset($season)){
  echo "シーズン情報が設定されていません。";
  exit(0);
}

# メンバーリスト取得
$person_list = get_personlist($season);
?>

<form action="./posted.php" method="post">

  <h3>いつ漢気を示しましたか？*</h3>
  <input type="date" name="when" value="<?php echo date('Y-m-d');?>" required>

  <h3>誰が漢気を示しましたか？*</h3>
  <ul class='tile'>
    <?php
    foreach ($person_list as $i => $person) {
      echo "<li><input id='radio-$i' type='radio' name='who' value='$person' onclick='onClickRadio(this)' required><label for='radio-$i'>$person</label></li>";
    }
    ?>
  </ul>

  <h3>何円の漢気を示しましたか？*</h3>
  <input type="number" name="howmuch" min="0" required>

  <h3>参加者は誰でしたか？*</h3>
  <ul class='tile' >
    <?php
    foreach ($person_list as $i => $person) {
      echo "<li><input id='check-$i' type='checkbox' name='attendee[]' value='$person' onclick='onClickCheckbox()'><label for='check-$i'>$person</label></li>";
    }
    ?>
  </ul>
  <h3>ゲストは何人でしたか？*</h3>
  <input type="number" name="guest_num" value="0" min="0" required>

  <button id='send_btn' type='submit' name='action' value='otokogi' disabled='true'>送信</button>
</form>

<p><a href='./omoide.php'>思い出の記録はこちら</a></p>

<script>
function onClickRadio(element){
  var id = element.id;
  var ids = id.split("-");
  var checked = element.checked;
  document.getElementById("check-"+ids[1]).checked = checked;
  onClickCheckbox();
}

function onClickCheckbox(){
  var elements = document.getElementsByName('attendee[]');
  var count = 0;
  for(var i = 0 ; i < elements.length ; i ++){
    if(elements[i].checked == true){
      count++;
    }
  }
  var rac = radioAndCheckbox();
  if ((count > 2) && rac){
    document.getElementById("send_btn").disabled = false;
  }else{
    document.getElementById("send_btn").disabled = true;
  }
}

function radioAndCheckbox(){
  var elements = document.getElementsByName('who');
  var checked_radio;
  for(var i = 0 ; i < elements.length ; i ++){
    if(elements[i].checked == true){
      checked_radio = elements[i];
      break;
    }
  }
  var id = checked_radio.id;
  var ids = id.split("-");
  return document.getElementById("check-"+ids[1]).checked;
}
</script>

<?php
include('./footer.html');
?>
