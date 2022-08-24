<?php
require_once('./lib.php');
$season = get_current_season();

include('./header.html');
if (!isset($season)){
  echo "シーズン情報が設定されていません。";
  exit(0);
}

# メンバーリスト取得
$person_list = get_personlist($season);

if(count($_POST) > 0){
  #メンバー追加
  if($_POST['action'] == 'add_person'){
    $name = $_POST['name'];
    if (strlen($name)!=0){
      if(array_search($name, $person_list) === false){
        add_person($season,$name);
      }else{
        $duplicated = true;
      }
    }else{
      $notset =true;
    }
  }elseif($_POST['action'] == 'remove_person') {
  #メンバー削除
    $name = $_POST['name'];
    if (strlen($name)!=0){
      if(array_search($name, $person_list) === false){
        $notexist = true;
      }else{
        if (has_attended($season, $name)){
          $attended = true;
        }else{
          remove_person($season, $name);
        }
      }
    }else{
      $notset =true;
    }
  }
}

# メンバーリスト取得し直す
$person_list = get_personlist($season);
?>

<form action="./setting.php" method="post">
<h3>メンバー変更</h3>
  <h4>現在のメンバー</h4>
  <?php
  $person_str = implode(", ", $person_list);
  echo "<p>$person_str</p>";
  ?>
  <h4>変更メンバー</h4>
  <?php
  if(isset($duplicated)){
    echo "<p class='warning'>$name は既に存在しています。まだ登録されていない名前を入力してください。</p>";
  }elseif (isset($notset)) {
    echo "<p class='warning'>入力文字列がありません。有効な名前を入力してください。</p>";
  }elseif (isset($notexist)) {
    echo "<p class='warning'>$name が存在しません。すでに登録されている名前を入力してください。</p>";
  }elseif (isset($attended)){
    echo "<p class='warning'>$name は既に参加記録が残っているので削除できません。</p>";
  }
  ?>
  <label for='add_person'>名前</label>
  <input id="add_person" type="text" name="name" <?php if($duplicated){echo "value='$name'";} ?>>
  <button type='submit' name='action' value='add_person'>追加</button>
  <button type='submit' name='action' value='remove_person'>削除</button>
</form>

<h3>記録の部分削除</h3>
<p><a href="./remove.php">こちら &raquo;</a></p>


<h3>次シーズンに進む</h3>
<p>Coming soon.</p>

<?php
include('./footer.html');
?>
