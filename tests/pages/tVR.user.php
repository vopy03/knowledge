<?php 


if ($_SESSION['id'] == $test['creator_id']) {
    $asCreator = true;
    $userID = $_GET['secondId'];
    @$secId  = $_GET['thirdId'];
} else {
    $asCreator = false;
    $userID = $usr_mem[0]['tm_id'];
    @$secId  = $_GET['secondId'];
}



$result = $pdo->prepare("SELECT * FROM test_members WHERE tm_id = :user_id AND test_id = :test_id");
$result->bindParam(':user_id', $userID, PDO::PARAM_INT);
$result->bindParam(':test_id', $_GET['testId'], PDO::PARAM_INT);
$result->execute(); 
$mem = $result->fetchAll();
$mem = $mem[0];

$result = $pdo->prepare("SELECT * FROM test_results WHERE test_member_id = :test_member_id ORDER BY `test_results`.`completion_datetime` DESC");
$result->bindParam(':test_member_id', $mem['tm_id'], PDO::PARAM_INT);
$result->execute();
$res = $result->fetchAll();

// echo '<pre>';
// echo count($res);
// echo '</pre>';











	
$raCount = 0;


function CheckedAnwers($fileResult, $inputType, $q_number, $ans_number) {

	if ($inputType == 'checkbox') {
		if(isset($fileResult['q'.$q_number])) {
            if (count($fileResult['q'.$q_number]) > 1) {
    			$ranswers = implode('-', $fileResult['q'.$q_number]);
    			if (str_contains($ranswers,'a'.$ans_number)) { 
    				return 'checked';
    			}
    		}
    		else {
    			if ($fileResult['q'.$q_number][0] == 'a'.$ans_number) return 'checked';
    		}
        }
	} 
	else {
		if(isset($fileResult['q'.$q_number][0])) if ($fileResult['q'.$q_number][0] == 'a'.$ans_number) return 'checked';
	}

	}

	function StyleAnswers($fileResult ,$raArr, $inputType, $q_number, $ans_number, $mode) {
        // $mode = 'ALLCORRECT';

		if ($mode == 'ALL') {
			if (str_contains($raArr[$q_number], 'a'.$ans_number)) {
				return 'correctAnswer';
			} else return 'incorrectAnswer';
		}
        if ($mode == 'ALLCORRECT') {
        if (str_contains($raArr[$q_number], 'a'.$ans_number)) {
                return 'correctAnswer';
            }
        }
		if ($mode == 'NONE') {
			
		}

		if (CheckedAnwers($fileResult, $inputType, $q_number, $ans_number) == 'checked') {

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
if (!$asCreator) include '../../data/libs/headerRDeep.php';
?>

    <content>
		<div class="content testAllContent" >
			<?php if (!isset($secId)) { ?>

		<div class="testHeader">
        		<h3><span class="material-icons-outlined" >assignment</span><?php echo $test['name']; ?></h3>
        		<p style="display:flex; align-items: center; gap:3px"><span class="material-icons-outlined" style="font-size:20px" >person</span> Автор: <?php if ($current_test_creator == @$_SESSION['full_name']) {echo 'Ви';} else {echo $current_test_creator;} ?></p>

        		<div class="testContent">
        			<h4>Результати</h4>
        			<hr>
        			<div class="tVRUserResults">
        			<?php 
                    if (count($res) != 0) {

                    foreach ($res as $key => $val) {
        			 ?>
        			<a href="<?php if($asCreator) {echo $_GET['testId'].'-'.$userID.'-'.$key;} else { echo $_GET['testId'].'-'.$key;} ?>">
	        			<div class="tVRUserResult">
	        				<span class="material-icons-outlined">description</span>
	        				<p><span style="font-size:14px"><?php echo $mem['member_name'];?></span><br><span style="font-size:12px"><?php echo $val['completion_datetime']; ?></span></p>
	        				<p ><span style="font-size:14px">Оцінка:</span> <span style="font-size:12px"><span><?php echo $val['ra_number'].' з '. $test['num_of_questions']; ?></span></p>
	        				<?php if ($key == 0) { ?>
	        					<span class="resultLastMess"><span class="material-icons-outlined" style="font-size:20px" >last_page</span><span style="font-size:14px">Останній результат</span></span>
	        				<?php } ?>
	        			</div>
        			</a>
        			<?php } ?>
        			</div>
        		  <br>
        		</div>
                    <?php } else {
                        ?>
                        <style type="text/css">
                            .testContent {
                                padding: 20px;
                            }
                            .course404 {
                                transform: translate(-50%, -30%);
                            }
                        </style>
                        <div style="height: 250px;">
                                <div class="course404"><span class="material-icons">content_paste_off</span><br><p>Немає результатів</p></div>
                            </div>
                        <?php } ?>
        	</div>

				

			<?php } else {
				// $wrongSecondIdButOk = false;
				if($secId >= count($res)) {
					$secId = count($res)-1;
					// $wrongSecondIdButOk = true;
				}

				$res = $res[$secId];




			$fp = fopen("".$res['filepath'], "r"); // Открываем файл в режиме чтения
			    if ($fp) {
			        while (!feof($fp)) {

			            $str = fgets($fp);  
			                $q = substr($str , 0 ,strpos($str, '=')-1);
			                $a = substr($str , strpos($str, '=')+2);
                            if (str_contains($a,'-')) {
                                $fileResult["$q"] = explode('-',trim($a));
                            } else {
                                $fileResult["$q"][0] = trim($a);
                            }
                            
			        }

			    } else echo "Ошибка при открытии файла";
			    fclose($fp);

			

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
            // echo $config_name. ' = ';
            // echo $test["$config_name"] . '<br>';
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

                        if($asCreator) {
                            $test['show_ans_mode'] = 'ALLCORRECT';
                        }
                    ?>

                    <div class="testAnswer form-check <?php echo StyleAnswers($fileResult, $raArr, $q_cfg['type'] , $q_number, $ans_number, $test['show_ans_mode']); ?>"  >
                        <input disabled class="form-check-input testInput TI<?php echo $q_cfg['type'];?>" type="<?php echo $q_cfg['type'];?>" <?php echo $isRequired; ?> id="q<?php echo $q_number;?>a<?php echo $ans_number;?>"  name="<?php echo 'q'. $q_number.'[]'; ?>" value='a<?php echo $ans_number; ?>' autocomplete="off" <?php echo CheckedAnwers($fileResult, $q_cfg['type'], $q_number, $ans_number); ?> >
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
        		<p style="display:flex; align-items: center; gap:3px"><span class="material-icons-outlined" style="font-size:20px" >person</span> Автор: <?php if ($current_test_creator == @$_SESSION['full_name']) {echo 'Ви';} else {echo $current_test_creator;} ?></p>

        		<div class="testContent">
        			<h4>Результат</h4>
        			<!-- <span><?php if($wrongSecondIdButOk) echo 'Останній наявний результат' ; ?></span> -->
        			<span><?php if($asCreator) { echo '<i>користувача '.$mem['member_name'].'</i>';} ?> <?php echo 'за'.' '.$res['completion_datetime'] ; ?></span>
                    <?php if($asCreator && is_numeric($test['max_leave_count']) && $test['max_leave_count'] != 0) { ?>
                    <hr>
                        <span>Покидань сторінки: </span>
                        <div style="position: relative; background-color:5px">
                            <div class="progress" style="position: relative;">
                              <div class="progress-bar  <?php if($fileResult['leave_count'][0] > $test['max_leave_count']) echo 'bg-danger'; ?>" role="progressbar" style="width: <?php echo round(($fileResult['leave_count'][0]/$test['max_leave_count'])*100); ?>%;">
                                
                                <?php echo $fileResult['leave_count'][0]; ?>
                                </div>
                              
                            </div>
                            <span class="pBRanges" style="left:0px">0</span>
                            <span class="pBRanges" style="right:0px"><?php echo $test['max_leave_count']; ?></span>
                        </div>
                        <br>
                    <?php } ?>
        			<hr>
                    <?php if(isset($fileResult['mins'])) { ?>
                        <p>Часу потрачено: <?php echo $fileResult['mins'][0].'хв '.$fileResult['secs'][0].'c' ?> </p>
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
        		<?php if ($secId == 0) { ?>
        		<span class="resultLastMess rLMInResult"><span class="material-icons-outlined" style="font-size:20px" >last_page</span><span style="font-size:14px">Останній результат</span></span>
        		<?php } ?>
        	</div>

        	<?php
            echo "<div class='testContent'>";
            }
        }

    }

} else echo "Ошибка при открытии файла";
fclose($fp);

echo "<a href='../../main/' class='btn btn-dark testSubmit'>На головну</a>";

?>

<?php 

    foreach ($raArr as $key => $value) {
        if (count($fileResult["q$key"]) == 1) {
            if ($value == $fileResult["q$key"][0]) $raCount++;
        }
        else {
            $checkBoxValues = explode('-',$value);
            $rightAnswersCount=0;
            for ($i=0; $i < count($fileResult["q$key"]) ; $i++) { 
                if (@$checkBoxValues[$i] == $fileResult["q$key"][$i]){
                    $rightAnswersCount++;
                }
            }
            if (count($checkBoxValues) == $rightAnswersCount) $raCount++;
 		}
	}
?>


	</div>

		<script type="text/javascript">
            <?php
            if ($userID != $test['creator_id']) { ?>
            	
            
			var ra_count = document.getElementById('raCount');
			var qCount = document.getElementById('qCount');
			var ra_percent = document.getElementById('raPercent');
			var ra_five = document.getElementById('raFive');
			var ra_twelve = document.getElementById('raTwelve');
            var raMinusMark = document.getElementById('raMinusMark');

            var pgBar = document.getElementById('pgBar');
            var pgBarSpan = document.getElementById('pgBarSpan');
            var pgBarSpanNumber = document.getElementById('pgBarSpanNumber');

			var raCount = <?php echo $raCount; ?>;
			var q_count = <?php echo $q_number; ?>;
            var MinusMark = <?php if (isset($fileResult['minusMarkValue'][0])) {echo $fileResult['minusMarkValue'][0];} else  echo '0'; ?>;
            if (MinusMark == 0) {
                document.getElementById('pMinusMark').hidden = true;
            }
			ra_count.innerHTML = raCount;
			qCount.innerHTML = q_count;
			
			ra_percent.innerHTML = roundToTwo((raCount/q_count)*100);
            ra_percent.innerHTML = ra_percent.innerHTML - MinusMark;
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
        <?php } }?>

		</script>
	</content>
