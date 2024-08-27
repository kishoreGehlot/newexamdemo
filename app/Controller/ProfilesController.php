<?php
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class ProfilesController extends AppController
{
    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session', 'RequestHandler');
    public $presetVars = true;

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function crm_index()
    {
        $this->authenticate();
        $this->loadModel('Student');
        $id = $this->userValue['Student']['id'];
        $post = $this->Student->findById($id);
        $this->loadModel('StudentGroup');
        $groupSelect = $this->StudentGroup->find('all', array('fields' => array('Groups.group_name'),
            'joins' => array(array('table' => 'groups', 'type' => 'Inner', 'alias' => 'Groups',
                'conditions' => array('StudentGroup.group_id=Groups.id', "student_id=$id")))));
        if (strlen($post['Student']['photo']) > 0)
            $std_img = 'student_thumb/' . $post['Student']['photo'];
        else
            $std_img = 'User.png';
        $this->set('post', $post);
        $this->set('std_img', $std_img);
        $this->set('groupSelect', $groupSelect);
    }

    public function rest_index()
    {
        if ($this->authenticateRest($this->request->query)) {
            $this->studentId = $this->restStudentId($this->request->query);
            $this->loadModel('Student');
            $id = $this->userValue['Student']['id'];
            $post = $this->Student->findById($id);
            $this->loadModel('StudentGroup');
            $groupSelect = $this->StudentGroup->find('all', array('fields' => array('Groups.group_name'),
                'joins' => array(array('table' => 'groups', 'type' => 'Inner', 'alias' => 'Groups',
                    'conditions' => array('StudentGroup.group_id=Groups.id', "student_id=$id")))));
            if (strlen($post['Student']['photo']) > 0)
                $std_img = $this->siteDomain . '/img/student_thumb/' . $post['Student']['photo'];
            else
                $std_img = $this->siteDomain . '/img/User.png';
            $this->set('response', $post);
            $this->set('studentImage', $std_img);
            $this->set('groupSelect', $groupSelect);
            $status = true;
            $message = __('Profile fetch successfully');
        } else {
            $status = false;
            $message = ('Invalid Token');
            $this->set('response', (object)array());
            $this->set('studentImage', null);
            $this->set('groupSelect', array());
        }
        $this->set(compact('status', 'message'));
        $this->set('_serialize', array('status', 'message', 'response', 'studentImage', 'groupSelect'));
    }

    public function crm_editProfile()
    {
        $id = $this->userValue['Student']['id'];
        $post = $this->Profile->findById($id);
        $this->loadModel('StudentGroup');
        if (!$post) {
            $this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
            $this->redirect(array('action' => 'index'));
        }
        if ($this->request->is(array('post', 'put'))) {
            $this->Profile->id = $id;
            if ($this->Profile->save($this->request->data)) {
                $this->Session->setFlash(__('Profile Updated Successfully'), 'flash', array('alert' => 'success'));
                $this->redirect(array('action' => 'editProfile'));
            }
        }
        if (!$this->request->data) {
            $this->request->data = $post;
        }
    }

    public function rest_editProfile()
    {
        $message = __('Invalid Post');
        $status = false;
        if ($this->request->is('post')) {
            if (isset($this->request->data['public_key']) && isset($this->request->data['private_key'])) {
                $dataArr = $this->restPostKey($this->request->data);
                if ($this->authenticateRest($dataArr)) {
                    $this->studentId = $this->restStudentId($dataArr);
                    $this->request->data['Profile'] = $this->request->data;
                    $this->Profile->id = $this->studentId;
                    if ($this->Profile->save($this->request->data)) {
                        $status = true;
                        $message = __('Profile Updated Successfully');
                    } else {
                        $message = __('The Profile could not be saved. Please, try again.');
                    }
                } else {
                    $message = ('Invalid Token');
                }
            } else {
                $message = __('Invalid Post');
            }
        } else {
            $message = __('GET request not allowed!');
        }
        $this->set(compact('status', 'message'));
        $this->set('_serialize', array('status', 'message'));
    }

    public function crm_changePhoto()
    {
        $id = $this->userValue['Student']['id'];
        $post = $this->Profile->findById($id);
        if (!$post) {
            $this->Session->setFlash(__('Invalid Post'), 'flash', array('alert' => 'danger'));
            $this->redirect(array('action' => 'index'));
        }
        if ($this->request->is(array('post', 'put'))) {
            $this->Profile->id = $id;
            if ($this->Profile->save($this->request->data)) {
                $this->Session->setFlash(__('Photo Updated Successfully'), 'flash', array('alert' => 'success'));
                $this->redirect(array('action' => 'index'));
            }
        }
    }

    public function rest_changePhoto()
    {
        $message = __('Invalid Post');
        $status = false;
        if ($this->request->is('post')) {
            if (isset($this->request->data['public_key']) && isset($this->request->data['private_key']) && isset($this->request->data['photo'])) {
                $dataArr = $this->restPostKey($this->request->data);
                if ($this->authenticateRest($dataArr)) {
                    $this->studentId = $this->restStudentId($dataArr);
                    $this->request->data['Profile'] = $this->request->data;
                    $this->Profile->id = $this->studentId;
                    if ($this->Profile->save($this->request->data)) {
                        $status = true;
                        $message = __('Photo Updated Successfully');
                    } else {
                        $message = __('The Photo could not be saved. Please, try again.');
                    }
                } else {
                    $message = ('Invalid Token');
                }
            } else {
                $message = __('Invalid Post');
            }
        } else {
            $message = __('GET request not allowed!');
        }
        $this->set(compact('status', 'message'));
        $this->set('_serialize', array('status', 'message'));
    }

    public function crm_changePass()
    {
        if ($this->request->is(array('post', 'put'))) {
            $passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
            $id = $this->userValue['Student']['id'];
            $post = $this->Profile->findById($id);
            if ($post['Profile']['password'] == $passwordHasher->hash($this->request->data['Profile']['oldPassword'])) {
                $this->Profile->id = $id;
                $this->request->data['Profile']['password'] = $passwordHasher->hash($this->request->data['Profile']['password']);
                $this->Profile->unbindValidation('remove', array('photo'), true);

                if ($this->Profile->save($this->request->data)) {
                    $this->Session->setFlash(__('Password Changed Successfully'), 'flash', array('alert' => 'success'));
                }
            } else {
                $this->Session->setFlash(__('Invalid Password'), 'flash', array('alert' => 'danger'));
            }
            $this->redirect(array('action' => 'changePass'));
        }
    }

    public function rest_changePass()
    {
        $message = __('Invalid Post');
        $status = false;
        if ($this->request->is('post')) {
            if (isset($this->request->data['public_key']) && isset($this->request->data['private_key'])) {
                $dataArr = $this->restPostKey($this->request->data);
                if ($this->authenticateRest($dataArr)) {
                    $this->studentId = $this->restStudentId($dataArr);
                    $passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
                    $id = $this->studentId;
                    $post = $this->Profile->findById($id);
                    $this->request->data['Profile'] = $this->request->data;
                    if ($post['Profile']['password'] == $passwordHasher->hash($this->request->data['Profile']['oldPassword'])) {
                        if ($this->request->data['Profile']['password'] == $this->request->data['Profile']['con_password']) {
                            $this->Profile->id = $id;
                            $this->request->data['Profile']['password'] = $passwordHasher->hash($this->request->data['Profile']['password']);
                            $this->Profile->unbindValidation('remove', array('photo'), true);
                            if ($this->Profile->save($this->request->data)) {
                                $status = true;
                                $message = __('Password Changed Successfully');
                            } else {
                                $message = __('The Password could not be changed. Please, try again.');
                            }
                        } else {
                            $message = __('Password & confirm password not matched.');
                        }
                    } else {
                        $message = __('Invalid Password');
                    }
                } else {
                    $message = ('Invalid Token');
                }
            } else {
                $message = __('Invalid Post');
            }
        } else {
            $message = __('GET request not allowed!');
        }
        $this->set(compact('status', 'message'));
        $this->set('_serialize', array('status', 'message'));
    }
}
