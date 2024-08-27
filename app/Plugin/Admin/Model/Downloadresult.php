<?php
class Downloadresult extends AppModel
{
  public $validationDomain = 'validation';
  public $useTable="exam_results";
  public $belongsTo=array('Student'=>array('className'=>'students'),'Exam'=>array('className'=>'exams'));
  public $hasMany=array('ExamStat');
}
?>