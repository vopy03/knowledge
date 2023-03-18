<?php
require_once "../../dbconnect.php";


if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}

    function readF($test){

        $fp = fopen("../".$test['filepath'], "r"); // Открываем файл в режиме чтения
        if ($fp) {
            $q_number = 0;
            $q_cfg_part = false;
            $q_cfg_part_done = false;
            $config_part = false;
            while (!feof($fp)) {

                $str = fgets($fp);  
                if (str_contains($str, 'econfig')) {
                    fclose($fp);
                    break;
                }
                if ($config_part) {
                    $config_name = substr($str , 0 ,strpos($str, '=')-1);
                    $config_value = substr($str , strpos($str, '=')+2);
                    $test['config']["$config_name"] = trim($config_value);
                }

                if (str_contains($str, 'sconfig')) {
                    $config_part = true;
                }

            }
            return $test;

        } else echo "Ошибка при открытии файла";
    }

    if(!isset($_POST['trueTestStart'])) {
        header('Location: ../../tests/s/'.$_GET['testId']. '');
    }

    $result = $pdo->prepare("SELECT * FROM tests WHERE id = :test_id");
        $result->bindParam(':test_id', $_GET['testId'], PDO::PARAM_INT);
        $result->execute(); 
        $current_test = $result->fetchAll();
        $current_test = $current_test[0];

    $result = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = :creator_id");
        $result->bindParam(':creator_id', $current_test['creator_id'], PDO::PARAM_INT);
        $result->execute(); 
        $current_test_creator = $result->fetchAll();
        $current_test_creator = $current_test_creator[0][0] . ' ' . $current_test_creator[0][1];

        $isNotAuthor = true;
        if(isset($_SESSION['id'])) {
            if(($_SESSION['id'] == $current_test['creator_id'] )) {
                $isNotAuthor = false;
            }
        }

    if($isNotAuthor) {

            if(isset($_SESSION['id'])) {
                $result = $pdo->prepare("SELECT * FROM test_members WHERE user_id = :user_id AND test_id = :test_id");
                $params = [
                "user_id" => $_SESSION['id'],
                "test_id" => $_GET['testId'],
                ]; 
                $result->execute($params); 
                $member = $result->fetchAll();

            } 
            else {
                $result = $pdo->prepare("SELECT * FROM `test_members` WHERE `member_name` = :member_name AND test_id = :test_id");
                $params = [
                "member_name" => $_POST['username'],
                "test_id" => $_GET['testId'],
                ]; 
                $result->execute($params); 
                $member = $result->fetchAll();
            }
            
            
            
            
            if (!$member) {

                $result = $pdo->prepare("SELECT * FROM tests WHERE id = :test_id");
                $params = [
                    "test_id" => $_GET['testId'],
                ];
                $result->execute($params); 
                $test = $result->fetchAll();
                $test = $test[0];
                $test = readF($test);
            
            if (isset($_SESSION['id'])) {
                $usID = $_SESSION['id'];
            } else {
                $usID = NULL;
            }
            

            $sql = "INSERT INTO `test_members` (`member_name`,`test_id`, `user_id`, `remaining_attempts`) VALUES (:member_name, :test_id, :user_id, :remaining_attempts)";

            $result = $pdo->prepare($sql);
            $params = [
            "member_name" => $_POST['username'],
            "test_id" => $_GET['testId'],
            "user_id" => $usID,
            "remaining_attempts" => $test['config']['attempts']-1,
            ]; 
            $result->execute($params);
            }
            else {
            $member = $member[0];
            var_dump($member['remaining_attempts']);
            if ($member['remaining_attempts'] == 0) {
                header('Location: ../../tests/s/'.$_GET['testId']. '-'.$member["tm_id"].'');
            }
            else {
                $sql = "UPDATE `test_members` SET `remaining_attempts`= :remaining_attempts WHERE test_id = :test_id AND member_name = :member_name ";

                $result = $pdo->prepare($sql);
                $params = [
                "test_id" => $member['test_id'],
                "member_name" => $_POST['username'],
                "remaining_attempts" => $member['remaining_attempts']-1,
                ]; 
                $result->execute($params);
                }
            }

        }

include '../../data/libs/includesDeep.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Тест: <?php echo $current_test['name'];?> - Knowledge.</title>
</head>
<body <?php if (@$_SESSION['dark'] == 1) { ?> class='dark_mode' <?php } ?>>
    <div id="headerRightContainerNRThemeToggle" class="hRCNRTInTest" align="right">
        
        <span class="material-icons-outlined" id="HRCDarkMode">brightness_5</span>

        <script type="text/javascript">
            var HRCDarkMode = document.getElementById('HRCDarkMode');
            if(document.body.classList.contains('dark_mode')) {
                    HRCDarkMode.innerHTML = 'brightness_5';
                } else {
                    HRCDarkMode.innerHTML = 'dark_mode';
                }
            HRCDarkMode.onclick = function () {
                document.body.classList.toggle('dark_mode');
                if(document.body.classList.contains('dark_mode')) {
                    HRCDarkMode.innerHTML = 'brightness_5';
                } else {
                    HRCDarkMode.innerHTML = 'dark_mode';
                }
            }
            $(document).ready(function(){
                $('#HRCDarkMode').click(function(){
                    $.ajax({
                        url: "../../data/libs/changeTheme.php",
                        success: function(html){
                            $("#content").html(html);
                        }
                    });
                    return false;
                });
        });
        </script>
        
    </div>
    <content id="testPageContent">
		<div class="content testAllContent" >

				

			<?php

$fp = fopen("../".$current_test['filepath'], "r"); // Открываем файл в режиме чтения
if ($fp) {
    $q_number = 0;
    $q_cfg_part = false;
    $q_cfg_part_done = false;
    $config_part = false;
    $config_part_done = false;
    echo "<form method='POST' id='testForm' action='../c/".$_GET['testId']."'>";
    while (!feof($fp)) {

        $str = fgets($fp);
        if (!$config_part_done) {
            
        
        if (str_contains($str, 'econfig')) {
            $config_part = false;
            $q_part = false;
            $config_part_done = true;
        }

        if ($config_part) {
            $config_name = substr($str , 0 ,strpos($str, '=')-1);
            $config_value = substr($str , strpos($str, '=')+2);
            $test["$config_name"] = trim($config_value);
            //echo $config_name. ' = ';
            //echo $test["$config_name"] . '<br>';
        }

        if (str_contains($str, 'sconfig')) {
            $config_part = true;
        }

        } else { 

            if (str_contains($str, 'equestions')) {
            $q_part = false;
            echo '</div>';
            }


            if($q_part) {

                if (str_contains($str, '[Q]')) {
                $q_title = substr($str , 3);
                if ($q_number > 0) {
                	echo '</div>';
                }
                $q_number++;
                $ans_number = 0;
                echo '<div class="testQuestion" ><div class="testQuestionTitle" > <!-- <div class="testQuestionMark">3 бала</div> --> <h5>' . $q_number . '. ' . $q_title . '</h5></div>';
                }

                if (str_contains($str, '[eQCFG]')) {
                $q_cfg_part = false;
                $q_cfg_part_done = true;

                }

                if ($q_cfg_part) {
                $config_name = substr($str , 0 ,strpos($str, '=')-1);
                $config_value = substr($str , strpos($str, '=')+2);
                $q_cfg["$config_name"] = trim($config_value);
                if($config_name == 'ra') $raArr[$q_number] = $q_cfg["$config_name"] ;
                }




                if (str_contains($str, '[sQCFG]')) {
                $q_cfg_part = true;

                }

                if($q_cfg_part_done) {

                    if (str_contains($str, '[A]')) {
                    $ans_number++;
                    $q_answer = substr($str , 3);
                    $isRequired = ($q_cfg['type'] == 'checkbox') ? '' : 'required' ; 
                    ?>
                    <div class="testAnswer form-check" ><input class="form-check-input testInput TI<?php echo $q_cfg['type'];?>" type="<?php echo $q_cfg['type'];?>" <?php echo $isRequired; ?> id="q<?php echo $q_number;?>a<?php echo $ans_number;?>"  name="<?php echo 'q'. $q_number.'[]'; ?>" value='a<?php echo $ans_number; ?>' autocomplete="off"><label class="form-check-label" for="q<?php echo $q_number;?>a<?php echo $ans_number;?>" style="display:inline-block;"><?php echo $q_answer; ?></label></div>
                    <?php }

                }

                

                

            }

            if (str_contains($str, 'squestions')) {
            $q_part = true;
            ?>
        	<div class="testHeader">
        		<h3><span class="material-icons-outlined" >assignment</span><?php echo $test['filename']; ?></h3>
        		<p style="display:flex; align-items: center; gap:3px"><span class="material-icons-outlined" style="font-size:20px" >person</span>Автор: <?php if ($current_test_creator == @$_SESSION['full_name']) {echo 'Ви';} else {echo $current_test_creator;} ?></p>
        		<div class="testInfo" id="testInfo" title="Натисніть щоб прикріпити">
        			<p class="testInfoSmallTitle" align="center"><span class="material-icons-outlined">info</span> Інформація про тест</p>
        		<?php 
                if(isset($test['time'])) { ?>
                    <span class="material-icons-outlined testInfoSpans" style="margin-left: 5px;" >schedule</span><p id="timer" style="display: inline-flex;" ></p>
                <?php } ?>
        		<?php  if(is_numeric($test['max_leave_count'])) { ?>
        		<p id="text"><span class="material-icons-outlined testInfoSpans" style="margin-right: 5px;" >surfing</span>Кількість покидань сторінки: 
        			<span id="num">0</span><span>/<?php echo $test['max_leave_count'];?></span>
        		</p>
        		<?php } else echo "<p style='margin-bottom:-10px;'></p>"; ?>
        		<span class="material-icons-outlined testInfoSpans" style="margin-left: 5px;" >question_mark</span><p id="q_number" style="display:inline-flex;"></p>
        		</div>
        	</div>
        	<?php
            echo "<div class='testContent'>";
            }
        }

    }

} else echo "Ошибка при открытии файла";
fclose($fp);
?>
<input id="testMinusMarkValue" type="number" value="0" name="minusMarkValue" hidden >
<?php if(isset($test['time'])) { ?>
    <input id="testInputTimeMins" type="number" value="0" name="mins"  hidden>
    <input id="testInputTimeSecs" type="number" value="0" name="secs"  hidden>
<?php }?>
<input class='btn btn-dark testSubmit' type='submit' name='ss' value='Завершити тест'>
<input id="testLeaveCount" type="number" value="0" name="leave_count" width="0" height="0"  hidden>
<input id="testMemName" type="text" value="<?php echo $_POST['username']; ?>" name="member_name" width="0" height="0"  hidden>
</form>
<script type="text/javascript">
	var q_number = document.getElementById('q_number');
	q_number.innerHTML = "Кількість питань: <?php echo $q_number; ?>";
</script>



<script type="text/javascript">
    var testForm = document.getElementById('testForm');
    var testInfo = document.getElementById('testInfo');
    testInfo.onclick = function() {
        if (this.classList == 'testInfo') {
            this.classList.remove('testInfo');
            this.classList.add('fixedInfo');
            this.title = "Натисніть щоб відкріпити";
        } else {
            this.classList.add('testInfo');
            this.classList.remove('fixedInfo');
            this.title = "Натисніть щоб прикріпити";
        }
    }
    function testFail(testForm) {
            var fail = document.createElement('input');
            fail.hidden = true;
            fail.type = 'text';
            fail.name = 'fail';
            testForm.appendChild(fail);
            testForm.submit();
        }
    <?php if(is_numeric($test['max_leave_count'])) { ?>
    var text = document.getElementById('text');
    var testLeaveCount = document.getElementById('testLeaveCount');
    var num = document.getElementById('num');
    var blur = <?php if(isset($test['mlc_blur'])) echo $test['mlc_blur']; else echo "false"; ?>;
    window.onblur = function() {
        num.innerHTML = (Number(num.innerHTML)+1);
        testLeaveCount.value = num.innerHTML;
        if (blur) {
        document.body.style.filter = 'blur(5px)';
        }

        


        if (num.innerHTML > <?php echo $test['max_leave_count'] ?>) {
        text.style.color = '#e74c3c';
        //alert('Ви перебільшили максимально допустиму кількітсть покидань сторінки. Оцінку буде знижено');

        var mlc_action = "<?php echo $test['mlc_action'] ?>";

        var testMinusMarkValue = document.getElementById('testMinusMarkValue');
        if (mlc_action == 'fail') {
            //test failed
            testFail(testForm);
        }

        if (mlc_action == 'lowerMarkOnce') {
            testMinusMarkValue.value = <?php if(isset($test['mlc_action_value'])) echo $test['mlc_action_value']; else echo "0"; ?>;
        }

        if (mlc_action == 'lowerMarkEveryTime') {
            testMinusMarkValue.value = Number(testMinusMarkValue.value) + <?php if(isset($test['mlc_action_value'])) echo $test['mlc_action_value']; else echo "0"; ?>;
            }
            if (testMinusMarkValue.value > 99) {
                //test failed
                testFail(testForm);
            }
        }

        if (mlc_action == 'warning') {

        }




    }
    window.onfocus = function() {
        document.body.style.filter = '';
    }

    <?php } ?>

    <?php 
    if(isset($test['time'])) {
        $test['time'] = explode(':', $test['time']);
    ?>
    
    // Set the date we're counting down to

function minute(a) {
    a = Math.floor(a*1000*60);
    return a;
}

function second(a) {
    a = Math.floor(a*1000);
    return a;
}
var timer = minute(<?php echo $test['time'][0] ?>) + second(<?php echo $test['time'][1] ?>);

var countDownDate = new Date().getTime() + timer;

// Update the count down every 1 second
var TimeSpentSecs = 0;
var countdownfunction = setInterval(function() {

    // Get todays date and time
    var now = new Date().getTime();
    
    // Find the distance between now an the count down date
    TimeSpentSecs += second(1);
    var distance = countDownDate - now;
    var Rdistance = TimeSpentSecs;
    
    // Time calculations for days, hours, minutes and seconds
    var minutes = Math.floor((distance % (100000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    var Rminutes = Math.floor((Rdistance % (100000 * 60 * 60)) / (1000 * 60));
    var Rseconds = Math.floor((Rdistance % (1000 * 60)) / 1000);

    // Output the result in an element with id="demo"
    document.getElementById('timer').innerHTML = "Часу залишилось: " + minutes + "хв " + seconds + "с ";
    document.getElementById('testInputTimeMins').value = Rminutes;
    document.getElementById('testInputTimeSecs').value = Rseconds;

    // If the count down is over, write some text 
    if (distance < 0) {
        clearInterval(countdownfunction);
        document.getElementById('timer').innerHTML = "Час вичерпано!";
        setInterval(function() {testFail(testForm)}, 1000);
    }
}, 1000);




<?php }?>

</script>
	</div>
	</content>
    <?php if ($test['widget_calc'] == 'true') { ?>
    <div class="calculator" id="calcDiv" >
        <div class="calcHeader" id="calcHeader">
            <p><span class="material-icons">calculate</span><span>Калькулятор</span></p>
            <div class="calcHButtons">
                <span class="material-icons" id="calcHMinim">minimize</span>
                <span class="material-icons calcHClose" id="calcHClose" >close</span>
            </div>
        </div>
            <input type="text" name="screen" id="answer" readonly>
            <table>
                <tr>
                    <td><button class='calcButton'>(</button></td>
                    <td><button class='calcButton'>)</button></td>
                    <td><button class='calcButton' style="background-color: #e74c3c; font-weight: bold; color: #ecf0f1;">C</button></td>
                    <td><button class='calcButton'>%</button></td>
                </tr>
                <tr>
                    <td><button class='calcButton'>7</button></td>
                    <td><button class='calcButton'>8</button></td>
                    <td><button class='calcButton'>9</button></td>
                    <td><button class='calcButton'>X</button></td>
                </tr>
                <tr>
                    <td><button class='calcButton'>4</button></td>
                    <td><button class='calcButton'>5</button></td>
                    <td><button class='calcButton'>6</button></td>
                    <td><button class='calcButton'>-</button></td>
                </tr>
                <tr>
                    <td><button class='calcButton'>1</button></td>
                    <td><button class='calcButton'>2</button></td>
                    <td><button class='calcButton'>3</button></td>
                    <td><button class='calcButton'>+</button></td>
                </tr>
                <tr>
                    <td><button class='calcButton'>0</button></td>
                    <td><button class='calcButton' style="font-weight: bold;">.</button></td>
                    <td><button class='calcButton'>/</button></td>
                    <td><button class='calcButton' tabindex="-1" id="doriv" style="background-color: #2ecc71; font-weight: bold; color: #ecf0f1;">=</button></td>
                </tr>
            </table>
            <script type="text/javascript">

                var calcDiv     = document.getElementById('calcDiv');
                var calcHClose  = document.getElementById('calcHClose');
                var calcHMinim  = document.getElementById('calcHMinim');
                var calcHeader  = document.getElementById('calcHeader');

                    calcDiv.classList.toggle('calcHidden');
                    calcHClose.innerHTML = 'arrow_upward';
                    calcHClose.classList.toggle('calcHArrowUp');
                    calcHMinim.classList.toggle('hideMe');
                
               calcHeader.onclick = function() {
                    calcDiv.classList.toggle('calcHidden');
                    if (calcHClose.innerHTML == 'close') {
                        calcHClose.innerHTML = 'arrow_upward';
                        calcHClose.classList.toggle('calcHArrowUp');
                        calcHMinim.classList.toggle('hideMe');
                    } else {
                        calcHClose.innerHTML = 'close';
                        calcHMinim.classList.toggle('hideMe');
                        calcHClose.classList.toggle('calcHArrowUp');
                    }
                };

                
document.getElementById('answer').readOnly = true; //set this attribute in Html file
let screen = document.getElementById('answer');
buttons = document.querySelectorAll('.calcButton');
let screenValue = '';
for (item of buttons) {
    item.addEventListener('click', (e) => {
        //console.log(buttonText, "has been pressed");
        buttonText = e.target.innerText;
        if (buttonText == 'X') {
            buttonText = '*';
            screenValue += buttonText;
            screen.value = screenValue;
        }
        else if (buttonText == 'C') {
            screenValue = "";
            screen.value = screenValue;

        }
        else if (buttonText == '=') {
            screen.value = eval(screenValue);
        }
        else {
            screenValue += buttonText;
            screen.value = screenValue;
        }

    })
}

document.addEventListener("keydown", function(event) {
    document.getElementById('doriv').focus();
    console.log(event.which);
    if(event.shiftKey==57){
        event.key = '(';
    }
    else if(event.shiftKey==48){
        event.key = ')';
    }
    else if(event.shiftKey==53){
        event.key = '%';
    }
    if(event.keyCode==88){
        screenValue += '*';
        screen.value = screenValue;
    }
    if(event.key<=9 || event.key=='+' || event.key=='-' || event.key=='*' || event.key=='.' || event.key=='/' || event.key=='%' || event.key=='(' || event.key==')'){
        screenValue += event.key;
        screen.value = screenValue;
    }
    if(event.keyCode == 13 || event.keyCode == 187)
    {
        screen.value = eval(screenValue);
    }
    else if(event.keyCode == 46){
        screenValue = "";
        screen.value = screenValue;
        console.clear();
    }
    else if(event.keyCode == 8){
        screenValue = screenValue.slice(0, -1);
        screen.value = screenValue;
    }
    else if(event.keyCode == 67){
        screenValue = "";
        screen.value = screenValue;
        console.clear();
    }
    else if(event.keyCode == 82){
        location.reload();
    }
  })

  window.onerror = function(){
      // alert("PLEASE INPUT VALID EXPRESSION");
      screenValue = "";
      screen.value = screenValue;
      console.clear();
  }
            </script>
        <?php } ?>
        </div>
    </body>
</html>