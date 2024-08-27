<?php
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class UsersController extends AppController
{
    public $helpers = array('Html');
    public $components = array('RequestHandler');

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function crm_index()
    {
        if ($this->Session->check('Student')) {
            $this->redirect(array('controller' => 'Dashboards', 'action' => 'index'));
            exit();
        }
        $this->redirect(array('action' => 'login'));
    }

    public function crm_login()
    {
        $this->layout = 'login';
        if (empty($this->data['User']['email']) == false) {
            $this->loadModel('Student');
            $passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
            $password = $passwordHasher->hash($this->request->data['User']['password']);
            $user = $this->Student->find('first', array('conditions' => array('Student.email' => $this->data['User']['email'], 'Student.password' => $password)));
            if ($user != false) {
                if ($user['Student']['status'] == "Active") {
                    $expiryDays = $user['Student']['expiry_days'];
                    if ($expiryDays > 0) {
                        $expiryDate = date('Y-m-d', strtotime($user['Student']['renewal_date'] . "+$expiryDays days"));
                        if ($this->currentDate > $expiryDate) {
                            $this->Session->setFlash(__('Your account has expired. Please contact administrator'), 'flash', array('alert' => 'danger'));
                            $this->redirect(array('action' => 'login'));
                            exit();
                        }
                    }
                    $recordArr = array('Student' => array('id' => $user['Student']['id'], 'last_login' => $this->currentDateTime));
                    $this->Student->save($recordArr);
                    $this->Session->setFlash(__('You have been logged in successfully'), 'flash', array('alert' => 'success'));
                    $this->Session->write('Student', $user);
                    $this->redirect(array('controller' => 'Dashboards', 'action' => 'index'));
                    exit();
                } elseif ($user['Student']['status'] == "Pending" && $user['Student']['reg_status'] == "Live") {
                    $this->Session->setFlash(__('Your email not verified! Please click on link sent to your email inbox or spam'), 'flash', array('alert' => 'danger'));
                    $this->redirect(array('action' => 'login'));
                    exit();
                } else {
                    $status = __($user['Student']['status']);
                    $this->Session->setFlash(__('You are %s Member! Please contact administrator', $status), 'flash', array('alert' => 'danger'));
                    $this->redirect(array('action' => 'login'));
                    exit();
                }
            } else {
                $this->Session->setFlash(__('Incorrect username/password'), 'flash', array('alert' => 'danger'));
                $this->redirect(array('action' => 'login'));
                exit();
            }
        }
    }

    public function crm_logout()
    {
        $this->authenticate();
        $this->Session->destroy();
        $this->Session->setFlash(__('You have been logged out successfully'), 'flash', array('alert' => 'success'));
        $this->redirect(array('action' => 'login'));
        exit();
    }

    public function rest_login()
    {
        $message = __('Invalid Post');
        $status = false;
        $publicKey = null;
        $privateKey = null;
        $studentPhoto = null;
        $accountStatus = "Active";
        $user = (object)array();
        $sysSetting = (object)array();
        $currency = null;
        $currencyCode = null;
        if ($this->request->is('post')) {
            if (isset($this->request->data['email']) && isset($this->request->data['password'])) {
                $this->loadModel('Student');
                $passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
                $password = $passwordHasher->hash($this->request->data['password']);
                $user = $this->Student->find('first', array('conditions' => array('Student.email' => $this->request->data['email'], 'Student.password' => $password)));
                if ($user != false) {
                    if ($user['Student']['status'] == "Active") {
                        $expiryDays = $user['Student']['expiry_days'];
                        if ($expiryDays > 0) {
                            $expiryDate = date('Y-m-d', strtotime($user['Student']['renewal_date'] . "+$expiryDays days"));
                            if ($this->currentDate > $expiryDate) {
                                $message = __('Your account has expired. Please contact administrator');
                                $status = false;
                            }
                        } else {
                            $publicKey = $this->CustomFunction->generate_rand(15);
                            $privateKey = $passwordHasher->hash($publicKey . time());
                            $recordArr = array('Student' => array('id' => $user['Student']['id'], 'public_key' => $publicKey, 'private_key' => $privateKey, 'last_login' => $this->currentDateTime));
                            $this->Student->save($recordArr);
                            $message = __('You have been logged in successfully');
                            $status = true;
                            if ($user['Student']['photo'] != null) {
                                $studentPhoto = $this->siteDomain . '/img' . '/student_thumb/' . $user['Student']['photo'];
                            }
                            $sysSetting = $this->sysSetting;
                            $currency = $this->siteDomain . '/img' . '/currencies/' . $this->currencyArr['Currency']['photo'];
                            $currencyCode = $this->currencyType;
                        }
                    } elseif ($user['Student']['status'] == "Pending" && $user['Student']['reg_status'] == "Live") {
                        $message = __('Your email not verified! Please click on link sent to your email inbox or spam');
                        $status = true;
                        $accountStatus = "Pending";
                    } else {
                        $studentStatus = __($user['Student']['status']);
                        $message = __('You are %s Member! Please contact administrator', $studentStatus);
                        $status = false;
                    }
                } else {
                    $user = (object)array();
                    $message = __('Incorrect username/password');
                    $status = false;
                }
            } else {
                $message = __('Email & Password can not empty.');
                $status = false;
            }
        } else {
            $message = __('GET request not allowed!');
            $status = false;
        }
        $this->set('public_key', $publicKey);
        $this->set('private_key', $privateKey);
        $this->set(compact('status', 'message', 'studentPhoto', 'user', 'accountStatus', 'sysSetting', 'currency', 'currencyCode'));
        $this->set('_serialize', array('status', 'message', 'public_key', 'private_key', 'studentPhoto', 'user', 'accountStatus', 'sysSetting', 'currency', 'currencyCode'));
    }
}