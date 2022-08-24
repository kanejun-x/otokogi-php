<?php
include('./header.html');


require_once('./lib.php');
$season = get_current_season();

if (!isset($season)){
  echo "シーズン情報が設定されていません。";
  exit(0);
}

?>

<form action="./posted.php" method="post">
<h3>思い出の名前*</h3>
<input id="add_omoide_name" type="text" name="name" required>

<h3>思い出のURL*</h3>
<input id="add_omoide_url" type="text" name="url" required>

<button id='send_btn' type='submit' name='action' value='omoide'>送信</button>
</form>

<?php
include('./footer.html');
?>
