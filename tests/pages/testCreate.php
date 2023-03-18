
    <form id="phpF" method="GET" action="<?php echo $_SERVER['SCRIPT_NAME'];?>">
        <div class="testCreateSettings rounded" >
            <h4>Налаштування тесту</h4>
            <div class="testCreateSettingsContent">
                <h5 align="center">Базові налаштування</h5>
                
                
                <input type="number" class="testTimerInput" id='qAttemptsAmount' name="testAttemptNumber" min="1" value='3' max="10" required><label for="qAttemptsAmount">Кількість спроб</label><br>
                <h6 align="center"><b>Опис</b></h6>
                <textarea id="testCreateDescription" placeholder="Опис (не обов'язково)" ></textarea>
                
                <h6 align="center" id="createTestATSelectDivH"><b>Тип показу відповідей після проходження тесту</b></h6>
                    <div class="createTestATSelectDiv" >
                        <select class="createCourseFormSelect" id="qShowAnswersSelect" required>
                            <option value="ALL" >Всі</option>
                            <option value="ALLCORRECT">Тільки вірні</option>
                            <option value="ALLCHECK" selected>Всі що вибрані</option>
                            <option value="INCORRECT">Тільки невірні (Що вибрані)</option>
                            <option value="CORRECT">Тільки вірні (Що вибрані)</option>
                            <option value="NONE">Нічого</option>
                        </select>
                    </div>
                <hr align="center">
                <h5 align="center">Обмеження</h5>
                <div class="testTimerSettingSwitch" > 
                    <p class="form-check form-switch" >
                        <input type="checkbox" class="form-check-input" name="testMLCSettingCheckbox" id="testTimerSettingCheckbox">
                        <label for="testTimerSettingCheckbox" title="Обмежити час проходження тесту" class="form-check-label">Обмежити час проходження тесту
                        <br>
                        <span class="testCreateHint disabled" style="font-size: 12px;">Обмежує час проходження тесту. Після того як зазначений час буде вичерпано - тест буде провалено!</span>
                        </label>
                        
                    </p>
                    <div id="testTimerSetting" hidden>
                        <p>
                    <input type="text" id="testTimeMins" class="testTimerInput" placeholder="000" name="testTime" autocomplete="off"><span> хв </span><input type="text" id="testTimeSecs" class="testTimerInput" placeholder="00" name="testTime" autocomplete="off"> <span> с </span>
                </p>
                    </div>
                </div>
                <div class="testMLCSettingSwitch" > 
                    <p class="form-check form-switch" >
                        <input type="checkbox" class="form-check-input" name="testMLCSettingCheckbox" id="testMLCSettingCheckbox">
                        <label for="testMLCSettingCheckbox" title="Обмежити максимально допустиму кількість покидання сторінки" class="form-check-label">Обмежити покидання сторінки
                            <br>
                            <span class="testCreateHint disabled" style="font-size: 12px;">Обмежує кількість випадків, коли сторінка стає неактивною під час проходження тесту при її згортанні.</span>
                        </label>
                        
                    </p>
                    <div id="testMaxLeaveCountSetting" hidden>

                        <p id="MLCPRange">
                            <span>3</span><input type="range" min="3" max="30" value="5" class="form-range" id="testMaxLeaveCount" name="testMaxLeaveCount"><span>30</span><br><span id="currentMaxLeaveCount">0</span>
                        </p>
                        <hr align="center">
                        <h6 style="font-weight: bold;">Дії при перевищенні ліміту</h6>
                        <div class="form-check">
                            
                            <input class="form-check-input"  type="radio" name="f" id="f1"><label class="form-check-label" for="f1">Провал тесту</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input"  type="radio" name="f" id="f2">
                            <label class="form-check-label" for="f2">Зниження оцінки у відсотках (одноразово)</label> 
                            <div id="f2ValueP" class="fValueP" hidden>
                                <p>
                                    <input id="f2Value" class="testMinusMarkInput" type="text" name="fixedMinusMark" autocomplete="off" >
                                    <span> %</span>
                                </p>
                                <p class="disabled"><i>Зниження певного відсотка від максимально можливої кількості балів <b>один раз</b> після перевищення ліміту</i></p>
                            </div>
                        </div> 
                        <div class="form-check">
                            <input class="form-check-input"  type="radio" name="f" id="f3">
                            <label class="form-check-label" for="f3">Зниження оцінки у відсотках (за кожне +1 перевищення)</label> 
                            <div id="f3ValueP" class="fValueP" hidden>
                                <p>
                                    <input id="f3Value" class="testMinusMarkInput" type="text" name="fixedMinusMark" autocomplete="off">
                                    <span> %</span>
                                </p>
                                <p class="disabled"><i>Зниження певного відсотка від максимально можливої кількості балів <b>кожний раз</b> після перевищення ліміту. Якщо загальна кількість знятих відсотків буде більше 100, то тест буде провалено</i></p>
                            </div>
                        </div> 
                        <div class="form-check">
                            <input class="form-check-input" checked  type="radio" name="f" id="f4"><label class="form-check-label" for="f4">Нічого (попередження)</label>
                            <div id="f4ValueP" class="fValueP" hidden>

                            </div>
                        </div>
                        <hr align="center">
                        <h6 style="font-weight: bold;">Додаткові налаштування</h6>
                        <p class="form-check form-switch MLCBlurP" >
                            <input type="checkbox" class="form-check-input" name="testMLCSettingCheckbox" id="testMLCBlurPageCheckbox">
                            <label for="testMLCBlurPageCheckbox" style="" title="Розмиває всю сторінку якщо вона стає неактивною" class="form-check-label">Розмиття сторінки коли вона неактивна
                                <br>
                                <span class="disabled" style="font-size: 12px;">Розмиває сторінку коли користувач згортає сторінку з проходженням тесту</span>
                            </label>
                            
                        </p>
                    </div>
                </div>
                <hr align="center">
                <h5 align="center">Віджети</h5>
                <div class="testTimerSettingSwitch" > 
                    <p class="form-check form-switch" >
                        <input type="checkbox" class="form-check-input" name="testWidgetCalcCheckbox" id="testWidgetCalcCheckbox">
                        <label for="testWidgetCalcCheckbox" title="Ввімкнути наявність калькулятора" class="form-check-label">Калькулятор
                        <br>
                        <span class="testCreateHint disabled" style="font-size: 12px;">На сторінці проходження тесту з'являється калькулятор, який за необхідністю можна згорнути. Також ним можна користуватись безпосередньо через клавіатуру</span>
                        </label>
                        
                    </p>
                </div>
            </div>
        </div>
        <div class="testCreateMainContent form-check rounded">
        <input type="text" id="phpS" name="phpS" value="0" hidden>
        <input type="text" id="qcount" name="qcount" value="0" hidden>
        <input type="text" id="testName" class="testCreateTestName rounded" placeholder="Новий тест" name="testName" required autocomplete="off">
        


        <div id="questions">
            
        </div>
        <p id="addquest" class='courseButton testCreateAddQuestionButton' ><span class='material-icons courseBSpan'>add</span> Додати питання</p>
        </div>
        <br>
        <input id="subTestCreate" class="btn btn-dark" type="submit" name="subTestCreate" value="Створити тест" hidden>
    </form>
<span id="phpScontent" ></span>
<p> </p><br><br>


<script type="text/javascript">
    var testName = document.getElementById('testName');
    var phpS = document.getElementById('phpS');
    var qcount = document.getElementById('qcount');
    var phpF = document.getElementById('phpF');
    //var selection = document.getElementById('selection');
    var phpScontent = document.getElementById('phpScontent');
    var testTimeMins = document.getElementById('testTimeMins');
    var testTimeSecs = document.getElementById('testTimeSecs');
    var testMLCSettingCheckbox = document.getElementById('testMLCSettingCheckbox');
    var testMaxLeaveCountSetting = document.getElementById('testMaxLeaveCountSetting');

    var testTimerSettingCheckbox = document.getElementById('testTimerSettingCheckbox');
    var testTimerSetting = document.getElementById('testTimerSetting');

    var testMaxLeaveCount = document.getElementById('testMaxLeaveCount');
    var currentMaxLeaveCount = document.getElementById('currentMaxLeaveCount');
    var qShowAnswersSelect = document.getElementById('qShowAnswersSelect');
    var qAttemptsAmount = document.getElementById('qAttemptsAmount');
    var testCreateDescription = document.getElementById('testCreateDescription');
    var testMLCBlurPageCheckbox = document.getElementById('testMLCBlurPageCheckbox');
    var testWidgetCalcCheckbox = document.getElementById('testWidgetCalcCheckbox');



    var q_count = 0;
    var a_count = [];
        a_count[q_count] = [q_count];
    var questions = document.getElementById('questions');
    var addquest = document.getElementById('addquest');
        var leaveCountValue = 'none';

        function fDetect(f1,f2,f3,f4) {
            if (f1.checked == true) return 'fail';
            if (f2.checked == true) return 'lowerMarkOnce';
            if (f3.checked == true) return 'lowerMarkEveryTime';
            if (f4.checked == true) return 'warning';
        }
        function ifMLCSCchecked() {
            if (testMLCSettingCheckbox.checked == true) return true;
            else return false;
        }
        function ifTimerSCchecked() {
            if (testTimerSettingCheckbox.checked == true) return true;
            else return false;
        }

    phpF.oninput = function() {
        qSASSelected = qShowAnswersSelect.options[qShowAnswersSelect.selectedIndex].value;

        if (testTimerSettingCheckbox.checked == true) {
        testTimerSetting.hidden = false;
    } else {
        testTimerSetting.hidden = true;
    }



    if (testMLCSettingCheckbox.checked == true) {
        testMaxLeaveCountSetting.hidden = false;
        leaveCountValue = testMaxLeaveCount.value;

            var f1 = document.getElementById('f1'); // провал
            var f2 = document.getElementById('f2'); // одноразовий мінус бал
            var f3 = document.getElementById('f3'); // багаторазовий мінус бал
            var f4 = document.getElementById('f4'); // попередження

            var f = fDetect(f1,f2,f3,f4);

                if(f =='lowerMarkOnce') {
                    var MLCNeedValue = true;
                    var MLCValueInput = document.getElementById('f2Value');
                    var MLCValueP = document.getElementById('f2ValueP');
                    MLCValueP.hidden = false;

                        if(MLCValueInput.value > 100) {
                            MLCValueInput.value = 100;
                        }
                        if(MLCValueInput.value < 0) {
                            MLCValueInput.value = 0;
                        }
                        if(isNaN(MLCValueInput.value)) {
                            MLCValueInput.value = '';
                        }

                    var f_value = MLCValueInput.value;
                } else {
                    var MLCValueP = document.getElementById('f2ValueP');
                    MLCValueP.hidden = true;
                }

                if(f =='lowerMarkEveryTime') {
                    var MLCNeedValue = true;
                    var MLCValueInput = document.getElementById('f3Value');
                    var MLCValueP = document.getElementById('f3ValueP');
                    MLCValueP.hidden = false;

                    if(MLCValueInput.value > 100) {
                            MLCValueInput.value = 100;
                        }
                        if(MLCValueInput.value < 0) {
                            MLCValueInput.value = 0;
                        }
                        if(isNaN(MLCValueInput.value)) {
                            MLCValueInput.value = '';
                        }

                    var f_value = MLCValueInput.value;
                } else {
                    var MLCValueP = document.getElementById('f3ValueP');
                    MLCValueP.hidden = true;
                }


    } else {
        testMaxLeaveCountSetting.hidden = true;
        leaveCountValue = 'none';
    }

    phpS.value =                "sconfig<br>"+
                                "filename = " + testName.value +"<br>"+
                                "date_of_creating = <?php echo date('Y.m.d');?><br>";
    if(ifTimerSCchecked()) {
        phpS.value +=           "time = "+ testTimeMins.value +":"+ testTimeSecs.value +"<br>";
    }
    if (testCreateDescription.value != '') {
        phpS.value +=           "description = "+testCreateDescription.value+"<br>";
    }
    phpS.value +=               "attempts = " + qAttemptsAmount.value +"<br>"            
    phpS.value +=               "max_leave_count = "+ leaveCountValue +"<br>";
    if(ifMLCSCchecked()) {
            phpS.value +=       "mlc_action = " + f + "<br>";
        if(MLCNeedValue) {
            phpS.value +=       "mlc_action_value = " + f_value + "<br>";
        }
        if (testMLCBlurPageCheckbox.checked == true) {
            phpS.value +=       "mlc_blur = true<br>";
        } else {
            phpS.value +=       "mlc_blur = false<br>";
        }
    }
    if (testWidgetCalcCheckbox.checked == true) {
            phpS.value +=       "widget_calc = true<br>";
        } else {
            phpS.value +=       "widget_calc = false<br>";
        }
    phpS.value +=   "show_ans_mode = " + qSASSelected + "<br>";       
    phpS.value +=   "econfig<br>"+
                    "<br>";         
    qcount.value = q_count;
    currentMaxLeaveCount.innerHTML = testMaxLeaveCount.value;
    phpS.value += "squestions<br>";
    

    for (var i = 1; i <= q_count; i++) {
        var qTitle = document.getElementById('qTitle' + i);
        var qType = document.getElementById('selectQ' + i + 'Type');
        qTypeSelected = qType.options[qType.selectedIndex].value;
        
        phpS.value += "[Q]"+qTitle.value+"<br>";

        phpS.value +="[sQCFG]<br>";

        phpS.value +="type = " + qTypeSelected + "<br>";

            var checkAnswer_count = 0;

            for (var b = 1; b < a_count[i].length; b++) {
                var rightAnswer = document.getElementById('q'+ i + 'ra'+b);
                rightAnswer.setAttribute('type', qTypeSelected);
                if(rightAnswer.checked == true) checkAnswer_count++;

                if (qTypeSelected == 'checkbox') {
                    rightAnswer.removeAttribute('required');
                }
                else {
                    rightAnswer.setAttribute('required', true);
                }

            }

            if (qTypeSelected == 'checkbox') {

                phpS.value +="ra = ";

                var checkAnswer_typed_count = 0;

                for (var b = 1; b <= a_count[i].length-1; b++) {
                var rightAnswer = document.getElementById('q'+ i + 'ra'+b);

                    if(rightAnswer.checked == true) checkAnswer_typed_count++;

                    if(checkAnswer_typed_count == checkAnswer_count ) {
                        if(rightAnswer.checked == true) phpS.value +="a" + b + " ";
                    }
                    else {
                        if(rightAnswer.checked == true) phpS.value +="a" + b + "-";
                        
                    }
                }

                phpS.value +="<br>";

            } 
            else {
                for (var b = 1; b <= a_count[i].length-1; b++) {
                    var rightAnswer = document.getElementById('q'+ i + 'ra' + b);
                    if(rightAnswer.checked == true) phpS.value +="ra = a" + b + "<br>";
                }

            }

        phpS.value +="[eQCFG]<br>";

        //console.log("Кількість відповідей "+i+" в питанні: "+(a_count[i].length-1));

        for (var b = 1; b <= a_count[i].length-1; b++) {
            var answ = document.getElementById('q'+ i + 'aTitle'+b);
            phpS.value += "[A]" + answ.value + "<br>";
        }
    }
    

    phpS.value += "equestions";

    // phpScontent.innerHTML = phpS.value;


    }

    testTimeMins.oninput = function() {
        if (testTimeMins.value.length > 1) testTimeSecs.focus();
        if (testTimeMins.value.length > 3) testTimeMins.value = '';
        if (isNaN(testTimeMins.value)) testTimeMins.value = '';
        if (Number(testTimeMins.value) > 999) testTimeMins.value = 999;
    }
    testTimeSecs.oninput = function() {
        if (testTimeSecs.value.length > 1) testTimeMins.focus();
        if (testTimeSecs.value.length > 2) testTimeSecs.value = '';
        if (isNaN(testTimeSecs.value)) testTimeSecs.value = '';
        if (Number(testTimeSecs.value) > 59) testTimeSecs.value = 59;
    }

    createQuestion();



    addquest.onclick = function(){createQuestion()};

    function createQuestion() {
        q_count++;
        a_count[q_count] = [q_count];
        var quest = "<b id=bq"+q_count+">"+q_count+ ". </b>" +"<input type='text' class='testCreateQuestionTitle' id='qTitle" + q_count + "' name='q" + q_count + "title' placeholder='Питання' value='Питання' autocomplete='off'> <select name='category_id' title='Тип питання' id='selectQ" + q_count + "Type' class='createCourseFormSelect testCreateQuestionType' required><option value='radio' selected>◉ Одна відповідь</option><option value='checkbox'>☑ Багато відповідей</option></select> <span id='qs"+ q_count +"' onclick='delq(`q" + q_count + "`)' class='material-icons createTestDeleteButton cTDBQ rounded' title='Видалити питання'>close</span><p id='qp"+ q_count +"' onclick='addAns(`q"+ q_count +"`)' class='courseButton testCreateAddAnswerButton'><span class='material-icons courseBSpan'>add</span> Додати відповідь</p>";
        var qu = document.createElement('div');
        qu.id = "q"+ q_count;
        qu.classList.add('testCreateQuestion');
        qu.classList.add('rounded');
        qu.setAttribute('data-q', q_count);
        qu.innerHTML = quest;
        questions.appendChild(qu);

        addAns('q'+q_count);
    }
    function delq(del) {

        document.getElementById(del).remove();
        qID = del.substr(1);

        for (var i = q_count; i > 0; i--) {
            var qr = document.getElementById('q'+ i);

            if (qr == null) {
                
                for (var b = i+1; b <= q_count; b++) {
                    qr = document.getElementById('q'+ b);
                    qp = document.getElementById('qp'+ b);
                    qs = document.getElementById('qs'+ b);
                    bq = document.getElementById('bq'+ b);
                    qName = document.getElementById('qTitle'+ b);
                    qType = document.getElementById('selectQ' + b + 'Type');
                    qr.id = "q"+ (b-1);
                    qp.id = "qp"+ (b-1);
                    qs.id = "qs"+ (b-1);
                    bq.id = "bq"+ (b-1);
                    bq.innerHTML = (b-1)+'. ';
                    qName.id = "qTitle"+ (b-1);
                    qType.id = "selectQ" + (b-1) + "Type";
                    qp.setAttribute('onclick',"addAns('q"+ (b-1) +"')");
                    qs.setAttribute('onclick',"delq('q"+ (b-1) +"')");
                    qName.setAttribute('name',"q"+ (b-1) +"title");
                    qr.setAttribute('data-q', (b-1));


                    answerCount = a_count[b].length-1;

                    for (var c = 1; c <= answerCount; c++) {
                    var ar = document.getElementById('q'+ b+'a'+c);
                    ara = document.getElementById('q'+ b+'ra'+c);
                    aTitle = document.getElementById('q'+ b+'aTitle'+c);
                    as = document.getElementById('as'+ c);
                    ar.setAttribute('data-q', (b-1));
                    ar.id = 'q'+ (b-1)+'a'+c;
                    ara.id = 'q'+ (b-1)+'ra'+c;
                    aTitle.id = 'q'+ (b-1)+'aTitle'+c;
                    as.id = "as"+ c;

                    aTitle.setAttribute('placeholder', 'Варіант '+ c )
                    as.setAttribute('onclick',"dela('q"+ (b-1)+"a"+c+"')");

                }





                    
                }
                break;
            }
        }
        a_count.splice(qID, 1);
        q_count--;

   
    }
    function dela(del) {
        delans = document.getElementById(del);
        delans.remove();
        qID = delans.getAttribute('data-q');
        answerCount = a_count[qID].length-1;


        for (var i = answerCount; i > 0; i--) {
            var ar = document.getElementById('q'+ qID+'a'+i);
            if (ar == null) {
                for (var b = i+1; b <= answerCount; b++) {
                    var ar = document.getElementById('q'+ qID+'a'+b);
                    ara = document.getElementById('q'+ qID+'ra'+b);
                    aTitle = document.getElementById('q'+ qID+'aTitle'+b);
                    as = document.getElementById('as'+ b);
                    ar.id = 'q'+ qID+'a'+(b-1);
                    ara.id = 'q'+ qID+'ra'+(b-1);
                    aTitle.id = 'q'+ qID+'aTitle'+(b-1);
                    as.id = "as"+ (b-1);

                    aTitle.setAttribute('placeholder', 'Варіант '+ (b-1) )
                    as.setAttribute('onclick',"dela('q"+ qID+"a"+(b-1)+"')");

                }
                break;
            }
        }

        a_count[qID].splice(a_count[qID].length-1, 1);
        


    }
    function addAns(addans) {
    
        var addans = document.getElementById(addans);
        // console.log(addans.getAttribute('data-q'));
        var qID = Number(addans.getAttribute('data-q'));
        var aID = a_count[qID].length;
        var qp = document.getElementById('qp' + qID);

        var qType = document.getElementById('selectQ' + qID + 'Type');
        qTypeSelected = qType.options[qType.selectedIndex].value;

        var isRequired = (qTypeSelected == 'checkbox') ? '' : 'required';



        a_count[qID][aID]= aID;
        var ans = "<input type='"+qTypeSelected+"' class='form-check-input testCreateAnswerCheck' " + isRequired + " id='" +addans.id +"ra" + aID + "' name='ra"+ qID+"'><input type='text' class='testCreateAnswerTitle rounded' id='" +addans.id +"aTitle" + aID + "' name='ans' placeholder='Варіант " + aID +"' value='Варіант " + aID +"' autocomplete='off'><span class='material-icons createTestDeleteButton rounded' id='as" + aID +"' onclick='dela(`" +addans.id +"a" + aID + "`)' title='Видалити відповідь'>close</span><br>";
        var an = document.createElement('div');
        an.id = addans.id +"a"+ aID;
        an.classList.add('testCreateAnswer');
        an.classList.add('rounded');
        an.setAttribute('data-q', qID);
        an.innerHTML = ans;
        qp.before(an);
    }
</script>
