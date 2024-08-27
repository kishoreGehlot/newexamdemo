<?php

class LeaderboardsController extends AppController
{
    public $components = array('RequestHandler');

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function crm_index()
    {
        $this->authenticate();
        //////////////////// CUSTOM QUERY START ///////////////////////
        $scoreboard = $this->Leaderboard->query("SELECT `points`,`student_id`,`exam_given`,`name`,`rank` FROM (SELECT ROUND(SUM(`percent`)/((SELECT COUNT( `id` ) FROM `exam_results` WHERE `student_id` = `ExamResult`.`student_id` AND `finalized_time` IS NOT NULL)),2) AS `points` ,`student_id`,(SELECT COUNT( `id` ) FROM `exam_results` WHERE `student_id` = `ExamResult`.`student_id` AND `finalized_time` IS NOT NULL) AS `exam_given`, `Student`.`name`,FIND_IN_SET((SELECT ROUND(SUM(`percent`)/((SELECT COUNT( `id` ) FROM `exam_results` WHERE `student_id` = `ExamResult`.`student_id` AND `finalized_time` IS NOT NULL)),2)),(SELECT GROUP_CONCAT(cast(`total` as char)) FROM (SELECT DISTINCT(ROUND(SUM(`percent`)/(SELECT COUNT( `id` ) FROM `exam_results` WHERE `student_id` = `ExamResult`.`student_id` AND `finalized_time` IS NOT NULL),2)) `total` FROM `exam_results` AS `ExamResult` GROUP BY `student_id` ORDER BY 1 DESC) as avg_percent)) AS `rank` FROM `exam_results` AS `ExamResult` INNER JOIN `students` AS `Student` ON `ExamResult`.`student_id` = `Student`.`id` WHERE `finalized_time` IS NOT NULL GROUP BY `student_id`) `Selection` ORDER BY `points` DESC LIMIT 100");
        //////////////////// CUSTOM QUERY END ///////////////////////
        $this->set('scoreboard', $scoreboard);
    }

    public function rest_index()
    {
        if ($this->authenticateRest($this->request->query)) {
            //////////////////// CUSTOM QUERY START ///////////////////////
            $response = $this->Leaderboard->query("SELECT `points`,`student_id`,`exam_given`,`name`,`rank` FROM (SELECT ROUND(SUM(`percent`)/((SELECT COUNT( `id` ) FROM `exam_results` WHERE `student_id` = `ExamResult`.`student_id` AND `finalized_time` IS NOT NULL)),2) AS `points` ,`student_id`,(SELECT COUNT( `id` ) FROM `exam_results` WHERE `student_id` = `ExamResult`.`student_id` AND `finalized_time` IS NOT NULL) AS `exam_given`, `Student`.`name`,FIND_IN_SET((SELECT ROUND(SUM(`percent`)/((SELECT COUNT( `id` ) FROM `exam_results` WHERE `student_id` = `ExamResult`.`student_id` AND `finalized_time` IS NOT NULL)),2)),(SELECT GROUP_CONCAT(cast(`total` as char)) FROM (SELECT DISTINCT(ROUND(SUM(`percent`)/(SELECT COUNT( `id` ) FROM `exam_results` WHERE `student_id` = `ExamResult`.`student_id` AND `finalized_time` IS NOT NULL),2)) `total` FROM `exam_results` AS `ExamResult` GROUP BY `student_id` ORDER BY 1 DESC) as avg_percent)) AS `rank` FROM `exam_results` AS `ExamResult` INNER JOIN `students` AS `Student` ON `ExamResult`.`student_id` = `Student`.`id` WHERE `finalized_time` IS NOT NULL GROUP BY `student_id`) `Selection` ORDER BY `points` DESC LIMIT 100");
            //////////////////// CUSTOM QUERY END ///////////////////////
            $status = true;
            $message = __('Leader Board data fetch successfully.');
        } else {
            $status = false;
            $message = ('Invalid Token');
            $response = (object)array();
        }
        $this->set(compact('status', 'message', 'response'));
        $this->set('_serialize', array('status', 'message', 'response'));
    }
}