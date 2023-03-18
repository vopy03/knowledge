<?php
require_once "../../dbconnect.php";

	if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}

/*
echo "<pre>";
var_dump($_FILES['file']);
echo "</pre>";
*/
	$fileformat = substr($_FILES['file']['name'], strlen($_FILES['file']['name'])-4,strlen($_FILES['file']['name']));
	
	if ($fileformat == 'kntf') {

		$testName = substr($_FILES['file']['name'], 0,strlen($_FILES['file']['name'])-5);
		$uploaddir = "../../data/tests/".$_SESSION['login']."/".$testName."/";
		$updir = "../data/tests/".$_SESSION['login']."/".$testName."/";
		$upfile = $updir . basename($_FILES['file']['name']);
		mkdir($uploaddir. "test_results", 0777, true);
		$uploadfile = $uploaddir . basename($_FILES['file']['name']);

		if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
			//echo "Тест завантажено\n";


			$fp = fopen($uploadfile, "r"); // Открываем файл в режиме чтения
			if ($fp) {
			    $test['q_number'] = 0;
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
			            $test['config']["$config_name"] = trim($config_value);
			        }
			        if (str_contains($str, 'sconfig')) {
			            $config_part = true;
			        }
			        } else { 
			            if (str_contains($str, 'equestions')) {
			            $q_part = false;
			            }
			            if($q_part) {

			                if (str_contains($str, '[Q]')) {
			                $q_title = substr($str , 3);
			                if ($test['q_number'] > 0) {
			                }
			                $test['q_number']++;
			                }
			   
			            }

			            if (str_contains($str, 'squestions')) {
			            $q_part = true;
			            }
			        }

			    }

		} else echo "Ошибка при открытии файла";
		fclose($fp);


			$sql = "INSERT INTO `tests` (`name`, `creator_id`, `date_of_creating`, `num_of_questions`, `filepath`) VALUES (:name, :creator_id, :date_of_creating, :num_of_questions, :filepath)";

		    $result = $pdo->prepare($sql);
		    $params = [
		    "name" => $test['config']['filename'],
		    "creator_id" => $_SESSION['id'],
		    "date_of_creating" => $test['config']['date_of_creating'],
		    "num_of_questions" => $test['q_number'],
		    "filepath" => $upfile,
		    ]; 
		    $result->execute($params);

		} else {
		    echo "Тест відсутній!\n";
		    rmdir($uploaddir. "test_results");
		    rmdir($uploaddir);
		}
	} else { ?>

		<script>
		alert('Невірний формат файлу! Виберіть файл з закінченням .kntf');
		</script>
	<?php }

 ?>