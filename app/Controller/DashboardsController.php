<?php
App::uses('CakeTime', 'Utility');
App::uses('Paypal', 'Paypal.Lib');

class DashboardsController extends AppController
{
    public $components = array('HighCharts.HighCharts', 'RequestHandler');
    public $currentDateTime, $studentId;

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->studentId = $this->userValue['Student']['id'];
        $this->limit = 5;
        /* Paypal Pending Payment*/
        $this->loadModel('PaypalConfig');
        $paySetting = $this->PaypalConfig->findById('1');
        if ($paySetting['PaypalConfig']['sandbox_mode'] == 1)
            $sandboxMode = true;
        else
            $sandboxMode = false;
        $this->Paypal = new Paypal(array(
            'sandboxMode' => $sandboxMode,
            'nvpUsername' => $paySetting['PaypalConfig']['username'],
            'nvpPassword' => $paySetting['PaypalConfig']['password'],
            'nvpSignature' => $paySetting['PaypalConfig']['signature']
        ));
        /* Paypal Pending Payment*/
    }

    public function crm_index($type = "crm", $publicKey = "0", $privateKey = "0")
    {
        if ($type == "crm") {
            $this->authenticate();
        } else {
            if ($this->authenticateRest(array('public_key' => $publicKey, 'private_key' => $privateKey))) {
                $this->studentId = $this->restStudentId($this->request->query);
            } else {
                echo __('Invalid Post');
                die();
            }
        }
        $this->set('type',$type);
        /* Paypal Pending Payment*/
        $this->loadModel('Payment');
        $paymentArr = $this->Payment->findByStudentIdAndStatus($this->studentId, 'Pending');
        if ($paymentArr) {
            $responseArr = $this->Paypal->getPaymentDetails($paymentArr['Payment']['transaction_id']);
            if ($responseArr == "Completed") {
                $amount = $paymentArr['Payment']['amount'];
                $description = $paymentArr['Payment']['remarks'];
                $recordArr = array('student_id' => $this->studentId, 'status' => 'Approved');
                $this->Payment->save($recordArr);
                $this->CustomFunction->WalletInsert($this->studentId, $amount, "Added", $this->currentDateTime, "PG", $description);
            } else {
                $this->Session->setFlash("Previous payment pending", 'flash', array('alert' => 'danger'));
            }
        }
        /* Paypal Pending Payment*/
        $this->loadModel('Exam');
        /* Start Remaining Exam Cheking*/
        $this->loadModel('ExamResult');
        $testId = null;
        $remExamName = null;
        $remExam = $this->ExamResult->find('first', array('joins' => array(array('table' => 'exams', 'type' => 'INNER', 'alias' => 'Exam', 'conditions' => array('ExamResult.exam_id=Exam.id'))),
            'fields' => array('Exam.id', 'Exam.name'),
            'conditions' => array('student_id' => $this->studentId, 'end_time' => null)));
        if ($remExam) {
            $testId = $remExam['Exam']['id'];
            $remExamName = $remExam['Exam']['name'];
        }
        $this->set('testId', $testId);
        $this->set('remExamName', $remExamName);
        /* End Remaining Exam Cheking*/
        $todayExam = $this->Exam->getUserExam("today", $this->studentId, $this->currentDateTime, $this->limit);
        $this->set('todayExam', $todayExam);

        $totalExamGiven = $this->Dashboard->find('count', array('conditions' => array('Dashboard.student_id' => $this->studentId)));
        $failedExam = $this->Dashboard->find('count', array('conditions' => array('Dashboard.student_id' => $this->studentId, 'Dashboard.result' => 'Fail')));
        $userTotalAbsent = $this->Dashboard->userTotalAbsent($this->studentId);
        if ($userTotalAbsent < 0)
            $userTotalAbsent = 0;
        $bestScoreArr = $this->Dashboard->userBestExam($this->studentId);
        $bestScore = "";
        $bestScoreDate = "";
        if (isset($bestScoreArr['Exam']['name'])) {
            $bestScore = $bestScoreArr['Exam']['name'];
            $bestScoreDate = CakeTime::format($this->sysDay . $this->dateSep . $this->sysMonth . $this->dateSep . $this->sysYear . $this->dateGap . $this->sysHour . $this->timeSep . $this->sysMin . $this->dateGap . $this->sysMer, $bestScoreArr['ExamResult']['start_time']);
        }
        $this->set('limit', $this->limit);
        $this->set('totalExamGiven', $totalExamGiven);
        $this->set('failedExam', $failedExam);
        $this->set('userTotalAbsent', $userTotalAbsent);
        $this->set('bestScore', $bestScore);
        $this->set('bestScoreDate', $bestScoreDate);

        $performanceChartData = array();
        $currentMonth = CakeTime::format('m', time());
        for ($i = 1; $i <= 12; $i++) {
            if ($i > $currentMonth)
                break;
            $examData = $this->Dashboard->performanceCount($this->studentId, $i);
            $performanceChartData[] = (float)$examData;
        }
        $tooltipFormatFunction = "function() { return '<b>'+ this.series.name +'</b><br/>'+ this.x +': '+ this.y +'%';}";
        $chartName = "My Chartdl";
        $mychart = $this->HighCharts->create($chartName, 'spline');
        $this->HighCharts->setChartParams(
            $chartName,
            array(
                'renderTo' => "mywrapperdl",  // div to display chart inside
                'titleAlign' => 'center',
                'creditsEnabled' => FALSE,
                'xAxisLabelsEnabled' => TRUE,
                'xAxisCategories' => array(__('Jan'), __('Feb'), __('Mar'), __('Apr'), __('May'), __('Jun'), __('Jul'), __('Aug'), __('Sep'), __('Oct'), __('Nov'), __('Dec')),
                'yAxisTitleText' => __('Percentage'),
                'tooltipEnabled' => TRUE,
                'tooltipFormatter' => $tooltipFormatFunction,
                'enableAutoStep' => FALSE,
                'plotOptionsShowInLegend' => TRUE,
                'yAxisMax' => 100,
            )
        );
        $series = $this->HighCharts->addChartSeries();
        $series->addName(__('Month'))->addData($performanceChartData);
        $mychart->addSeries($series);

        $this->loadModel('ExamResult');
        $examResultArr = $this->ExamResult->find('all', array('fields' => array('Exam.name', 'ExamResult.percent'),
            'joins' => array(array('table' => 'exams', 'alias' => 'Exam', 'type' => 'INNER', 'conditions' => array('ExamResult.exam_id=Exam.id'))),
            'conditions' => array('ExamResult.student_id' => $this->studentId),
            'order' => array('ExamResult.id' => 'desc'),
            'limit' => 10));
        $this->ExamResult->virtualFields = array('total_percent' => 'SUM(ExamResult.percent)');
        $totalPercentArr = $this->ExamResult->find('first', array('fields' => array('total_percent'), 'conditions' => array('ExamResult.student_id' => $this->studentId)));
        $this->ExamResult->virtualFields = array();
        $totalExamAttempt = $this->ExamResult->find('count', array('conditions' => array('ExamResult.student_id' => $this->studentId)));
        $totalPercent = $totalPercentArr['ExamResult']['total_percent'];
        if ($totalExamAttempt > 0)
            $averagePercent = round($totalPercent / $totalExamAttempt, 2);
        else
            $averagePercent = 0;
        $performanceChartData = array();
        $xAxisCategories = array();
        foreach ($examResultArr as $post) {
            $xAxisCategories[] = array($post['Exam']['name']);
            $performanceChartData[] = array((float)$post['ExamResult']['percent']);
        }
        $tooltipFormatFunction = "function() { return ''+ this.x +': '+ this.y +'%';}";
        $chartName = "My Chartd2";
        $mychart = $this->HighCharts->create($chartName, 'column');
        $this->HighCharts->setChartParams(
            $chartName,
            array(
                'renderTo' => "mywrapperd2",  // div to display chart inside
                'titleAlign' => 'center',
                'creditsEnabled' => FALSE,
                'xAxisLabelsEnabled' => TRUE,
                'xAxisCategories' => $xAxisCategories,
                'yAxisTitleText' => __('Percentage'),
                'tooltipEnabled' => TRUE,
                'tooltipFormatter' => $tooltipFormatFunction,
                'enableAutoStep' => FALSE,
                'plotOptionsShowInLegend' => TRUE,
                'yAxisMax' => 100,
            )
        );
        $series = $this->HighCharts->addChartSeries();
        $series->addName(__('Exams'))->addData($performanceChartData);
        $mychart->addSeries($series);

        $rank = 0;
        $rankPost = $this->ExamResult->query("SELECT `percent`,`student_id`, FIND_IN_SET((SELECT ROUND(SUM(`percent`)/((SELECT COUNT( `id` ) FROM `exam_results` WHERE `student_id` = `ExamResult`.`student_id` AND `finalized_time` IS NOT NULL)),2)),(SELECT GROUP_CONCAT(cast(`total` as char)) FROM (SELECT DISTINCT(ROUND(SUM(`percent`)/(SELECT COUNT( `id` ) FROM `exam_results` WHERE `student_id` = `ExamResult`.`student_id` AND `finalized_time` IS NOT NULL),2)) `total` FROM `exam_results` AS `ExamResult` GROUP BY `student_id` ORDER BY 1 DESC) as avg_percent)) AS `rank` FROM `exam_results` AS `ExamResult`  WHERE `student_id`=$this->studentId HAVING `rank` IS NOT NULL ORDER BY `percent` DESC LIMIT 1");
        if ($rankPost)
            $rank = $rankPost[0][0]['rank'];
        $this->set('averagePercent', $averagePercent);
        $this->set('rank', $rank);
    }

    public function rest_index()
    {
        $this->autoRender = false;
        if ($this->authenticateRest($this->request->query)) {
            $this->studentId = $this->restStudentId($this->request->query);
            try {
                $message = null;
                $this->crm_index('rest', $this->request->query['public_key'], $this->request->query['private_key']);
                $this->layout = 'rest';
                $this->render('crm_index', null);
            } catch (Exception $e) {
                $message = $e->getMessage();
            }
        } else {
            $message = ('Invalid Token');
        }
        echo $message;
    }
    public function rest_exam()
    {
        if ($this->authenticateRest($this->request->query)) {
            $this->studentId = $this->restStudentId($this->request->query);
            $this->loadModel('Exam');
            $response = $this->Exam->getUserExam("today", $this->studentId, $this->currentDateTime, $this->limit);
            $status = true;
            $message = __('Exam fetch successfully');
        } else {
            $status = false;
            $message = ('Invalid Token');
            $response = (object)array();
        }
        $this->set(compact('status', 'message', 'response'));
        $this->set('_serialize', array('status', 'message', 'response'));
    }
    public function rest_balance(){
        if ($this->authenticateRest($this->request->query)) {
            $this->studentId = $this->restStudentId($this->request->query);
            $balance=$this->CustomFunction->WalletBalance($this->studentId);
            $status = true;
            $message = __('Balance fetch successfully');
        } else {
            $status = false;
            $message = ('Invalid Token');
            $balance=null;
        }
        $this->set(compact('status', 'message', 'balance'));
        $this->set('_serialize', array('status', 'message', 'balance'));
    }
    public function rest_mail(){
        if ($this->authenticateRest($this->request->query)) {
            $this->studentId = $this->restStudentId($this->request->query);
            $email= $this->Mail->find('count', array('conditions' => array('email' => $this->userValue['Student']['email'], 'status <>' => 'Trash', 'type' => 'Unread', 'mail_type' => 'To')));
            $status = true;
            $message = __('Email fetch successfully');
        } else {
            $status = false;
            $message = ('Invalid Token');
            $email=null;
        }
        $this->set(compact('status', 'message', 'email'));
        $this->set('_serialize', array('status', 'message', 'email'));
    }
    public function rest_remainingExam(){
        if ($this->authenticateRest($this->request->query)) {
            $this->studentId = $this->restStudentId($this->request->query);
        /* Start Remaining Exam Cheking*/
        $this->loadModel('ExamResult');
        $testId = null;
        $remExamName = null;
        $remExam = $this->ExamResult->find('first', array('joins' => array(array('table' => 'exams', 'type' => 'INNER', 'alias' => 'Exam', 'conditions' => array('ExamResult.exam_id=Exam.id'))),
            'fields' => array('Exam.id', 'Exam.name'),
            'conditions' => array('student_id' => $this->studentId, 'end_time' => null)));
        if ($remExam) {
            $testId = $remExam['Exam']['id'];
            $remExamName = $remExam['Exam']['name'];
            $status = true;
            $message = __('Click here to complete %s',$remExamName);
        }
        else{
            $status = false;
            $message = __('No remaining exam.');
        }
        /* End Remaining Exam Cheking*/
        } else {
            $status = false;
            $message = ('Invalid Token');
            $testId=null;
            $remExamName=null;
        }
        $this->set(compact('status', 'message', 'testId','remExamName'));
        $this->set('_serialize', array('status', 'message', 'testId','remExamName'));
    }
}
