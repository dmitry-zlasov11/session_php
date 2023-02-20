<?session_start();?>
<?

$basehp = 10;

// Определяем конфигурацию игры
if (!isset($_SESSION['GAME'])) {
    $_SESSION['GAME'] = [
         'LOSE' => null,
         'STEP' => 0,
         'PLAYERS' => [
             'PLAYER_CLIENT' => [
                 'NAME' => 'client',
                 'DAMAGE' => [],
                 'HEALTH' => $basehp,
             ],
             'PLAYER_SERVER' => [
                 'NAME' => 'server',
                 'DAMAGE' => [],
                 'HEALTH' => $basehp,
             ]
         ]
    ];
 }


// echo '<pre>';
// print_r($_SESSION['GAME']);
// echo '</pre>';


// Сброс
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['reset'])) {
    unset($_SESSION['GAME']);
}


// нанесение урона
function setDamage($damage, &$user){
    $user['HEALTH'] -= $damage;
    $user['DAMAGE']['STEP '.$_SESSION['GAME']['STEP']]
    = $user['NAME']." получил урон в количестве - ".$damage." единиц";
}

// Проверка здоровья
function checkHealth(&$gameInfo){
    if ($gameInfo['PLAYERS']) {
        foreach($gameInfo['PLAYERS'] as $user) {
            if ($user['HEALTH'] <= 0) {
                $gameInfo['LOSE'] = $user['NAME'];
                break;
            }
        }
    }
}


function redirectUrl($url){
        header('Location: '.$url);
}



if($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['damage']) && $_POST['damage'] >= 1 && $_POST['damage'] <= 3)){

    $value_user = intval($_POST['damage']);
    $rand_number = rand(1,3);
  
    $_SESSION['GAME']['STEP']++;

    if($value_user === $rand_number) {
       $damage = rand(1, 4);
       array_push($_SESSION['GAME']['PLAYERS']['PLAYER_CLIENT']['DAMAGE'], $damage);
       setDamage($damage,  $_SESSION['GAME']['PLAYERS']['PLAYER_CLIENT']);
    }else {
        $damage = rand(1, 4);
        array_push($_SESSION['GAME']['PLAYERS']['PLAYER_SERVER']['DAMAGE'], $damage);
 
        setDamage($damage,  $_SESSION['GAME']['PLAYERS']['PLAYER_SERVER']);
    }
         checkHealth($_SESSION['GAME']);

         if ($_SESSION['GAME']['LOSE']) {
            redirectUrl($_SERVER['SCRIPT_NAME'].'?page=game1over');
        } else {
            redirectUrl($_SERVER['SCRIPT_NAME']);
        }
 
}
?>


<? if(!$_SESSION['GAME']['LOSE']): ?>
<!-- form battle -->

<form method="POST">
    <label for="">Введите любое число от 1 до 3</label>
    <input type="number" name='damage' id='damage' min='1' max='3' required>
    <button type='submit'>Отправить</button>
</form>

<?else:?>
    <div style="color:red;">Проиграл <?=$_SESSION['GAME']['LOSE'];?></div>
    <a href="<?=$_SERVER['SCRIPT_NAME']?>?reset">Повторить</a>

<?endif;?>

<?$step = $_SESSION['GAME']['STEP'];?>
<?if ($step):?>
    <h1>Шаг: <?=$step?></h1>
    <table>
        <tr>
            <?foreach ($_SESSION['GAME']['PLAYERS'] as $user):?>
                <th><?=$user['NAME'];?> HP(<?=$user['HEALTH'];?>)</th>
            <?endforeach;?>
        </tr>
        <?for ($i = 0; $i <= $step ; $i++):?>
        <tr>
            <?foreach ($_SESSION['GAME']['PLAYERS'] as $user):?>
                <td><?=$user['DAMAGE']['STEP ' .$i];?></td>
            <?endforeach;?>
        </tr>
        <?endfor;?>
    </table>    
<?endif;?>



