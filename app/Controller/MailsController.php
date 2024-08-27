<?php
App::uses('CakeTime', 'Utility');

class MailsController extends AppController
{
    public $helpers = array('Html', 'Form', 'Session', 'Paginator', 'Js' => array('Jquery'));
    public $components = array('Session', 'Paginator', 'search-master.Prg');
    public $presetVars = true;
    var $paginate = array('page' => 1, 'order' => array('Mail.date' => 'desc'));

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->mailType = $this->userValue['Student']['email'];
    }

    public function crm_index()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $this->layout = 'rest';
        }
        if (isset($this->request->query['public_key']) && isset($this->request->query['private_key'])) {
            if (!$this->authenticateRest($this->request->query)) {
                echo ('Invalid Token');
                die();
            } else {
                $this->loadModel('Student');
                $post = $this->Student->findByPublicKeyAndPrivateKey($this->request->query['public_key'], $this->request->query['private_key']);
                $this->Session->write('Student', $post);
                $this->mailType = $post['Student']['email'];
            }
        } else {
            $this->authenticate();
        }
        $this->Prg->commonProcess();
        $this->Paginator->settings = $this->paginate;
        $this->Paginator->settings['conditions'] = array('email' => $this->mailType, 'mail_type' => 'To', 'status <>' => 'Trash', $this->Mail->parseCriteria($this->Prg->parsedParams()));
        $this->Paginator->settings['limit'] = $this->pageLimit;
        $this->Paginator->settings['maxLimit'] = $this->maxLimit;
        $this->set('Mail', $this->Paginator->paginate());
        $this->set('mailType', $this->mailType);
        if ($this->request->is('ajax')) {
            $this->render('crm_index', 'ajax'); // View, Layout
        }
    }

    public function crm_sent()
    {
        $this->authenticate();
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $this->layout = 'rest';
        }
        $this->Prg->commonProcess();
        $this->Paginator->settings = $this->paginate;
        $this->Paginator->settings['conditions'] = array('email' => $this->mailType, 'mail_type' => 'From', 'status <>' => 'Trash', $this->Mail->parseCriteria($this->Prg->parsedParams()));
        $this->Paginator->settings['limit'] = $this->pageLimit;
        $this->Paginator->settings['maxLimit'] = $this->maxLimit;
        $this->set('Mail', $this->Paginator->paginate());
        $this->set('mailType', $this->mailType);
        if ($this->request->is('ajax')) {
            $this->render('crm_index', 'ajax'); // View, Layout
        } else
            $this->render('crm_index');
    }

    public function crm_trash()
    {
        $this->authenticate();
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $this->layout = 'rest';
        }
        $this->Prg->commonProcess();
        $this->Paginator->settings = $this->paginate;
        $this->Paginator->settings['conditions'] = array('email' => $this->mailType, 'status' => 'Trash', $this->Mail->parseCriteria($this->Prg->parsedParams()));
        $this->Paginator->settings['limit'] = $this->pageLimit;
        $this->Paginator->settings['maxLimit'] = $this->maxLimit;
        $this->set('Mail', $this->Paginator->paginate());
        $this->set('mailType', $this->mailType);
        if ($this->request->is('ajax')) {
            $this->render('crm_index', 'ajax'); // View, Layout
        } else
            $this->render('crm_index');
    }

    public function crm_compose($type = null, $id = null)
    {
        $this->authenticate();
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $this->layout = 'rest';
        }
        $this->loadModel('User');
        $this->loadModel('StudentGroup');
        $studentId = $this->userValue['Student']['id'];
        $studentGroups = $this->StudentGroup->find('all', array('conditions' => array('StudentGroup.student_id' => $studentId)));
        foreach ($studentGroups as $value) {
            $userArr[] = $value['StudentGroup']['group_id'];
        }
        $userFinal = $this->User->find('list', array('joins' => array(array('table' => 'user_groups', 'alias' => 'UserGroup', 'type' => 'Left', 'conditions' => array('User.id=UserGroup.user_id'))),
            'fields' => array('User.name', 'User.name'),
            'conditions' => array('UserGroup.group_id' => $userArr), 'group' => array('User.id')));
        $this->set('userFinal', $userFinal);
        if ($this->request->is('post')) {
            try {
                $flag = false;
                foreach ($userFinal as $index => $value) {
                    if ($this->request->data['Mail']['to_email'] == $index) {
                        $flag = true;
                    }
                }
                if ($flag == true) {
                    $this->Mail->create();
                    $toEmail = $this->request->data['Mail']['to_email'];
                    $currentDateTime = $this->currentDateTime;
                    $this->request->data['Mail']['date'] = $currentDateTime;
                    $this->request->data['Mail']['from_email'] = $this->mailType;
                    $this->request->data['Mail']['email'] = $this->request->data['Mail']['to_email'];
                    if ($this->Mail->save($this->request->data)) {
                        $this->Mail->create();
                        $this->request->data['Mail']['to_email'] = $toEmail;
                        $this->request->data['Mail']['from_email'] = $this->mailType;
                        $this->request->data['Mail']['email'] = $this->mailType;
                        $this->request->data['Mail']['type'] = "Read";
                        $this->request->data['Mail']['mail_type'] = "From";
                        $this->Mail->save($this->request->data);
                        $this->Session->setFlash(__('Mail has been sent'), 'flash', array('alert' => 'success'));
                        return $this->redirect(array('action' => 'index'));
                    }
                } else {
                    $this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
                }
            } catch (Exception $e) {
                $this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
                return $this->redirect(array('action' => 'index'));
            }
        }
        $post = $this->Mail->findById($id);
        if ($post) {
            if ($post['Mail']['to_email'] == $this->mailType)
                $this->request->data['Mail']['to_email'] = $post['Mail']['from_email'];
            else
                $this->request->data['Mail']['to_email'] = $post['Mail']['to_email'];
            $this->request->data['Mail']['subject'] = $post['Mail']['subject'];
            if ($type == "forward") {
                $message = "<p>----------" . __('Forwarded message') . "----------</p>\n<p>" . __('From') . ": " . $this->request->data['Mail']['to_email'] . "</p>\n<p>" . CakeTime::format('D, M d, Y', $post['Mail']['date']) . ".__('at')." . CakeTime::format('h:i A', $post['Mail']['date']) .
                    "</p>\n<p>" . __('Subject') . ":" . $this->request->data['Mail']['subject'] . "</p>\n";
                $this->request->data['Mail']['message'] = $message . $post['Mail']['message'];
            }
        }
    }

    public function crm_view($id = null)
    {
        $this->authenticate();
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $this->layout = 'rest';
        }
        $this->layout = null;
        if (!$id) {
            $this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
            $this->redirect(array('action' => 'index'));
        }
        $post = $this->Mail->findById($id);
        $this->Mail->unbindValidation('remove', array('to_email', 'subject'), true);
        if ($post)
            $this->Mail->save(array('id' => $id, 'type' => 'Read'));
        $this->set('post', $post);
    }

    public function crm_trashall()
    {
        $this->authenticate();
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $this->layout = 'rest';
        }
        try {
            if ($this->data['type'] == "read") {
                $this->moveMark($this->data, "Read");
            } elseif ($this->data['type'] == "unread") {
                $this->moveMark($this->data, "Unread");
            } else {
                if ($this->request->is('post')) {
                    $this->Mail->unbindValidation('remove', array('to_email', 'subject'), true);
                    if ($this->data['Mail']['id']) {
                        foreach ($this->data['Mail']['id'] as $key => $value) {
                            if ($value > 0)
                                $this->Mail->save(array('id' => $value, 'status' => 'Trash'));
                        }
                    }
                    $this->Session->setFlash(__('The mail has been moved to the Trash'), 'flash', array('alert' => 'success'));
                }
                $this->redirect(array('action' => 'index'));
            }
        } catch (Exception $e) {
            $this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
            return $this->redirect(array('action' => 'index'));
        }
    }

    public function crm_deleteall()
    {
        $this->authenticate();
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $this->layout = 'rest';
        }
        try {
            if ($this->data['type'] == "inbox") {
                $this->moveInbox($this->data, "Inbox");
            } else {
                if ($this->request->is('post')) {
                    if ($this->data['Mail']['id']) {
                        foreach ($this->data['Mail']['id'] as $key => $value) {
                            $this->Mail->delete($value);
                        }
                    }
                    $this->Session->setFlash(__('Mail has been deleted'), 'flash', array('alert' => 'success'));
                }
                $this->redirect(array('action' => 'index'));
            }
        } catch (Exception $e) {
            $this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
            return $this->redirect(array('action' => 'index'));
        }
    }

    public function moveInbox($record)
    {
        $this->authenticate();
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $this->layout = 'rest';
        }
        try {
            if ($record) {
                $this->Mail->unbindValidation('remove', array('to_email', 'subject'), true);
                if ($this->data['Mail']['id']) {
                    foreach ($record['Mail']['id'] as $key => $value) {
                        if ($value > 0)
                            $this->Mail->save(array('id' => $value, 'status' => 'Live'));
                    }
                }
                $this->Session->setFlash(__('The mail has been moved to the Inbox'), 'flash', array('alert' => 'success'));
            }
            $this->redirect(array('action' => 'trash'));
        } catch (Exception $e) {
            $this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
            return $this->redirect(array('action' => 'index'));
        }
    }

    public function moveMark($record, $type)
    {
        $this->authenticate();
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $this->layout = 'rest';
        }
        try {
            if ($record) {
                $this->Mail->unbindValidation('remove', array('to_email', 'subject'), true);
                if ($this->data['Mail']['id']) {
                    foreach ($record['Mail']['id'] as $key => $value) {
                        if ($value > 0)
                            $this->Mail->save(array('id' => $value, 'type' => $type));
                    }
                }
                if ($type == "Read") {
                    $this->Session->setFlash(__("The mail has been marked as Read"), 'flash', array('alert' => 'success'));
                } else {
                    $this->Session->setFlash(__("The mail has been marked as Unread"), 'flash', array('alert' => 'success'));
                }
            }
            $this->redirect(array('action' => 'index'));
        } catch (Exception $e) {
            $this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
            return $this->redirect(array('action' => 'index'));
        }
    }
}
