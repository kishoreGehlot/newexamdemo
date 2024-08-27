<?php

class TransactionhistorysController extends AppController
{
    public $helpers = array('Paginator', 'Js' => array('Jquery'));
    public $components = array('Paginator', 'RequestHandler');
    public $currentDateTime, $studentId;
    var $paginate = array('page' => 1, 'order' => array('Transactionhistory.id' => 'desc'));

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->studentId = $this->userValue['Student']['id'];
        $this->paymentTypeArr = array("AD" => __('Administrator'), "PG" => __('Payment Gateway'), "EM" => __('Pay Exam'));
    }

    public function crm_index()
    {
        $this->authenticate();
        $this->Paginator->settings = $this->paginate;
        $this->Paginator->settings['conditions'] = array('Transactionhistory.student_id' => $this->studentId);
        $this->Paginator->settings['limit'] = $this->pageLimit;
        $this->Paginator->settings['maxLimit'] = $this->maxLimit;
        $this->set('Transactionhistory', $this->Paginator->paginate());
        $this->set('payment_type_arr', $this->paymentTypeArr);
        if ($this->request->is('ajax')) {
            $this->render('crm_index', 'ajax'); // View, Layout
        }
    }

    public function rest_index()
    {
        if ($this->authenticateRest($this->request->query)) {
            $this->studentId = $this->restStudentId($this->request->query);
            $this->Paginator->settings = $this->paginate;
            $this->Paginator->settings['conditions'] = array('Transactionhistory.student_id' => $this->studentId);
            $this->Paginator->settings['limit'] = 99999999999999999;
            $this->Paginator->settings['maxLimit'] = $this->maxLimit;
            $response = $this->Paginator->paginate();
            $status = true;
            $message = __('Transaction History fetch successfully');
            $paymentTypeArr = $this->paymentTypeArr;
        } else {
            $status = false;
            $message = ('Invalid Token');
            $response = (object)array();
            $paymentTypeArr = (object)array();
        }
        $this->set(compact('status', 'message', 'paymentTypeArr', 'response'));
        $this->set('_serialize', array('status', 'message', 'paymentTypeArr', 'response'));
    }
}
