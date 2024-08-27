<?php
class HelpsController extends AppController {
    public $components = array('RequestHandler');
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->studentId=$this->userValue['Student']['id'];
    }
    public function crm_index()
    {
        $this->authenticate();
        $helpPost=$this->Help->find('all',array('conditions'=>array('status'=>'Active'),
                                                'order'=>'id asc'));
        $this->set('helpPost',$helpPost);
    }
    public function rest_index()
    {
        if ($this->authenticateRest($this->request->query)) {
            $response = $this->Help->find('all', array('conditions' => array('status' => 'Active'),
                'order' => 'id asc'));
            $status = true;
            $message = __('Help data fetch successfully.');
        } else {
            $status = false;
            $message = ('Invalid Token');
            $response = (object)array();
        }
        $this->set(compact('status', 'message', 'response'));
        $this->set('_serialize', array('status', 'message', 'response'));
    }
}
