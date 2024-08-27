<?php
App::uses('CakeNumber', 'Utility');
class GroupperformancesController extends AppController
{
    public $components = array('HighCharts.HighCharts', 'RequestHandler');
    public $studentId;
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->studentId=$this->userValue['Student']['id'];
    }
    public function crm_index($type = "crm", $publicKey = "0", $privateKey = "0")
    {
        if ($type == "crm") {
            $this->authenticate();
        } else {
            if ($this->authenticateRest(array('public_key'=>$publicKey,'private_key'=>$privateKey))) {
                $this->studentId = $this->restStudentId($this->request->query);
            } else {
                echo __('Invalid Post');
                die();
            }
        }
        $testName=$this->Groupperformance->userGroupTestName($this->studentId);
        $performanceData=$this->Groupperformance->userPerformance($this->studentId);
        $myPerformanceChartData=$performanceData[0];
        $GroupPerformanceChartData=$performanceData[1];
        $xAxisCategories=array();
        if(is_array($testName))
        {
            foreach($testName as $textValue)
            {
                $xAxisCategories[]=$textValue['Exam']['name'];
            }
        }
        $performanceChartData1=array(75,35);
        $chartName = "My Chartdl";
        $tooltipFormatFunction ="function() { return '<b>'+ this.series.name +'</b><br/>'+ this.x +': '+ this.y +'% Marks';}";
        $mychart = $this->HighCharts->create($chartName,'line');
        $this->HighCharts->setChartParams(
                                          $chartName,
                                          array(
                                                'renderTo'=> "mywrapperdl",  // div to display chart inside
                                                'titleAlign'=> 'center',
                                                'creditsEnabled'=> FALSE,
                                                'xAxisLabelsEnabled'=> TRUE,
                                                'xAxisCategories'=> $xAxisCategories,
                                                'yAxisTitleText'=>__('Percentage'),
                                                'legendEnabled'=> TRUE,
                                                'tooltipEnabled'=> TRUE,
                                                'tooltipFormatter'=> $tooltipFormatFunction,
                                                'enableAutoStep'=> FALSE,
                                                'plotOptionsLineDataLabelsFormat'=> TRUE,                                                
                                                )
                                          );
        $series = $this->HighCharts->addChartSeries();
        $series1 = $this->HighCharts->addChartSeries();
        $series1->addName(__('My Performance'))->addData($myPerformanceChartData);
        $series->addName(__('My Group performance'))->addData($GroupPerformanceChartData);
        $mychart->addSeries($series);
        $mychart->addSeries($series1);        
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
}