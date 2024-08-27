<?php $examDuration = $post['Exam']['duration'];
$viewUrl = $this->Html->url(array('controller' => 'Exams', 'action' => 'submit', $examId, $examResultId));
$targetUrl = $this->Html->url(array('controller' => 'Ajaxcontents', 'action' => 'examwarning', $examResultId));
$finishUrl = $this->Html->url(array('controller' => 'Exams', 'action' => 'finish', $examId, 'warn'));
?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#exam-loading').show();
        $('#printajax').hide();
        $('.exam-panel').hide();
        navigation(1, 1);
    });
</script>
<div style="display: none;"><label id="totalQuestion"><?php echo $totalQuestion; ?></label></div>
<div style="display: none;"><label
        id="saveUrl"><?php echo $this->Html->url(array('controller' => 'Exams', 'action' => 'save', $examId)); ?></label>
</div>
<div style="display: none;"><label
        id="resetUrl"><?php echo $this->Html->url(array('controller' => 'Exams', 'action' => 'resetAnswer', $examId)); ?></label>
</div>
<div style="display: none;"><label
        id="reviewAnswerUrl"><?php echo $this->Html->url(array('controller' => 'Exams', 'action' => 'reviewAnswer', $examId)); ?></label>
</div>
<div style="display: none;"><label
        id="attemptTimeUrl"><?php echo $this->Html->url(array('controller' => 'Exams', 'action' => 'attemptTime', $examId)); ?></label>
</div>
<div style="display: none;"><label
        id="examUrl"><?php echo $this->Html->url(array('controller' => 'Exams', 'action' => 'start', $examId)); ?></label>
</div>
<div class="col-sm-offset-3 col-md-6" id="exam-loading"
     style="display: none;"><?php echo $this->Html->image('loading-lg.gif', array('class' => 'img-responsive')); ?></div>
<div id="printajax">
    <div class="col-md-12">
        <div class="col-md-9">
            <?php echo $this->Session->flash(); ?>
            <div class="exam-heading"><?php echo h($post['Exam']['name']); ?></div>
            <div class="exam-sections">
                <?php $temp = 0;
                $subjectHighlight = null;
                foreach ($userSectionQuestion as $subjectName => $quesArr):$temp++;
                    $subjectNameId = str_replace(" ", "", h($subjectName));
                    if ($temp == 1) {
                        $subjectHighlight = "exam-subjectHighlight";
                    } else {
                        $subjectHighlight = null;
                    } ?>
                    <div class="exam-dpopup"><a id="btnSection<?php echo $temp; ?>"
                                                class="exam-SubjectButton <?php echo $subjectHighlight; ?>"
                                                onclick="SectionButtonClick(<?php echo $temp; ?>)"><?php echo $subjectName; ?></a>
                    </div>
                <?php endforeach;
                unset($i);
                unset($value, $temp); ?>
            </div>
            <?php $mainSubjectName = null;
            $subTempId = 0;
            $subjectSection = null;
            foreach ($userExamQuestionArr as $k => $userExamQuestion):
                $quesNo = $userExamQuestion['ExamStat']['ques_no'];
                $tempSubjectName = $userExamQuestion['Subject']['subject_name'];
                if ($tempSubjectName != $mainSubjectName) {
                    $mainSubjectName = $tempSubjectName;
                    $subTempId = $subTempId + 1;
                    $subjectSection = "subject$subTempId";
                } else {
                    $subjectSection = "sub$subTempId";
                } ?>
                <div class="exam-panel <?php echo $subjectSection; ?>" id="quespanel<?php echo $quesNo; ?>">
                    <?php echo $this->Form->create('Exam', array('controller' => 'Exams', 'action' => "finish/$examId", 'name' => "post_req-$quesNo", 'id' => "post_req-$quesNo")); ?>
                    <div style="display: none;"><label
                            id="questype<?php echo $quesNo; ?>"><?php echo $userExamQuestion['Qtype']['type'];
                            ?></label>
                    </div>
                    <div class="exam-QuestionHeader">
                        <div class="exam-QuestionNo"><?php echo __('Question No.'); ?><span
                                id="exam-lblQuestionNo"><?php echo $userExamQuestion['ExamStat']['ques_no']; ?></span>
                        </div>
                        <div class="exam-Marks"><?php echo __('Right mark'); ?>:<span id="exam-lblRightMark"
                                                                                      style="color:Green;"><?php echo $userExamQuestion['ExamStat']['marks']; ?></span>
                            &nbsp; <?php echo __('Negative mark'); ?>:<span id="exam-lblNegativeMark"
                                                                            style="color:Red;"><?php echo $userExamQuestion['Question']['negative_marks']; ?></span>
                        </div>

                    </div>
                    <div class="exam-Question"><?php
                        $questionStyle = 'style="width: 100%;"'; ?>
                        <div class="exam-questionBox" id="exam-questionBox" <?php echo $questionStyle; ?>>
                            <table class="table">
                                <thead>
                                <tr>
                                    <td>
                                        <div class="">
                                            <div
                                                class="lang-1"><?php echo str_replace("<script", "", $userExamQuestion['Question']['question']); ?></div>
                                    </td>
                                </tr>
                                </thead>
                                <?php if (strlen($userExamQuestion['Question']['hint']) > 0) { ?>
                                    <tr>
                                        <td>
                                            <div class="mrg-left lang-1"><strong><?php echo __('Hint'); ?>
                                                    : </strong><?php echo str_replace("<script", "", $userExamQuestion['Question']['hint']); ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php if ($userExamQuestion['Qtype']['type'] == "M") {
                                    $options = array();
                                    $optColor1_1 = '<span>';
                                    $optColor1_2 = '<span>';
                                    $optColor1_3 = '<span>';
                                    $optColor1_4 = '<span>';
                                    $optColor1_5 = '<span>';
                                    $optColor1_6 = '<span>';
                                    $optColor2 = '</span>';
                                    if ($post['Exam']['instant_result'] == 1 && $userExamQuestion['ExamStat']['answered'] == 1) {
                                        if (strlen($userExamQuestion['Question']['answer']) > 1) {
                                            $selDanger = '<span class="text-danger"><b>';
                                            $selSuccess = '<span class="text-success"><b>';
                                            foreach (explode(",", $userExamQuestion['ExamStat']['option_selected']) as $value) {
                                                $opt = $value;
                                                $varName1 = 'optColor1' . '_' . $opt;
                                                $$varName1 = $selDanger;
                                            }
                                            unset($value);
                                            foreach (explode(",", $userExamQuestion['ExamStat']['correct_answer']) as $value) {
                                                $opt = $value;
                                                $varName1 = 'optColor1' . '_' . $opt;
                                                $$varName1 = $selSuccess;
                                            }
                                            unset($value);
                                        } else {
                                            $selDanger = '<span class="text-danger"><b>';
                                            $selSuccess = '<span class="text-success"><b>';
                                            $opt = $userExamQuestion['ExamStat']['option_selected'];
                                            $varName1 = 'optColor1' . '_' . $opt;
                                            $$varName1 = $selDanger;
                                            $opt = $userExamQuestion['ExamStat']['correct_answer'];
                                            $varName1 = 'optColor1' . '_' . $opt;
                                            $$varName1 = $selSuccess;
                                        }
                                    }
                                    $optionKeyArr = explode(",", $userExamQuestion['ExamStat']['options']);
                                    foreach ($optionKeyArr as $value) {
                                        $optKey = "option" . $value;
                                        $doptCol = 'optColor1' . '_' . $value;
                                        if (strlen($userExamQuestion['Question'][$optKey]) > 0)
                                            $options[$value] = $$doptCol . str_replace("<script", "", $userExamQuestion['Question'][$optKey]) . $optColor2;
                                    }
                                    unset($value);
                                    ?>
                                    <tr>
                                        <td>
                                            <?php if (strlen($userExamQuestion['Question']['answer']) > 1) {
                                                ?>
                                                <div class="checkbox"><?php
                                                $optionSelected = array();
                                                $optionSelected = explode(",", $userExamQuestion['ExamStat']['option_selected']);
                                                echo $this->Form->input('option_selected',
                                                    array('type' => 'select', 'multiple' => 'checkbox', 'label' => false,
                                                        'options' => $options,
                                                        'value' => $optionSelected,
                                                        'escape' => false));
                                                ?></div><?php
                                            } else {
                                                $optionSelected = $userExamQuestion['ExamStat']['option_selected'];
                                                echo $this->Form->input('option_selected',
                                                    array('type' => 'radio', 'label' => false, 'legend' => false, 'div' => false,
                                                        'options' => $options,
                                                        'value' => $optionSelected,
                                                        'before' => '<div class="radio"><label>', 'separator' => '</label></div><div class="radio"><label>',
                                                        'escape' => false));
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php if ($userExamQuestion['Qtype']['type'] == "T") {
                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo $this->Form->radio('true_false', array('True' => __('True'), 'False' => __('False')), array('value' => $userExamQuestion['ExamStat']['true_false'], 'hiddenField' => false, 'separator' => '</div><div class="radio-inline">', 'legend' => false, 'label' => array('class' => 'radio-inline'))); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php if ($userExamQuestion['Qtype']['type'] == "F") {
                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo $this->Form->input('fill_blank', array('value' => $userExamQuestion['ExamStat']['fill_blank'], 'label' => false, 'autocomplete' => 'off')); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php if ($userExamQuestion['Qtype']['type'] == "S") {
                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo $this->Form->input('answer', array('type' => 'textarea', 'value' => $userExamQuestion['ExamStat']['answer'], 'label' => false, 'class' => 'form-control', 'rows' => '7')); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                    <?php echo $this->Form->end(); ?>

                    <div class="exam-QuestionFooter">
                        <div class="exam-MarkForReview">
                            <a class="exam-SecondaryButton exam-review"
                               onclick="markForReview(<?php echo $quesNo; ?>);"><?php echo __('Mark for Review &amp; Next'); ?></a>
                            <a class="exam-SecondaryButton exam-cresponse"
                               onclick="resetAnswer(<?php echo $quesNo; ?>);"><?php echo __('Clear Response') ?></a>
                        </div>
                        <a class="exam-DefaultButton exam-savenext"
                           onclick="callUserAnswerSaveNext(<?php echo $quesNo; ?>);"><?php echo __('Save &amp; Next'); ?></a>
                    </div>
                </div>
            <?php endforeach;
            unset($k, $userExamQuestion, $mainSubjectName, $subTempId, $subjectSection); ?>
        </div>
        <div class="col-md-3">
            <div id="timer">
                <div id="maincount"></div>
            </div>
            <div class="exam-student-name"><?php echo $userValue['Student']['name']; ?></div>
            <div id="exam-divQuestionPalleteTitle">
                <div id="exam-divQuestionPalleteSection"></div>
                <b><?php echo __('Question Palette'); ?>:</b>
            </div>
            <div id="exam-divQuestionPallete">
                <div class="exam-PalleteButtons">
                    <?php foreach ($userSectionQuestion as $subjectName => $quesArr):
                        $subjectNameId = str_replace(" ", "", h($subjectName));
                        foreach ($quesArr as $value):$quesNo = $value['ExamStat']['ques_no'];
                            if ($quesNo == 1) $btnType = "btn-default"; else$btnType = ""; ?>
                            <div
                                class="col-md-2 col-sm-2 col-xs-2 mrg-1"><?php echo $this->Form->button($quesNo, array('type' => 'button', 'onclick' => "navigation($quesNo)", 'id' => "navbtn$quesNo", 'class' => "exam-ButtonNotVisited")); ?></div>
                        <?php endforeach;
                        unset($quesArr); ?>
                    <?php endforeach;
                    unset($i);
                    unset($value); ?>
                </div>
            </div>
            <div class="exam-divLegend">
                <b><?php echo __('Legend'); ?>:</b>
                <br>
                <table>
                    <tbody>
                    <tr>
                        <td>
                            <button class="exam-ButtonAnswered" onclick="FilterPaletteButtons('ans'); return false;"
                                    id="countAnswered">0
                            </button>
                            <?php echo __('Answered'); ?>
                        </td>
                        <td>
                            <button class="exam-ButtonNotAnswered"
                                    onclick="FilterPaletteButtons('notans'); return false;" id="countNotAnswered">0
                            </button>
                            <?php echo __('Not Answered'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button class="exam-ButtonNotAnsweredMarked"
                                    onclick="FilterPaletteButtons('notansmarked'); return false;"
                                    id="countNotAnswerMarked">0
                            </button>
                            <?php echo __('Marked'); ?>
                        </td>
                        <td>
                            <button class="exam-ButtonNotVisited"
                                    onclick="FilterPaletteButtons('notvisit'); return false;"
                                    id="countNotVisited"><?php echo $totalQuestion; ?></button>
                            <?php echo __('Not Visited'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <button class="exam-ButtonAnsweredMarked"
                                    onclick="FilterPaletteButtons('ansmarked'); return false;" id="countAnsMarked">0
                            </button>
                            <?php echo __('Answered &amp; Marked for Review'); ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="exam-divQuestionFilter">
                    &nbsp;&nbsp;<?php echo __('Filter'); ?>:
                    <select id="exam-ddlQStatus">
                        <option selected="selected" value="all"><?php echo __('All'); ?></option>
                        <option value="notvisit"><?php echo __('Not Visited'); ?></option>
                        <option value="notans"><?php echo __('Not Answered'); ?></option>
                        <option value="notansmarked"><?php echo __('Marked for review'); ?></option>
                        <option value="ansmarked"><?php echo __('Answered &amp; marked for review'); ?></option>
                        <option value="ans"><?php echo __('Answered'); ?></option>

                    </select>
                </div>
            </div>
            <div class="exam-divRightpanelBottom">
                <center>

                    <button id="btnQuestionPaper"
                            class="exam-RightPanelButton"><?php echo __('Question Paper'); ?></button>

                    <button id="btnInstructions"
                            class="exam-RightPanelButton"><?php echo __('Instructions'); ?></button>
                    <button id="btnProfile" class="exam-RightPanelButton"><?php echo __('Profile'); ?></button>
                    <button id="submit-btn" class="exam-submitButton"
                            onclick="show_modal('<?php echo $viewUrl; ?>')"><?php echo __('Submit'); ?></button>
                </center>
            </div>
        </div>
    </div>
    <?php $endTime = $this->Time->format('M d, Y H:i:s', $this->Time->fromString($examResult['ExamResult']['start_time']) + ($post['Exam']['duration'] * 60));
    $startTime = $this->Time->format('M d, Y H:i:s', $this->Time->fromString($examResult['ExamResult']['start_time']));
    $expiryUrl = $this->Html->url(array('controller' => 'Exams', 'action' => "finish/$examId"));
    $serverTimeUrl = $this->Html->url(array('crm' => false, 'controller' => 'ServerTimes', 'action' => 'index'));
    ?>
    <script type="text/javascript">
        <?php if($examDuration > 0){ ?>
        $(document).ready(function () {
            liftoffTime = new Date("<?php echo $endTime;?>");
            $("#maincount").countdown({
                until: liftoffTime,
                format: 'HMS',
                serverSync: serverTime,
                alwaysExpire: true,
                onExpiry: liftOff
            });
            function serverTime() {
                var time = null;
                $.ajax({
                    url: "<?php echo $serverTimeUrl;?>",
                    async: false, dataType: 'text',
                    success: function (text) {
                        time = new Date(text);
                    }, error: function (http, message, exc) {
                        time = new Date();
                    }
                });
                return time;
            }

            function liftOff() {
                window.location = '<?php echo $this->Html->url(array('controller' => 'Exams', 'action' => 'finish', $post['Exam']['id'], 'null'));?>/' + currentQuesNo();
            }
        });
        <?php } else{ ?>
        $(document).ready(function () {
            startTime = new Date("<?php echo $startTime;?>");
            $('#maincount').countdown({since: startTime, format: 'HMS', serverSync: serverTime});
            function serverTime() {
                var time = null;
                $.ajax({
                    url: "<?php echo $serverTimeUrl;?>",
                    async: false, dataType: 'text',
                    success: function (text) {
                        time = new Date(text);
                    }, error: function (http, message, exc) {
                        time = new Date();
                    }
                });
                return time;
            }
        });
        <?php }?>
        <?php if($post['Exam']['browser_tolrance'] == 1){?>
        $(window).on("blur", function (e) {
            $.ajax({
                method: "GET",
                cache: false,
                url: '<?php echo $targetUrl;?>'
            })
                .done(function (response) {
                    if (response == "Yes") {
                        window.location = '<?php echo $this->Html->url(array('controller' => 'Exams', 'action' => 'finish', $post['Exam']['id'], 'null'));?>/' + currentQuesNo();
                    }
                    else {
                        $('#myModal').modal({
                            backdrop: 'static',
                            keyboard: false
                        })
                    }
                });
        });
        <?php }?>
    </script>
    <style type="text/css">
        .modal-backdrop {
            background-color: #ff0000;
        }

        .modal-backdrop.in {
            opacity: .8;
        }
    </style>
    <?php echo $this->Form->create('Exam', array('controller' => 'Exams', 'action' => "lang/$examId", 'id' => 'langfrm'));
    echo $this->Form->hidden('lang', array('id' => 'lang'));
    echo $this->Form->end(null); ?>
    <div class="modal fade" id="targetModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-content">
        </div>
    </div>
    <div class="modal fade" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i
                            class="fa fa-exclamation-triangle"></i>&nbsp;<?php echo __('Navigated Away'); ?></h4>
                </div>
                <div class="modal-body">
                    <p>
                    <blockquote><?php echo $userValue['Student']['name']; ?>
                        , <?php echo __('you had navigated away from the test window. This will be reported to Moderator'); ?></blockquote>
                    </p>
                    <p>
                    <blockquote><span
                            class="text-danger"><?php echo __('Do not repeat this behaviour'); ?></span> <?php echo __('Otherwise you may get disqualified'); ?>
                    </blockquote>
                    </p>
                    <div class="text-center">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal"><?php echo __('Continue'); ?></button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
<div id="instructionBlock" class="exam-overlay" style="padding: 0 20px;">
    <div class="exam-LangaugeSelection">
        <?php //echo ('View In');?>:
        <?php //echo$this->Form->select('lang',$langArr,array('empty'=>false,'onchange'=>'changeLang(this.value)','value'=>$lang));?>
    </div>
    <br unselectable="on">
    <div class="exam-InstructionHeader" unselectable="on">
        <span id="exam-lblInstructionHeader" unselectable="on"></span>
    </div>
    <div id="exam-divInstructions">
        <table border="0" width="100%" cellspacing="0" cellpadding="0" height="50">
            <tbody>
            <tr>
                <td align="left" valign="top">
                    <h2><?php echo __('Instructions For') . ' ' . $post['Exam']['name']; ?></h2>
                    <p><?php echo str_replace("<script", "", $post['Exam']['instruction']); ?></p></td>
            </tr>
            </tbody>
        </table>
    </div>
    <center>
        <div class="exam-divbackButton">
            <button id="instructionBack" class="exam-RightPanelButton"><?php echo __('Back'); ?></button>
        </div>
    </center>
</div>
<div id="userInfo" class="popup exam-overlay">
    <center>
        <div id="userInfoInner">
            <table style="width:100%;">
                <tbody>
                <tr>
                    <td class="tdhead"><?php echo __('Name'); ?>:&nbsp;</td>
                    <td class="tdvalue"><?php echo $userValue['Student']['name']; ?></td>
                </tr>
                <tr>
                    <td class="tdhead"><?php echo __('Email'); ?>:</td>
                    <td class="tdvalue"><?php echo $userValue['Student']['email']; ?></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="exam-divbackButton">
            <button id="userinfoBack" class="exam-RightPanelButton"><?php echo __('Back'); ?></button>
        </div>
    </center>
</div>
<div id="QuestionPaperBlock" class="popup exam-overlay">
    <div class="exam-QuestionPaperHeader">
        <center><h2><?php echo __('Question Paper'); ?></h2></center>
    </div>
    <div id="QuestionPaperData">
        <table class="exam-QPQuestion">
            <?php
            foreach ($userExamQuestionArr as $k => $userExamQuestion):?>
                <tr>
                    <td class="exam-tdnum"><?php echo __('Q') . $userExamQuestion['ExamStat']['ques_no']; ?>.</td>
                    <td>
                        <table border="0" width="100%" cellspacing="0" cellpadding="0" height="50">
                            <tbody>
                            <tr>
                                <td align="left"
                                    valign="top"><?php echo $userExamQuestion['Question']['question']; ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="exam-tdnum"></td>
                    <td>
                        <table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tbody>
                            <tr>
                                <td width="40" align="center">
                                    <?php if ($userExamQuestion['Qtype']['type'] == "M") {
                                    $options = array();
                                    $optColor1_1 = '<span>';
                                    $optColor1_2 = '<span>';
                                    $optColor1_3 = '<span>';
                                    $optColor1_4 = '<span>';
                                    $optColor1_5 = '<span>';
                                    $optColor1_6 = '<span>';
                                    $optColor2 = '</span>';
                                    if ($post['Exam']['instant_result'] == 1 && $userExamQuestion['ExamStat']['answered'] == 1) {
                                        if (strlen($userExamQuestion['Question']['answer']) > 1) {
                                            $selDanger = '<span class="text-danger"><b>';
                                            $selSuccess = '<span class="text-success"><b>';
                                            foreach (explode(",", $userExamQuestion['ExamStat']['option_selected']) as $value) {
                                                $opt = $value;
                                                $varName1 = 'optColor1' . '_' . $opt;
                                                $$varName1 = $selDanger;
                                            }
                                            unset($value);
                                            foreach (explode(",", $userExamQuestion['ExamStat']['correct_answer']) as $value) {
                                                $opt = $value;
                                                $varName1 = 'optColor1' . '_' . $opt;
                                                $$varName1 = $selSuccess;
                                            }
                                            unset($value);
                                        } else {
                                            $selDanger = '<span class="text-danger"><b>';
                                            $selSuccess = '<span class="text-success"><b>';
                                            $opt = $userExamQuestion['ExamStat']['option_selected'];
                                            $varName1 = 'optColor1' . '_' . $opt;
                                            $$varName1 = $selDanger;
                                            $opt = $userExamQuestion['ExamStat']['correct_answer'];
                                            $varName1 = 'optColor1' . '_' . $opt;
                                            $$varName1 = $selSuccess;
                                        }
                                    }
                                    $optionKeyArr = explode(",", $userExamQuestion['ExamStat']['options']);
                                    foreach ($optionKeyArr as $value) {
                                        $optKey = "option" . $value;
                                        $doptCol = 'optColor1' . '_' . $value;
                                        if (strlen($userExamQuestion['Question'][$optKey]) > 0)
                                            $options[$value] = $$doptCol . str_replace("<script", "", $userExamQuestion['Question'][$optKey]) . $optColor2;
                                    }
                                    unset($value);
                                    ?>
                            <tr>
                                <td>
                                    <?php if (strlen($userExamQuestion['Question']['answer']) > 1) {
                                        ?>
                                        <div class="checkbox"><?php
                                        $optionSelected = array();
                                        $optionSelected = explode(",", $userExamQuestion['ExamStat']['option_selected']);
                                        echo $this->Form->input('option_selected',
                                            array('type' => 'select', 'multiple' => 'checkbox', 'label' => false,
                                                'options' => $options,
                                                'value' => $optionSelected,
                                                'escape' => false));
                                        ?></div><?php
                                    } else {
                                        $optionSelected = $userExamQuestion['ExamStat']['option_selected'];
                                        echo $this->Form->input('option_selected',
                                            array('type' => 'radio', 'label' => false, 'legend' => false, 'div' => false,
                                                'options' => $options,
                                                'value' => $optionSelected,
                                                'before' => '<div class="radio"><label>', 'separator' => '</label></div><div class="radio"><label>',
                                                'escape' => false));
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php } ?>
                            <?php if ($userExamQuestion['Qtype']['type'] == "T") {
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $this->Form->radio('true_false', array('True' => __('True'), 'False' => __('False')), array('value' => $userExamQuestion['ExamStat']['true_false'], 'hiddenField' => false, 'separator' => '</div><div class="radio-inline">', 'legend' => false, 'label' => array('class' => 'radio-inline'))); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if ($userExamQuestion['Qtype']['type'] == "F") {
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $this->Form->input('fill_blank', array('value' => $userExamQuestion['ExamStat']['fill_blank'], 'label' => false, 'autocomplete' => 'off')); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if ($userExamQuestion['Qtype']['type'] == "S") {
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $this->Form->input('answer', array('type' => 'textarea', 'value' => $userExamQuestion['ExamStat']['answer'], 'label' => false, 'class' => 'form-control', 'rows' => '7')); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
            <?php endforeach;
            unset($k, $userExamQuestion, $passageTempId, $passageMainId); ?>
        </table>
        <center>
            <div class="exam-divbackButton">
                <button id="questionPaperBack" class="exam-RightPanelButton"><?php echo __('Back'); ?></button>
            </div>
        </center>
    </div>
</div>
<script type="text/javascript">
    $(window).load(function () {
        $('#exam-loading').hide();
        $('#printajax').show();
    });
    $(document).ready(function () {
        $('.lang-1').show();
        $('.examLang').val(1);
        <?php foreach ($userExamQuestionArr as $k => $userExamQuestion):
        $quesNo=$userExamQuestion['ExamStat']['ques_no'];
        if($userExamQuestion['ExamStat']['answered']==1 && $userExamQuestion['ExamStat']['review']==0){
            ?>ChangeClassOFPaletteButton('ans', <?php echo $quesNo;?>);
        <?php }
         elseif($userExamQuestion['ExamStat']['opened']==1 && $userExamQuestion['ExamStat']['answered']==0 && $userExamQuestion['ExamStat']['review']==0){
            ?>ChangeClassOFPaletteButton('notans', <?php echo $quesNo;?>);
        <?php }
         elseif($userExamQuestion['ExamStat']['answered']==0 && $userExamQuestion['ExamStat']['review']==1){
            ?>ChangeClassOFPaletteButton('notansmarked', <?php echo $quesNo;?>);
        <?php }
        elseif($userExamQuestion['ExamStat']['answered']==1 && $userExamQuestion['ExamStat']['review']==1){
            ?>ChangeClassOFPaletteButton('ansmarked', <?php echo $quesNo;?>);
        <?php }
        endforeach;unset($userExamQuestion);?>
        getExamCounter();
    });
</script>