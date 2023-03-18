<?php
require_once "../../dbconnect.php";
    //var_dump($_POST);
if (count($_POST) == 0) {
	header('Location: ../../tests/s/'.$_GET['testId']. '');
}
/*
echo "<pre>";
$val = '';
$str = '';
foreach ($_POST as $key => $value) {
    if ($key != 'ss') {
        if (@count($value) > 1) {
            for ($i=0; $i < count($value) ; $i++) { 
                if($i == 0) {$val = $value[$i];}
                else {$val = $val .'-'. $value[$i];}
            }
        } else {
            if(!is_array($value)) $val = $value;
            else $val = $value[0];
        }
            

    echo $key.' = '. $val;
    echo '<br>';
    $str .= $key.' = '. $val. "\n";
    }
}
echo "<br>";
//echo $str;
echo "</pre>";
*/



function CheckedAnwers($inputType, $q_number, $ans_number) {

if ($inputType == 'checkbox') {
	if(isset($_POST['q'.$q_number])) {
        if (count($_POST['q'.$q_number]) > 1) {
			$ranswers = implode('-', $_POST['q'.$q_number]);
			if (str_contains($ranswers,'a'.$ans_number)) { 
				return 'checked';
			}
		}
		else {
			if ($_POST['q'.$q_number][0] == 'a'.$ans_number) return 'checked';
		}
    }
} 
else {
	if(isset($_POST['q'.$q_number][0])) if ($_POST['q'.$q_number][0] == 'a'.$ans_number) return 'checked';
}

}

function StyleAnswers($raArr, $inputType, $q_number, $ans_number, $mode) {


	if ($mode == 'ALL') {
		if (str_contains($raArr[$q_number], 'a'.$ans_number)) {
			return 'correctAnswer';
		} else return 'incorrectAnswer';
	}

	if ($mode == 'NONE') {
		
	}

	if (CheckedAnwers($inputType, $q_number, $ans_number) == 'checked') {

		if ($mode == 'ALLCHECK') {
		if (str_contains($raArr[$q_number], 'a'.$ans_number)) {
			return 'correctAnswer';
		} else return 'incorrectAnswer';
	}

	if ($mode == 'INCORRECT') {
		if (!str_contains($raArr[$q_number], 'a'.$ans_number)) return 'incorrectAnswer';
	}

	if ($mode == 'CORRECT') {
		if (str_contains($raArr[$q_number], 'a'.$ans_number)) return 'correctAnswer';
	}

	}
}

	



if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}
$raCount = 0;
	


    $result = $pdo->prepare("SELECT * FROM tests WHERE id = :test_id");
        $result->bindParam(':test_id', $_GET['testId'], PDO::PARAM_INT);
        $result->execute(); 
        $test = $result->fetchAll();
        $test = $test[0];

    $result = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = :creator_id");
        $result->bindParam(':creator_id', $test['creator_id'], PDO::PARAM_INT);
        $result->execute(); 
        $current_test_creator = $result->fetchAll();
        $current_test_creator = $current_test_creator[0][0] . ' ' . $current_test_creator[0][1];

include '../../data/libs/includesDeep.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Тест пройдено: <?php echo $test['name'];?> - Knowledge.</title>
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

    <content>
		<div class="content testAllContent" >

		

				

			<?php
            if (isset($_POST['fail'])) {

                $fp = fopen("../".$test['filepath'], "r"); // відкриваємо файл в режимі читання
                if ($fp) {
                    $q_number = 0;
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
                            $test["$config_name"] = trim($config_value);
                        }

                        if (str_contains($str, 'sconfig')) {
                            $config_part = true;
                        }

                    }

                } else echo "Ошибка при открытии файла";


                ?>
<div class="testHeader">
                <h3><span class="material-icons-outlined" >assignment</span><?php echo $test['filename']; ?></h3>
                <p style="display:flex; align-items: center; gap:3px"><span class="material-icons-outlined" style="font-size:20px" >person</span>Автор: <?php if ($current_test_creator == @$_SESSION['full_name']) {echo 'Ви';} else {echo $current_test_creator;} ?></p>

                <div class="testContent failedTestContent">
                    <h4>Результати</h4>
                    <hr>
                    <?php if(isset($_POST['mins'])) { ?>
                        <p>Часу потрачено: <?php echo $_POST['mins'].'хв '.$_POST['secs'].'c' ?> </p>
                        <br>
                    <?php } ?>
                    <div class="course404 testFailed"><span class="material-icons">cancel</span><br><p>Тест провалено.</p></div>
                </div>
            </div>
                <?php
            } else {

$fp = fopen("../".$test['filepath'], "r"); // Открываем файл в режиме чтения
if ($fp) {
    $q_number = 0;
    $q_cfg_part = false;
    $q_cfg_part_done = false;
    $config_part = false;
    $config_part_done = false;
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
                echo '<div class="testQuestion" ><div class="testQuestionTitle" ><div class="testQuestionMark">3 бала</div><h5>' . $q_number . '. ' . $q_title . '</h5></div>';
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

                    <div class="testAnswer form-check <?php echo StyleAnswers($raArr, $q_cfg['type'] , $q_number, $ans_number, $test['show_ans_mode']); ?>"  >
                        <input disabled class="form-check-input testInput TI<?php echo $q_cfg['type'];?>" type="<?php echo $q_cfg['type'];?>" <?php echo $isRequired; ?> id="q<?php echo $q_number;?>a<?php echo $ans_number;?>"  name="<?php echo 'q'. $q_number.'[]'; ?>" value='a<?php echo $ans_number; ?>' autocomplete="off" <?php echo CheckedAnwers($q_cfg['type'], $q_number, $ans_number); ?> >
                        <label class="form-check-label" for="q<?php echo $q_number;?>a<?php echo $ans_number;?>" style="display:inline-block;"><?php echo $q_answer; ?></label>
                    </div>
                    <?php }

                }
            }

            if (str_contains($str, 'squestions')) {
            $q_part = true;
            ?>
        	<div class="testHeader">
        		<h3><span class="material-icons-outlined" >assignment</span><?php echo $test['filename']; ?></h3>
        		<p style="display:flex; align-items: center; gap:3px"><span class="material-icons-outlined" style="font-size:20px" >person</span>Автор: <?php if ($current_test_creator == @$_SESSION['full_name']) {echo 'Ви';} else {echo $current_test_creator;} ?></p>

        		<div class="testContent">
        			<h4>Результати</h4>
        			<hr>
                    <?php if(isset($_POST['mins'])) { ?>
                        <p>Часу потрачено: <?php echo $_POST['mins'].'хв '.$_POST['secs'].'c' ?> </p>
                        <br>
                    <?php } ?>
        			<p>Питань правильно: <span id="raCount"></span> з <span id="qCount"></span></p>
                    <div class="meter " id="pgBar">
                        <span id="pgBarSpan" ><p id="pgBarSpanNumber" style="position: relative; top: -2px; display:flex; align-items:center; justify-content: center;">0%</p></span>

                    </div>
        			<p hidden>Відсотків: <span id="raPercent"></span>%</p>
        			<p>По 5-ти бальній системі: <span id="raFive"></span> балів</p>
        			<p>По 12-ти бальній системі: <span id="raTwelve"></span> балів</p>
        		  <br>
                  <p id="pMinusMark" class="alert alert-dark" ><span class="material-icons" style="color: black; position: relative; top:3px; margin-right: 2px; font-size: 20px;" >warning</span>Було знято <span id="raMinusMark"></span> від макс. кількості балів через перевищення ліміту покидання сторінки</p>
        		</div>
        	</div>

        	<?php
            echo "<div class='testContent'>";
            }
        }

    }

} else echo "Ошибка при открытии файла";
fclose($fp);
}
echo "<a href='../../main/' class='btn btn-dark testSubmit'>На головну</a>";
?>

<?php 
if (isset($_POST['ss'])) {
    foreach ($raArr as $key => $value) {
        if (count($_POST["q$key"]) == 1) {
            if ($value == $_POST["q$key"][0]) $raCount++;
        }
        else {
            $checkBoxValues = explode('-',$value);
            $rightAnswersCount=0;
            for ($i=0; $i < count($_POST["q$key"]) ; $i++) { 
                if (@$checkBoxValues[$i] == $_POST["q$key"][$i]){
                    $rightAnswersCount++;
                }
            }
            if (count($checkBoxValues) == $rightAnswersCount) $raCount++;
 		}
	}
}

if ($test['creator_id'] != @$_SESSION['id']) {

$val = '';
$str = '';
foreach ($_POST as $key => $value) {
    if ($key != 'ss') {
        if (@count($value) > 1) {
            for ($i=0; $i < count($value) ; $i++) { 
                if($i == 0) {$val = $value[$i];}
                else {$val = $val .'-'. $value[$i];}
            }
        } else {
            if(!is_array($value)) $val = $value;
            else $val = $value[0];
        }
            

    // echo $key.' = '. $val;
    // echo '<br>';


    $str .= $key.' = '. $val. "\n";
    }
}


$result = $pdo->prepare("SELECT name,login, tests.id FROM tests, users WHERE tests.id = :test_id AND creator_id = users.id");
$result->bindParam(':test_id', $_GET['testId'], PDO::PARAM_INT);
$result->execute(); 
$abTest = $result->fetchAll();
$abTest = $abTest[0];

$testResultName = $_POST['member_name'] .'`s-result-'.date('d-m-Y-h-i-s');

$filepath = "../../data/tests/".$abTest['login']."/".$abTest['name']."/test_results/". $testResultName .".txt";
$fd = fopen($filepath, 'w+') or die("не удалось создать файл");
fwrite($fd, $str);
fclose($fd);

$result = $pdo->prepare("SELECT tm_id FROM test_members WHERE member_name = :member_name AND test_id = :test_id");
$params = [
"member_name" => $_POST['member_name'],
"test_id" => $abTest['id'] ,
]; 
$result->execute($params); 
$member_id = $result->fetchAll();
$member_id = $member_id[0][0];


if (isset($_POST['mins'])) {
    $pMins = $_POST['mins'];
    $pSecs = $_POST['secs'];
} else {
    $pMins = 0;
    $pSecs = 0;
}


$sql = "INSERT INTO `test_results` (`test_member_id`, `passing_time`, `ra_number`, `leave_number`, `completion_datetime`, `filepath`) VALUES (:test_member_id, :passing_time, :ra_number, :leave_count, :completion_datetime, :filepath)";

$result = $pdo->prepare($sql);
$params = [
"test_member_id" => $member_id,
"passing_time" => $pMins.':'.$pSecs,
"ra_number" => $raCount,
"leave_count" => $_POST['leave_count'],
"completion_datetime" => date('Y-m-d-H-i-s'),
"filepath" => $filepath,
]; 
$result->execute($params);



}

?>


	</div>
    <?php if (!isset($_POST['fail'])) { ?>
		<script type="text/javascript">
            console.log(1);
			var ra_count = document.getElementById('raCount');
			var qCount = document.getElementById('qCount');
			var ra_percent = document.getElementById('raPercent');
			var ra_five = document.getElementById('raFive');
			var ra_twelve = document.getElementById('raTwelve');
            var raMinusMark = document.getElementById('raMinusMark');
			var raCount = <?php echo $raCount; ?>;
			var q_count = <?php echo $q_number; ?>;
            var MinusMark = <?php if (isset($_POST['minusMarkValue'])) {echo $_POST['minusMarkValue'];} else  echo '0'; ?>;
            if (MinusMark == 0) {
                document.getElementById('pMinusMark').hidden = true;
            }
			ra_count.innerHTML = raCount;
			qCount.innerHTML = q_count;
			
			ra_percent.innerHTML = roundToTwo((raCount/q_count)*100);
            ra_percent.innerHTML = roundToTwo(ra_percent.innerHTML - MinusMark);
            if (ra_percent.innerHTML < 0) ra_percent.innerHTML = 0;
			ra_five.innerHTML = roundToTwo((ra_percent.innerHTML/100)*5);
			ra_twelve.innerHTML = roundToTwo((ra_percent.innerHTML/100)*12);
            raMinusMark.innerHTML = MinusMark+'%';

            pgBarSpan.style.width = '0%';

            setInterval(function() {pgBarSpan.style.width = ra_percent.innerHTML+'%'});
            outNum(Math.round(ra_percent.innerHTML),pgBarSpanNumber.id, 1300, 1);
            
            if (Number(ra_percent.innerHTML) > 35) {
                pgBar.classList.add('orange');
            }
            if (Number(ra_percent.innerHTML) > 75) {
                pgBar.classList.add('green');
            }

		</script>
        <?php }?>
	</content>
    </body>
</html>