<?php
App::uses('CakeTime', 'Utility');
App::uses('CakeEmail', 'Network/Email');

class ExamsController extends AppController
{
	public $helpers = array('Html');
	public $components = array('CustomFunction', 'RequestHandler');

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->studentId = $this->userValue['Student']['id'];
	}

	public function crm_index()
	{
		$this->authenticate();
		$todayExam = $this->Exam->getUserExam("today", $this->studentId, $this->currentDateTime);
		$this->set('todayExam', $todayExam);
	}

	public function rest_index()
	{
		if ($this->authenticateRest($this->request->query)) {
			$this->studentId = $this->restStudentId($this->request->query);
			$response = $this->Exam->getUserExam("today", $this->studentId, $this->currentDateTime);
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

	public function crm_purchased()
	{
		$this->authenticate();
		$purchasedExam = $this->Exam->getPurchasedExam("today", $this->studentId, $this->currentDateTime);
		$this->set('purchasedExam', $purchasedExam);
	}

	public function rest_purchased()
	{
		if ($this->authenticateRest($this->request->query)) {
			$this->studentId = $this->restStudentId($this->request->query);
			$response = $this->Exam->getPurchasedExam("today", $this->studentId, $this->currentDateTime);
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

	public function crm_upcoming()
	{
		$this->authenticate();
		$upcomingExam = $this->Exam->getUserExam("upcoming", $this->studentId, $this->currentDateTime);
		$this->set('upcomingExam', $upcomingExam);
	}

	public function rest_upcoming()
	{
		if ($this->authenticateRest($this->request->query)) {
			$this->studentId = $this->restStudentId($this->request->query);
			$response = $this->Exam->getUserExam("upcoming", $this->studentId, $this->currentDateTime);
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

	public function crm_expired()
	{
		$this->authenticate();
		$expiredExam = $this->Exam->getPurchasedExam("expired", $this->studentId, $this->currentDateTime);
		$this->set('expiredExam', $expiredExam);
	}

	public function rest_expired()
	{
		if ($this->authenticateRest($this->request->query)) {
			$this->studentId = $this->restStudentId($this->request->query);
			$response = $this->Exam->getPurchasedExam("expired", $this->studentId, $this->currentDateTime);
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

	public function crm_view($id, $showType)
	{
		$this->authenticate();
		$this->layout = null;
		$this->loadModel('ExamQuestion');
		$this->loadModel('ExamGroup');
		if (!$id) {
			$this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
			$this->redirect(array('action' => 'index'));
		}
		$checkPost = $this->Exam->checkPost($id, $this->studentId);
		if ($checkPost == 0) {
			$this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
			$this->redirect(array('action' => 'error'));
		}
		$post = $this->Exam->findByIdAndStatus($id, 'Active');
		if (!$post) {
			$this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
			$this->redirect(array('action' => 'error'));
		}
		$examCount = $this->Exam->find('count', array('joins' => array(array('table' => 'exam_maxquestions', 'type' => 'INNER', 'alias' => 'ExamMaxquestion', 'conditions' => array('Exam.id=ExamMaxquestion.exam_id'))),
			'conditions' => array('Exam.id' => $id)));
		if ($post['Exam']['type'] == "Exam") {
			$subjectDetailArr = $this->Exam->getSubject($id);
			foreach ($subjectDetailArr as $value) {
				$subjectId = $value['Subject']['id'];
				$subjectName = $value['Subject']['subject_name'];
				$totalQuestionArr = $this->Exam->subjectWiseQuestion($id, $subjectId, 'Exam');
				$subjectDetail[$subjectName] = $totalQuestionArr;
			}
			$totalMarks = $this->Exam->totalMarks($id);
		} else {
			$subjectDetailArr = $this->Exam->getPrepSubject($id);
			foreach ($subjectDetailArr as $value) {
				$subjectId = $value['Subject']['id'];
				$subjectName = $value['Subject']['subject_name'];
				$totalQuestionArr = $this->Exam->subjectWiseQuestion($id, $subjectId);
				$subjectDetail[$subjectName] = $totalQuestionArr;
			}
			$totalMarks = 0;
		}
		$this->set('post', $post);
		$this->set('subjectDetail', $subjectDetail);
		$this->set('totalMarks', $totalMarks);
		$this->set('examCount', $examCount);
		$this->set('showType', $showType);
		$this->set('id', $id);
	}

	public function rest_view()
	{
		$status = false;
		$message = __('Invalid Post');
		$response = (object)array();
		$subjectDetail = array();
		$totalMarks = null;
		$examCount = null;
		if ($this->authenticateRest($this->request->query)) {
			$this->studentId = $this->restStudentId($this->request->query);
			$this->loadModel('ExamQuestion');
			$this->loadModel('ExamGroup');
			if (isset($this->request->query['id'])) {
				$id = $this->request->query['id'];
			} else {
				$id = 0;
			}
			$checkPost = $this->Exam->checkPost($id, $this->studentId);
			$post = $this->Exam->findByIdAndStatus($id, 'Active');
			if ($checkPost && $post) {
				$examCount = $this->Exam->find('count', array('joins' => array(array('table' => 'exam_maxquestions', 'type' => 'INNER', 'alias' => 'ExamMaxquestion', 'conditions' => array('Exam.id=ExamMaxquestion.exam_id'))),
					'conditions' => array('Exam.id' => $id)));
				if ($post['Exam']['type'] == "Exam") {
					$subjectDetailArr = $this->Exam->getSubject($id);
					foreach ($subjectDetailArr as $value) {
						$subjectId = $value['Subject']['id'];
						$subjectName = $value['Subject']['subject_name'];
						$totalQuestionArr = $this->Exam->subjectWiseQuestion($id, $subjectId, 'Exam');
						$subjectDetail[$subjectName] = $totalQuestionArr;
					}
					$totalMarks = $this->Exam->totalMarks($id);
				} else {
					$subjectDetailArr = $this->Exam->getPrepSubject($id);
					foreach ($subjectDetailArr as $value) {
						$subjectId = $value['Subject']['id'];
						$subjectName = $value['Subject']['subject_name'];
						$totalQuestionArr = $this->Exam->subjectWiseQuestion($id, $subjectId);
						$subjectDetail[$subjectName] = $totalQuestionArr;
					}
					$totalMarks = 0;
				}
				$status = true;
				$message = __('Exam fetch successfully.');
				$this->set('response', $post);
			} else {
				$message = __('Invalid Post');
				$subjectDetail = (object)array();
			}
		} else {
			$message = ('Invalid Token');
			$subjectDetail = (object)array();
		}
		$this->set(compact('status', 'message', 'subjectDetail', 'totalMarks', 'examCount', 'id'));
		$this->set('_serialize', array('status', 'message', 'response', 'subjectDetail', 'totalMarks', 'examCount', 'id'));
	}

	public function crm_guidelines($id = null)
	{
		$this->authenticate();
		$this->layout = 'exam';
		if (!$id) {
			$this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
			$this->redirect(array('action' => 'index'));
		}
		$checkPost = $this->Exam->checkPost($id, $this->studentId);
		if ($checkPost == 0) {
			$this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
			$this->redirect(array('action' => 'error'));
		}
		$post = $this->Exam->findByIdAndStatus($id, 'Active');
		if (!$post) {
			$this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
			$this->redirect(array('action' => 'error'));
		}
		$this->set('post', $post);
	}

	public function crm_instruction($id)
	{
		$this->authenticate();
		$this->layout = 'exam';
		$this->loadModel('ExamQuestion');
		if (!$id) {
			$this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
			$this->redirect(array('action' => 'index'));
		}
		$checkPost = $this->Exam->checkPost($id, $this->studentId);
		if ($checkPost == 0) {
			$this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
			$this->redirect(array('action' => 'error'));
		}
		$this->loadModel('Exam');
		$post = $this->Exam->findByIdAndStatus($id, 'Active');
		$ispaid = $this->checkPaidStatus($id, $this->studentId);
		$this->set('post', $post);
		$this->set('ispaid', $ispaid);
	}

	public function rest_instruction()
	{
		$status = false;
		$message = __('Invalid Post');
		$response = (object)array();
		$isPaid = true;
		if ($this->authenticateRest($this->request->query)) {
			$this->studentId = $this->restStudentId($this->request->query);
			$this->loadModel('ExamQuestion');
			if (isset($this->request->query['id'])) {
				$id = $this->request->query['id'];
			} else {
				$id = 0;
			}
			$checkPost = $this->Exam->checkPost($id, $this->studentId);
			if ($id && $checkPost) {
				$this->loadModel('Exam');
				$response = $this->Exam->findByIdAndStatus($id, 'Active');
				$isPaid = $this->checkPaidStatus($id, $this->studentId);
				$status = true;
				if ($isPaid == false) {
					$message = __('This Exam is paid. Amount should be deducted on your wallet automatically. After starting the exam timer will not stop. Do you want to pay & start?');
				} else {
					$message = __('Exam fetch successfully.');
				}
			} else {
				$message = __('Invalid Post');
			}
		} else {
			$message = ('Invalid Token');
		}
		$this->set(compact('status', 'message', 'response', 'isPaid'));
		$this->set('_serialize', array('status', 'message', 'response', 'isPaid'));
	}

	public function crm_error()
	{
		$this->authenticate();
		$this->layout = null;
	}

	public function crm_start($id = null, $quesNo = null, $currQuesNo = 1)
	{
		$this->authenticate();
		$this->layout = 'examstart';
		if ($id == null)
			$id = 0;
		$checkPost = $this->Exam->checkPost($id, $this->studentId);
		if ($checkPost == 0) {
			$this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
			$this->redirect(array('action' => 'index'));
		}
		$this->loadModel('ExamResult');
		$this->loadModel('ExamOrder');
		$post = $this->Exam->findById($id);
		$currentExamResult = $this->ExamResult->find('count', array('conditions' => array('student_id' => $this->studentId, 'end_time' => null)));
		if ($currentExamResult == 0) {
			$paidexam = $post['Exam']['paid_exam'];
			$expiryExam = $post['Exam']['expiry'];
			$totalExam = $this->ExamResult->find('count', array('conditions' => array('exam_id' => $id, 'student_id' => $this->studentId)));
			$attempt_count = $post['Exam']['attempt_count'];
			if ($attempt_count <= $totalExam && $attempt_count > 0) {
				$this->Session->setFlash(__('You have attempted maximum exam.'), 'flash', array('alert' => 'danger'));
				$this->redirect(array('action' => 'index'));
			}
			if ($paidexam == 1) {
				if (!$this->checkPaidStatus($id, $this->studentId)) {
					$this->redirect(array('action' => 'paid', $id, 'P'));
				}
			}
			if ($expiryExam != null) {
				if (!$this->checkExpiryStatus($id, $this->studentId, $expiryExam)) {
					$this->Session->setFlash(__('You have attempted expiry exam.'), 'flash', array('alert' => 'danger'));
					$this->redirect(array('action' => 'index'));
				}
			}
			$this->Exam->userExamInsert($id, $post['Exam']['ques_random'], $post['Exam']['type'], $post['Exam']['option_shuffle'], $this->studentId, $this->currentDateTime);
		}
		$examWise = $this->ExamResult->find('first', array('conditions' => array('student_id' => $this->studentId, 'end_time' => null)));
		if ($quesNo == null) {
			if ($currentExamResult == 1) {
				$this->loadModel('ExamStat');
				$examStat = $this->ExamStat->find('first', array('fields' => array('ques_no'), 'conditions' => array('exam_result_id' => $examWise['ExamResult']['id'], 'attempt_time' => NULL)));
				if ($examStat && $examStat['ExamStat']['ques_no'] != 1)
					$quesNo = $examStat['ExamStat']['ques_no'] - 1;
				else
					$quesNo = 1;
			} else
				$quesNo = 1;
		}
		if ($currentExamResult == 1) {
			$examWiseId = $examWise['ExamResult']['exam_id'];
			$endTime = CakeTime::format('Y-m-d H:i:s', CakeTime::fromString($examWise['ExamResult']['start_time']) + ($post['Exam']['duration'] * 60));
			if ($this->currentDateTime >= $endTime && $post['Exam']['duration'] > 0)
				$this->redirect(array('action' => 'finish', $examWiseId));
			if ($examWiseId != $id)
				$this->redirect(array('action' => 'start', $examWiseId, $quesNo, $currQuesNo));
		}
		$this->loadModel('ExamQuestion');
		$userExamQuestion = $this->Exam->userExamQuestion($id, $this->studentId, $quesNo, "all");
		$examResult = $this->ExamResult->find('first', array('conditions' => array('exam_id' => $id, 'student_id' => $this->studentId, 'end_time' => null)));
		$userSectionQuestion = $this->Exam->userSectionQuestion($id, $post['Exam']['type'], $this->studentId);
		if ($post['Exam']['type'] == "Exam")
			$totalQuestion = $this->ExamQuestion->find('count', array('conditions' => array('exam_id' => $id)));
		else
			$totalQuestion = $this->Exam->totalPrepQuestions($id, $this->studentId);
		$nquesNo = $quesNo;
		$pquesNo = $quesNo;

		if ($totalQuestion < $quesNo)
			$quesNo = 1;
		$currSubjectName = $this->Exam->userSubject($id, $quesNo, $this->studentId);
		$this->Exam->userQuestionRead($id, $quesNo, $this->studentId, $this->currentDateTime);
		$oquesNo = $quesNo;
		if ($totalQuestion == $quesNo)
			$quesNo = 0;
		if ($totalQuestion < $quesNo)
			$pquesNo = 2;
		if ($quesNo == 1)
			$pquesNo = 2;
		$this->set('userExamQuestionArr', $userExamQuestion);
		$this->set('userSectionQuestion', $userSectionQuestion);
		$this->set('currSubjectName', $currSubjectName);
		$this->set('post', $post);
		$this->set('examResult', $examResult);
		$this->set('siteTimezone', $this->siteTimezone);
		$this->set('examId', $id);
		$this->set('nquesNo', $quesNo + 1);
		$this->set('pquesNo', $pquesNo - 1);
		$this->set('oquesNo', $oquesNo);
		$this->set('totalQuestion', $totalQuestion);
		$this->set('examResultId', $userExamQuestion[0]['ExamStat']['exam_result_id']);
		$this->set('currQuesNo', $currQuesNo);
		$this->set('ajaxView', false);
	}

	public function rest_start()
	{
		$status = false;
		$message = __('Invalid Post');
		$userExamQuestion = array();
		$userSectionQuestion = array();
		$currSubjectName = null;
		$post = (object)array();
		$examResult = (object)array();
		$examId = null;
		$totalQuestion = null;
		$examResultId = null;
		$siteDomain = null;
		$studentDetail = (object)array();
		$studentPhoto = null;
		$examStatus = true;
		$examExpire = false;
		if ($this->authenticateRest($this->request->query)) {
			$this->studentId = $this->restStudentId($this->request->query);
			if (isset($this->request->query['id'])) {
				$id = $this->request->query['id'];
			} else {
				$id = 0;
			}
			$checkPost = $this->Exam->checkPost($id, $this->studentId);
			if ($checkPost == 0) {
				$message = __('Invalid Post');
			} else {
				$this->loadModel('ExamResult');
				$this->loadModel('ExamOrder');
				$post = $this->Exam->findById($id);
				$examId = $post['Exam']['id'];
				$currentExamResult = $this->ExamResult->find('count', array('conditions' => array('student_id' => $this->studentId, 'end_time' => null)));
				if ($currentExamResult == 0) {
					$paidexam = $post['Exam']['paid_exam'];
					$totalExam = $this->ExamResult->find('count', array('conditions' => array('exam_id' => $id, 'student_id' => $this->studentId)));
					$attempt_count = $post['Exam']['attempt_count'];
					if ($paidexam == 1) {
						if (!$this->checkPaidStatus($id, $this->studentId)) {
							if (!$this->rest_paidAmount($id, $this->studentId)) {
								$message = __('Insufficient Amount.');
								$examStatus = false;
							}
						}
					} else {
						if ($attempt_count <= $totalExam && $attempt_count > 0) {
							$message = __('You have attempted maximum exam.');
							$examStatus = false;
						}
					}
					if ($examStatus == true) {
						$this->Exam->userExamInsert($id, $post['Exam']['ques_random'], $post['Exam']['type'], $post['Exam']['option_shuffle'], $this->studentId, $this->currentDateTime);
					}
				}
				if ($examStatus == true) {
					$examWise = $this->ExamResult->find('first', array('conditions' => array('student_id' => $this->studentId, 'end_time' => null)));
					if ($currentExamResult == 1) {
						$examWiseId = $examWise['ExamResult']['exam_id'];
						$endTime = CakeTime::format('Y-m-d H:i:s', CakeTime::fromString($examWise['ExamResult']['start_time']) + ($post['Exam']['duration'] * 60));
						if ($this->currentDateTime >= $endTime && $post['Exam']['duration'] > 0) {
							$message = __('Exam expire');
							$examExpire = true;
							$post = (object)array();
						}
					}
					if ($examExpire == false) {
						$this->loadModel('ExamQuestion');
						$userExamQuestion = $this->Exam->userExamQuestion($id, $this->studentId, 1, 'all');
						$examResult = $this->ExamResult->find('first', array('conditions' => array('exam_id' => $id, 'student_id' => $this->studentId, 'end_time' => null)));
						$userSectionQuestion = $this->Exam->userSectionQuestion($id, $post['Exam']['type'], $this->studentId);
						if ($post['Exam']['type'] == "Exam") {
							$totalQuestion = $this->ExamQuestion->find('count', array('conditions' => array('exam_id' => $id)));
						} else {
							$totalQuestion = $this->Exam->totalPrepQuestions($id, $this->studentId);
						}
						$currSubjectName = $this->Exam->userSubject($id, 1, $this->studentId);
						$examResultId = $userExamQuestion[0]['ExamStat']['exam_result_id'];
						$this->loadModel('Student');
						$studentDetail = $this->Student->findById($this->studentId);
						if ($studentDetail['Student']['photo'] != null) {
							$studentPhoto = $this->siteDomain . 'img' . '/student_thumb/' . $studentDetail['Student']['photo'];
						} else {
							$studentPhoto = null;
						}
					}
					if ($examStatus == true && $examExpire == false) {
						$status = true;
						$message = "Exam fetch successfully.";
					}
				}
			}
		} else {
			$message = ('Invalid Token');
		}
		$this->set(compact('status', 'message', 'examExpire', 'userExamQuestion', 'userSectionQuestion', 'currSubjectName', 'post', 'examResult', 'examId', 'totalQuestion', 'examResultId', 'siteDomain', 'studentDetail', 'studentPhoto'));
		$this->set('_serialize', array('status', 'message', 'examExpire', 'userExamQuestion', 'userSectionQuestion', 'currSubjectName', 'post', 'examResult', 'examId', 'totalQuestion', 'examResultId', 'siteDomain', 'studentDetail', 'studentPhoto'));
	}

	public function crm_attemptTime($id, $quesNo, $currQuesNo)
	{
		$this->authenticate();
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		$this->Exam->userQuestionUpdate($id, $currQuesNo, $this->studentId, $this->currentDateTime);
		$this->Exam->userQuestionRead($id, $quesNo, $this->studentId, $this->currentDateTime);
	}

	public function crm_save($id, $quesNo)
	{
		$this->authenticate();
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		unset($_REQUEST['data']['Exam']['review']);
		$dataArr = $_REQUEST['data'];
		print_r($dataArr);
		if ($this->Exam->userSaveAnswer($id, $quesNo, $this->studentId, $this->currentDateTime, $dataArr)) {
		} else {
			$this->Session->setFlash(__('You have attempted maximum number of questions in this subject'), 'flash', array('alert' => 'danger'));
		}
	}

	public function crm_resetAnswer($id, $quesNo)
	{
		$this->authenticate();
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		$this->Exam->userResetAnswer($id, $quesNo, $this->studentId);
	}

	public function crm_reviewAnswer($id, $quesNo)
	{
		$this->authenticate();
		$this->autoRender = false;
		$this->request->onlyAllow('ajax');
		$this->Exam->userReviewAnswer($id, $quesNo, $this->studentId, 1);
	}

	public function crm_submit($examId = null, $examResultId = null, $origQuesNo = null)
	{
		$this->authenticate();
		$this->layout = null;
		$this->loadModel('ExamStat');
		$this->set('examId', $examId);
		$this->set('post', $this->Exam->findById($examId));
		$this->set('answered', $this->ExamStat->find('count', array('conditions' => array('ExamStat.exam_result_id' => $examResultId, 'answered' => 1, 'review' => 0))));
		$this->set('notAnswered', $this->ExamStat->find('count', array('conditions' => array('ExamStat.exam_result_id' => $examResultId, 'opened' => 1, 'answered' => 0, 'review' => 0))));
		$this->set('notansmarked', $this->ExamStat->find('count', array('conditions' => array('ExamStat.exam_result_id' => $examResultId, 'answered' => 0, 'review' => 1))));
		$this->set('ansmarked', $this->ExamStat->find('count', array('conditions' => array('ExamStat.exam_result_id' => $examResultId, 'answered' => 1, 'review' => 1))));
		$this->set('notAttempted', $this->ExamStat->find('count', array('conditions' => array('ExamStat.exam_result_id' => $examResultId, 'opened' => 0))));
	}

	public function rest_saveAll()
	{
		$message = __('Invalid Post');
		$status = false;
		$feedback = false;
		$result = false;
		$examResultId = null;
		if ($this->request->is('post')) {
			if (isset($this->request->data['responses'])) {
				$dataArr = $this->restPostKey($this->request->data);
				if ($this->authenticateRest($dataArr)) {
					$this->studentId = $this->restStudentId($dataArr);
					$this->loadModel('ExamResult');
					$id = $this->request->data['exam_id'];
					$examResultRecord = $this->ExamResult->find('first', array('fields' => array('id'), 'conditions' => array('exam_id' => $id, 'student_id' => $this->studentId, 'end_time' => null)));
					if ($examResultRecord) {
						$responseArr = $this->request->data['responses'];
						$examResultId = $examResultRecord['ExamResult']['id'];
						foreach ($responseArr as $item) {
							if ($item['question_type'] == "M") {
								$valueArr['Exam']['option_selected'] = $item['response'];
							} elseif ($item['question_type'] == "T") {
								$valueArr['Exam']['true_false'] = $item['response'];
							} elseif ($item['question_type'] == "F") {
								$valueArr['Exam']['fill_blank'] = $item['response'];
							} else {
								$valueArr['Exam']['answer'] = $item['response'];
							}
							$valueArr['Exam']['review'] = $item['review'];
							$valueArr['Exam']['attempt_time'] = $item['attempt_time'];
							$valueArr['Exam']['time_taken'] = $item['time_taken'];
							$quesNo = $item['question_no'];
							$this->Exam->userSaveAnswer($id, $quesNo, $this->studentId, $this->currentDateTime, $valueArr);
						}
						$requestArr = $this->rest_finish($id, $this->studentId);
						$status = true;
						$feedback = $requestArr['feedback'];
						$result = $requestArr['result'];
						$message = $requestArr['message'];
					} else {
						$message = __('No exam opened');
					}
				} else {
					$message = ('Invalid Token');
				}
			}
		} else {
			$message = __('GET request not allowed!');
		}
		$this->set(compact('status', 'message', 'feedback', 'result', 'examResultId'));
		$this->set('_serialize', array('status', 'message', 'feedback', 'result', 'examResultId'));
	}

	public function crm_finish($id = null, $warn = null, $origQuesNo = null)
	{
		$this->authenticate();
		$this->autoRender = false;
		if ($id == null) {
			$this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
			$this->redirect(array('action' => 'index'));
		}
		$this->loadModel('ExamResult');
		$currentExamResult = $this->ExamResult->find('first', array('conditions' => array('exam_id' => $id, 'student_id' => $this->studentId, 'end_time' => null)));
		if ($currentExamResult) {
			$this->Exam->userQuestionUpdate($id, $origQuesNo, $this->studentId, $this->currentDateTime);
			$this->Exam->userExamFinish($id, $this->studentId, $this->currentDateTime);
			if ($warn == null || $warn == 'null') {
				$this->loadModel('Exam');
				$examArr = $this->Exam->findById($id);
				if ($this->examFeedback) {
					if ($examArr['Exam']['finish_result'])
						$this->resultEmailSms($currentExamResult, $examArr);
					$this->redirect(array('controller' => 'Exams', 'action' => 'feedbacks', $currentExamResult['ExamResult']['id']));
					exit(0);
				} else {
					if ($examArr['Exam']['finish_result']) {
						$this->resultEmailSms($currentExamResult, $examArr);
						$this->Session->setFlash(__('You can find your result here'), 'flash', array('alert' => 'success'));
						$this->redirect(array('controller' => 'Results', 'action' => 'view', $currentExamResult['ExamResult']['id']));
					} else {
						$this->Session->setFlash(__('Thanks for given the exam.'), 'flash', array('alert' => 'success'));
						$this->redirect(array('controller' => 'Exams', 'action' => 'index'));
					}
				}
			} else {
				$this->redirect(array('controller' => 'Ajaxcontents', 'action' => 'examclose', $currentExamResult['ExamResult']['id']));
				exit(0);
			}
		} else {
			$this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
			$this->redirect(array('action' => 'index'));
		}
	}

	public function rest_expiredFinish()
	{
		$message = __('Invalid Post');
		$status = false;
		$feedback = false;
		$result = false;
		$examResultId = null;
		if ($this->request->is('post')) {
			if (isset($this->request->data['exam_id'])) {
				$dataArr = $this->restPostKey($this->request->data);
				if ($this->authenticateRest($dataArr)) {
					$this->studentId = $this->restStudentId($dataArr);
					$id = $this->request->data['exam_id'];
					$requestArr = $this->rest_finish($id, $this->studentId);
					$examResultId = $requestArr['examResultId'];
					$status = true;
					$feedback = $requestArr['feedback'];
					$result = $requestArr['result'];
					$message = $requestArr['message'];
				} else {
					$message = ('Invalid Token');
				}
			}
		} else {
			$message = __('GET request not allowed!');
		}
		$this->set(compact('status', 'message', 'feedback', 'result', 'examResultId'));
		$this->set('_serialize', array('status', 'message', 'feedback', 'result', 'examResultId'));
	}

	public function crm_feedbacks($id)
	{
		$this->authenticate();
		$this->layout = 'exam';
		if (!$id) {
			$this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
			$this->redirect(array('action' => 'index'));
		}
		$this->loadModel('ExamResult');
		$examArr = $this->ExamResult->findByIdAndStudentId($id, $this->studentId);
		if (!$examArr) {
			$this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
			$this->redirect(array('action' => 'error'));
		}
		$this->set('id', $id);
		$this->set('isClose', 'No');
		if ($this->request->is('post')) {
			try {
				$this->loadModel('ExamFeedback');
				$this->ExamFeedback->create();
				$this->request->data['Exam']['exam_result_id'] = $id;
				$recordArr['ExamFeedback'] = $this->request->data['Exam'];
				$this->ExamFeedback->save($recordArr);
				$this->Session->setFlash(__('Feedback has submitted successfully!'), 'flash', array('alert' => 'success'));
				$this->set('isClose', 'Yes');
			} catch (Exception $e) {
				$this->Session->setFlash(__('Feedback already submitted.'), 'flash', array('alert' => 'danger'));
				$this->set('isClose', 'Yes');
			}
		}
	}

	public function rest_showFeedback()
	{
		if ($this->authenticateRest($this->request->query)) {
			$response = $this->feedbackArr;
			$status = true;
			$message = __('Feedback fetch successfully');
		} else {
			$status = false;
			$message = ('Invalid Token');
			$response = (object)array();
		}
		$this->set(compact('status', 'message', 'response'));
		$this->set('_serialize', array('status', 'message', 'response'));
	}

	public function rest_feedbacks()
	{
		$message = __('Invalid Post');
		$status = false;
		$examResultId = null;
		if ($this->request->is('post')) {
			if (isset($this->request->data['responses'])) {
				$dataArr = $this->restPostKey($this->request->data);
				if ($this->authenticateRest($dataArr)) {
					$this->studentId = $this->restStudentId($dataArr);
					if (isset($this->request->data['exam_result_id'])) {
						$id = $this->request->data['exam_result_id'];
					} else {
						$id = 0;
					}
					$this->loadModel('ExamResult');
					$examArr = $this->ExamResult->findByIdAndStudentId($id, $this->studentId);
					if ($id && $examArr) {
						try {
							$this->loadModel('ExamFeedback');
							$this->ExamFeedback->create();
							$recordArr['ExamFeedback'] = $this->request->data['responses'];
							$recordArr['ExamFeedback']['exam_result_id'] = $id;
							$this->ExamFeedback->save($recordArr);
							$message = __('Feedback has submitted successfully!');
							$status = true;
							$examResultId = $id;
						} catch (Exception $e) {
							$message = __('Feedback already submitted.');
						}
					} else {
						$message = __('Invalid Post');
					}
				} else {
					$message = ('Invalid Token');
				}
			}
		} else {
			$message = __('GET request not allowed!');
		}
		$this->set(compact('status', 'message', 'examResultId'));
		$this->set('_serialize', array('status', 'message', 'examResultId'));
	}

	public function crm_paid($id = null, $type = null)
	{
		$this->authenticate();
		if ($id == null) {
			$this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
			$this->redirect(array('action' => 'index'));
		}
		$this->loadModel('Exam');
		$post = $this->Exam->findByIdAndStatus($id, 'Active');
		if (!$post) {
			$this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
			$this->redirect(array('action' => 'error'));
		} else {
			if ($this->checkPaidStatus($id, $this->studentId)) {
				$this->redirect(array('action' => 'start', $id));
			} else {
				if ($this->paidAmount($id)) {
					$this->redirect(array('action' => 'start', $id));
				}
			}
		}
		$this->redirect(array('action' => 'index'));
	}

	public function paidAmount($id)
	{
		$this->authenticate();
		$exampost = $this->Exam->findByIdAndPaidExam($id, '1');
		$amount = $exampost['Exam']['amount'];
		$balance = $this->CustomFunction->WalletBalance($this->studentId);
		if ($balance >= $amount) {
			if ($this->CustomFunction->WalletInsert($this->studentId, $amount, "Deducted", $this->currentDateTime, "EM", __d('default', $amount, "%s Deducted for paying exam"))) {
				$this->loadModel('ExamOrder');
				$this->ExamOrder->create();
				$expiryDays = $exampost['Exam']['expiry'];
				if ($expiryDays > 0) {
					$expiryDate = date('Y-m-d', strtotime($this->currentDate . "+$expiryDays days"));
				} else {
					$expiryDate = null;
				}
				$this->ExamOrder->save(array("student_id" => $this->studentId, "exam_id" => $id, 'date' => $this->currentDate, 'expiry_date' => $expiryDate));
				return true;
			}
		} else {
			$this->Session->setFlash(__('Insufficient Amount.'), 'flash', array('alert' => 'danger'));
			$this->redirect(array('action' => 'index'));
		}
		return false;
	}

	public function crm_renewexam($id)
	{
		$this->authenticate();
		if ($this->paidAmount($id)) {
			$this->redirect(array('action' => 'index'));
		}
	}

	private function resultEmailSms($currentExamResult, $examArr)
	{
		try {
			if ($this->emailNotification || $this->smsNotification) {
				$valueArr = $this->ExamResult->findById($currentExamResult['ExamResult']['id']);
				$siteName = $this->siteName;
				$siteEmailContact = $this->siteEmailContact;
				$url = $this->siteDomain;
				$email = $this->userValue['Student']['email'];
				$studentName = $this->userValue['Student']['name'];
				$mobileNo = $this->userValue['Student']['phone'];
				$examName = $examArr['Exam']['name'];
				$result = $valueArr['ExamResult']['result'];
				$obtainedMarks = $valueArr['ExamResult']['obtained_marks'];
				$questionAttempt = $valueArr['ExamResult']['total_answered'];
				$timeTaken = $this->CustomFunction->secondsToWords(CakeTime::fromString($valueArr['ExamResult']['end_time']) - CakeTime::fromString($valueArr['ExamResult']['start_time']));
				$percent = $valueArr['ExamResult']['percent'];
				if ($this->emailNotification == 1) {
					/* Send Email */
					$this->loadModel('Emailtemplate');
					$emailSettingArr = $this->Emailtemplate->findByType('ERT');
					if ($emailSettingArr['Emailtemplate']['status'] == "Published") {
						$message = eval('return "' . addslashes($emailSettingArr['Emailtemplate']['description']) . '";');
						$Email = new CakeEmail();
						$Email->transport($this->emailSettype);
						if ($this->emailSettype == "Smtp")
							$Email->config(array('host' => $this->emailHost, 'port' => $this->emailPort, 'username' => $this->emailUsername, 'password' => $this->emailPassword, 'timeout' => 90));
						$Email->from(array($this->siteEmail => $this->siteName));
						$Email->to($email);
						$Email->template('default');
						$Email->emailFormat('html');
						$Email->subject($emailSettingArr['Emailtemplate']['name']);
						$Email->send($message);
						/* End Email */
					}
				}
				if ($this->smsNotification) {
					/* Send Sms */
					$this->loadModel('Smstemplate');
					$smsTemplateArr = $this->Smstemplate->findByType('ERT');
					if ($smsTemplateArr['Smstemplate']['status'] == "Published") {
						$url = "$this->siteDomain";
						$message = eval('return "' . addslashes($smsTemplateArr['Smstemplate']['description']) . '";');
						$this->CustomFunction->sendSms($mobileNo, $message, $this->smsSettingArr);
					}
					/* End Sms */
				}
			}
		} catch (Exception $e) {
			$this->Session->setFlash($e->getMessage(), 'flash', array('alert' => 'danger'));
		}
	}

	private function checkPaidStatus($examId, $studentId)
	{
		$this->loadModel('Exam');
		$this->loadModel('ExamResult');
		$this->loadModel('ExamOrder');
		$post = $this->Exam->findByIdAndStatus($examId, 'Active');
		$attemptCount = $post['Exam']['attempt_count'];
		$paidexam = $post['Exam']['paid_exam'];
		$expiry = $post['Exam']['expiry'];
		$totalExam = $this->ExamResult->find('count', array('conditions' => array('exam_id' => $examId, 'student_id' => $studentId)));
		$countExamOrder = $this->ExamOrder->find('count', array('conditions' => array('exam_id' => $examId, 'student_id' => $studentId)));
		$ispaid = false;
		if ($paidexam == 1) {
			if ($countExamOrder > 0 && $attemptCount == 0) {
				$ispaid = true;
			} else {
				if ($countExamOrder * $attemptCount > $totalExam) {
					$ispaid = true;
				}
			}
		} else {
			$ispaid = true;
		}
		if ($expiry > 0) {
			$examOrder = $this->ExamOrder->find('first', array('conditions' => array('exam_id' => $examId, 'student_id' => $studentId), 'order' => array('id' => 'desc')));
			if ($examOrder && $this->currentDate > $examOrder['ExamOrder']['expiry_date']) {
				$ispaid = false;
			}
		}
		return $ispaid;
	}

	private function checkExpiryStatus($examId, $studentId, $expiryDays)
	{
		$this->loadModel('ExamOrder');
		$examOrder = $this->ExamOrder->find('first', array('conditions' => array('exam_id' => $examId, 'student_id' => $studentId), 'order' => array('id' => 'desc')));
		if ($examOrder) {
			if ($examOrder['ExamOrder']['expiry_date']!=NULL && $this->currentDate > $examOrder['ExamOrder']['expiry_date']) {
				$status = false;
			} else {
				$status = true;
			}
		} else {
			$examPost = $this->Exam->findById($examId);
			$expiryDays = $examPost['Exam']['expiry'];
			if ($expiryDays > 0) {
				$expiryDate = date('Y-m-d', strtotime($this->currentDate . "+$expiryDays days"));
			} else {
				$expiryDate = null;
			}
			$this->ExamOrder->create();
			$this->ExamOrder->save(array('ExamOrder' => array('student_id' => $studentId, 'exam_id' => $examId, 'date' => $this->currentDate, 'expiry_date' => $expiryDate)));
			$status = true;
		}
		return $status;
	}

	private function rest_finish($id, $studentId)
	{
		$feedback = false;
		$result = false;
		$message = null;
		$this->studentId = $studentId;
		$this->loadModel('ExamResult');
		$currentExamResult = $this->ExamResult->find('first', array('conditions' => array('exam_id' => $id, 'student_id' => $this->studentId, 'end_time' => null)));
		if ($currentExamResult) {
			$this->Exam->userExamFinish($id, $this->studentId, $this->currentDateTime);
			$this->loadModel('Exam');
			$examArr = $this->Exam->findById($id);
			if ($this->examFeedback) {
				if ($examArr['Exam']['finish_result']) {
					$this->resultEmailSms($currentExamResult, $examArr);
					$result = true;
					$message = __('Please complete feedback.');
				}
				$feedback = true;
			} else {
				if ($examArr['Exam']['finish_result']) {
					$this->resultEmailSms($currentExamResult, $examArr);
					$feedback = false;
					$result = true;
					$message = __('You can find your result here');
				} else {
					$feedback = false;
					$result = false;
					$message = __('Thanks for given the exam.');
				}
			}
		}
		return array('feedback' => $feedback, 'result' => $result, 'message' => $message, 'examResultId' => $currentExamResult['ExamResult']['id']);
	}

	private function rest_paidAmount($id, $studentId)
	{
		$this->studentId = $studentId;
		$exampost = $this->Exam->findByIdAndPaidExam($id, 1);
		$amount = $exampost['Exam']['amount'];
		$balance = $this->CustomFunction->WalletBalance($this->studentId);
		if ($balance >= $amount) {
			if ($this->CustomFunction->WalletInsert($this->studentId, $amount, "Deducted", $this->currentDateTime, "EM", __d('default', $amount, "%s Deducted for paying exam"))) {
				$this->loadModel('ExamOrder');
				$this->ExamOrder->create();
				$expiryDays = $exampost['Exam']['expiry'];
				if ($expiryDays) {
					$expiryDate = date('Y-m-d', strtotime($this->currentDate . "+$expiryDays days"));
				} else {
					$expiryDate = null;
				}
				$this->ExamOrder->save(array("student_id" => $this->studentId, "exam_id" => $id, 'date' => $this->currentDate, 'expiry_date' => $expiryDate));
				return true;
			}
		} else {
			return false;
		}
	}
}
