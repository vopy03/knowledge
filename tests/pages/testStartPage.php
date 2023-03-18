<?php
require_once "../../dbconnect.php";
include '../../data/libs/userCheck.php';
    //userCheck();
	$test['name'] = 'NotFound';
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}
	


    $result = $pdo->prepare("SELECT * FROM tests WHERE id = :test_id");
        $result->bindParam(':test_id', $_GET['testId'], PDO::PARAM_INT);
        $result->execute(); 
        $current_test = $result->fetchAll();
        if ($current_test == NULL) {
        }else {
        $test = $current_test[0];

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

		} else echo "Ошибка при открытии файла";

    	$result = $pdo->prepare("SELECT first_name, last_name, id FROM users WHERE id = :creator_id");
        $result->bindParam(':creator_id', $test['creator_id'], PDO::PARAM_INT);
        $result->execute(); 
        $current_test_creator = $result->fetchAll();
        $test_creator = $current_test_creator[0];
        $current_test_creator = $current_test_creator[0][0] . ' ' . $current_test_creator[0][1];

        $result = $pdo->prepare("SELECT remaining_attempts FROM test_members WHERE member_name = :mem_name AND test_id = :test_id");
        $params = [
        "mem_name" => @$_SESSION['full_name'],
        "test_id" => $_GET['testId'],
        ];
        $result->execute($params); 
        $member = $result->fetchAll();
        @$rem_atp = $member[0][0];
    }

    if (isset($_SESSION['id']) && @$test_creator['id'] == $_SESSION['id']) {
    	$rem_atp = 2; 
    }

include '../../data/libs/includesDeep.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Тест: <?php echo $test['name'];?> - Knowledge.</title>
</head>
<body <?php if (@$_SESSION['dark'] == 1) { ?> class='dark_mode' <?php } ?>>


    <content>
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

		<div class="content testAllContent" >

					<div class="contentHeader">
							<a class="courseButton rounded" href="../../main">
								<span class="material-icons-outlined courseBSpan">home</span>
								<span>На головну</span>
							</a>
					</div>

				<?php
				if ($current_test == NULL) { ?>
				 	<div class="testHeader" style="min-height: 450px;">
        				<h3>Такого тесту не існує!</h3>

        				<div class="testContent">
        					<div class="course404" style="top: 200px;"><span class="material-icons">&#xe5cd</span><br><p>Тест не знайдено.</p></div>
        		
        				</div>
        				<a href='../../main/' class='btn btn-dark testSubmit'>На головну</a>
        			</div>
				 <?php } else { ?>
        	<div class="testHeader" style="margin-top:0">
        		<h3><span class="material-icons-outlined" >assignment</span><?php echo $test['name']; ?></h3>
        		<p style="display:flex; align-items: center; gap:3px"><span class="material-icons-outlined" style="font-size:20px" >person</span> Автор: <?php if ($current_test_creator == @$_SESSION['full_name']) {echo 'Ви';} else {echo $current_test_creator;} ?></p>

        		<div class="testStartInfo" id="testInfo">
        			<p class="testInfoSmallTitle" align="center"><span class="material-icons-outlined">info</span> Інформація про тест</p>
        		<?php if(isset($test['config']['time'])) { 
        			$test['config']['time'] = explode(':', $test['config']['time']);
        			?>
        			<p style="display:inline-flex;">
                    <span class="material-icons-outlined testInfoSpans" style="margin-right: 5px;" >schedule</span><p id="timer" style="display: inline-flex;" >Часу виділено: <?php echo $test['config']['time'][0]. ' хв '. $test['config']['time'][1]. ' с' ; ?></p></p>
                <?php } ?>
                <p style="display:inline-flex;" >
        		<span class="material-icons-outlined testInfoSpans" style="margin-right: 5px;" >question_mark</span><p id="q_number" style="display:inline-flex;">Кількість питань: <?php echo $test['num_of_questions']; ?></p></p>
        		<?php if (is_numeric($test['config']['max_leave_count'])) { ?>
        		<p id="text"><span class="material-icons-outlined testInfoSpans" style="margin-right: 5px;" >surfing</span>Допустима кількість покидань сторінки: 
        			<span><?php echo $test['config']['max_leave_count'];?></span>
        		</p>
        	<?php } ?>
        		</div>
        		<div class="testContent">
        			<form method="POST" action="../p/<?php echo $_GET['testId'] ?>">
        				
        				<p class="disabled">Ви проходите тест як: <span id="testStartGoAs" ><?php if (isset($_SESSION['full_name'])) { echo $_SESSION['full_name']; } ?></span></p>
        				<input type="text" id="testStartGoAsValue" name="username" value="" hidden>
        				<br>
        				<div class="testStartDescription">
        					<?php if (isset($test['config']['description'])) {?>
	        					<div class="testDescription">
	        						
	        						<h5>Опис</h5>
	        						<p><?php echo $test['config']['description']; ?></p>
	        						<!--<p>Тест створено виключно для переверки працездатності сайту та для того щоб автор зміг написати опис тесту щоб в подальшому подивитись як він буде виглядати. Найкраще всього буде, коли тексту буде багато, як от зараз.</p>-->
	        					</div>
	        					<br>

        					<?php } ?>

        					<?php if($test['creator_id'] == @$_SESSION['id']) { ?>
        						<p class="alert alert-dark sAASuccess"><span class="material-icons" style="margin-right:5px; color: black" >thumb_up_alt</span>Ви, як автор цього тесту, можете проходити його скільки завгодно. Ваші результати не будуть збережені.</p>
        					<?php } ?>

        					<?php
        						if (is_numeric($test['config']['max_leave_count'])) {
        							$actMess = '';
        							$AMID = 'Warning';
        							if ($test['config']['mlc_action'] == 'fail') {
        								$actMess = 'тест буде провалено';
        								$AMID = 'Fail';
        							}
        							if ($test['config']['mlc_action'] == 'lowerMarkOnce') {
        								$actMess = 'буде знято '. $test['config']['mlc_action_value'] .'% від максимального балу';
        								$AMID = 'LMO';
        							}
        							if ($test['config']['mlc_action'] == 'lowerMarkEveryTime') {
        								$actMess = 'буде знято '. $test['config']['mlc_action_value'].' % від максимального балу кожного разу. Якщо загальна кількість знятих відсотків буде більше 100%, то тест буде провалено';
        								$AMID = 'LMET';
        							}
        							if ($test['config']['mlc_action'] == 'warning') {
        								$actMess = 'автор буде про це знати';
        							}

        								echo '<p class="alert alert-dark" ><span class="material-icons" style="color: black" >warning</span> Тест містить обмеження кількості покидання сторінки під час проходження тесту. При перевищенні ліміту <span id="actMess'.$AMID.'" >'. $actMess .'.</span></p>';
        						}

        						if ($test['config']['widget_calc'] == 'true') { ?>

        								<p class="alert alert-dark sAASuccess"><span class="material-icons" style="margin-right:5px; color: black" >calculate</span>В цьому тесті доступний калькулятор, який за необхідністю можна згорнути. Також ним можна користуватись безпосередньо через клавіатуру</p>
        						<?php }
        					 
        					 if ((isset($rem_atp) && $rem_atp < 1)) { ?>
        						<p class="alert alert-dark" ><span class="material-icons" style="color: black" >warning</span>Ви вичерпали всі свої спроби в цьому тесті</p>
        					<?php }
        					if (isset($_GET['noA'])) { 

        						$result = $pdo->prepare("SELECT member_name FROM test_members WHERE tm_id = :tm_id AND test_id = :test_id");
						        $params = [
						        "tm_id" => $_GET['noA'],
						        "test_id" => $_GET['testId'],
						        ];
						        $result->execute($params); 
						        $member = $result->fetchAll();
						        @$memberName = $member[0][0];


						        if ($memberName) {
						        
        						?>
        						<p class="alert alert-dark" ><span class="material-icons" style="color: black" >warning</span>Ви, як користувач з іменем "<?php echo $memberName; ?>", вичерпали всі свої спроби в цьому тесті</p>
        					<?php }}
        						if (isset($rem_atp) && $rem_atp == 1) { ?>
        						<p class="alert alert-dark" ><span class="material-icons" style="color: black" >warning</span>Залишилась остання спроба в цьому тесті!</p>
        		 			<?php }
        		 			?>
        		 			<div id="testStartNameNotTypedDiv" class="testSubmit" style="display:inline-flex">
        				<p id="testStartNoticeWhenNameNotTyped" hidden>Введіть ваше ім'я для продовження</p>
        				<input type="submit" id="testStartNameNotTypedSubmit" class="btn btn-dark " name="trueTestStart" value="Розпочати тест">
        				</div>
        		 		<?php } 
        		 			?>

        					 
        				</div>
        				

        			</form>
        			<p  ></p>
        		</div>
        	</div>


        	


	</div>

	<script type="text/javascript">
		
		var GoAs = document.getElementById('testStartGoAs');
		var testStartNameNotTypedSubmit = document.getElementById('testStartNameNotTypedSubmit');
		var testStartNWNNTyped = document.getElementById('testStartNoticeWhenNameNotTyped');
		var GoAsSendValue = document.getElementById('testStartGoAsValue');
		var GoAsInput = document.getElementById('GoAsInput');
		GoAsSendValue.value = GoAs.innerHTML;
		<?php if (isset($rem_atp) && $rem_atp < 1) { ?>
				testStartNameNotTypedSubmit.classList.add('disabled');
		<?php }?>
		if (GoAs.innerHTML == '') {
			GoAs.innerHTML= 'Введіть своє ім`я';
		console.log(GoAsSendValue.value);

		function IsNameTyped() {
			
			if (GoAs.innerHTML == 'Введіть своє ім`я' || GoAsSendValue.value == 'Введіть своє ім`я') {
				testStartNameNotTypedSubmit.classList.add('disabled');
				testStartNWNNTyped.hidden = false;
			} else {
				testStartNameNotTypedSubmit.classList.remove('disabled');
				testStartNWNNTyped.hidden = true;
			}

		}
		IsNameTyped();
		GoAs.onclick = function() {

			var inputGoAs = document.getElementById('testStartGoAsInput');
			if (inputGoAs == null) {
				var value = GoAs.innerHTML;
			GoAs.innerHTML = '<input type="text" id="testStartGoAsInput" class="createCourseInputText" placeholder="Ім`я">';
			var inputGoAs = document.getElementById('testStartGoAsInput');
			inputGoAs.value = value;
				
			}
		}
		window.onclick = function(event) {
			

			var inputGoAs = document.getElementById('testStartGoAsInput');
			if (inputGoAs != null) {
				if (event.target != GoAs) {
				
				console.log(inputGoAs);
					if (event.target != inputGoAs) {
						GoAs.innerHTML = inputGoAs.value;
						if (GoAs.innerHTML == '') {GoAs.innerHTML= 'Введіть своє ім`я';}
						GoAsSendValue.value = GoAs.innerHTML;
						console.log(GoAsSendValue.value);
						IsNameTyped();
					}
					
				}

			}
			
		}
		
	}
	</script>
	</content>
    </body>
</html>