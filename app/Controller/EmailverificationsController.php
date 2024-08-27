<?php
App::uses('CakeEmail', 'Network/Email');

class EmailverificationsController extends AppController
{
    var $components = array('RequestHandler', 'CustomFunction');

    public function beforeFilter()
    {
        parent::beforeFilter();
        if ($this->frontRegistration == 0)
            return $this->redirect(array('controller' => '', 'action' => 'index'));
    }

    public function crm_index()
    {
        $this->layout = 'login';
        if ($this->request->is(array('post', 'put'))) {
            $id = $this->request->data['Emailverification']['vc'];
            $this->EmailVerify($id);
        }
    }

    public function crm_emailcode($id = null)
    {
        if ($id == null) {
            $this->redirect(array('action' => 'index'));
        }
        $this->EmailVerify($id);
    }

    public function EmailVerify($id)
    {
        $this->loadModel('Student');
        $post = $this->Student->findByRegCodeAndRegStatus($id, "Live");
        if ($post) {
            $this->loadModel('Configuration');
            $post1 = $this->Configuration->findById(1);
            $guest_login = $post1['Configuration']['guest_login'];
            if ($guest_login == 1)
                $this->request->data['Student']['reg_status'] = "Pending";
            else
                $this->request->data['Student']['status'] = "Active";
            $this->Student->id = $post['Student']['id'];
            $this->request->data['Student']['reg_code'] = "";
            $this->request->data['Student']['reg_status'] = "Done";
            if ($this->Student->save($this->request->data)) {
                $this->Session->setFlash(__('Email verified successfully'), 'flash', array('alert' => 'success'));
                $this->redirect(array('controller' => 'Users', 'action' => 'index'));
                exit();
            }
        } else {
            $this->Session->setFlash(__('Invalid Verification Code'), 'flash', array('alert' => 'danger'));
        }
        $this->redirect(array('action' => 'index'));
    }

    public function crm_resend()
    {
        $this->layout = 'login';
    }

    public function crm_resendsub()
    {
        $this->loadModel('Student');
        $id = $this->request->data['Emailverification']['email'];
        $post = $this->Student->findByEmail($id);
        if ($post) {
            if ($post['Student']['reg_status'] == "Live") {
                $studentName = $post['Student']['name'];
                $code = $post['Student']['reg_code'];
                $email = $post['Student']['email'];
                $mobileNo = $post['Student']['phone'];
                $rand1 = $this->CustomFunction->generate_rand(35);
                $rand2 = rand();
                $url = "$this->siteDomain/crm/Emailverifications/emailcode/$code/$rand1/$rand2";
                $siteName = $this->siteName;
                $siteEmailContact = $this->siteEmailContact;
                /* Send Email */
                if ($this->emailNotification) {
                    $this->loadModel('Emailtemplate');
                    $emailTemplateArr = $this->Emailtemplate->findByType('RVN');
                    if ($emailTemplateArr['Emailtemplate']['status'] == "Published") {
                        $message = eval('return "' . addslashes($emailTemplateArr['Emailtemplate']['description']) . '";');
                        $Email = new CakeEmail();
                        $Email->transport($this->emailSettype);
                        if ($this->emailSettype == "Smtp")
                            $Email->config(array('host' => $this->emailHost, 'port' => $this->emailPort, 'username' => $this->emailUsername, 'password' => $this->emailPassword, 'timeout' => 90));
                        $Email->from(array($this->siteEmail => $this->siteName));
                        $Email->to($email);
                        $Email->template('default');
                        $Email->emailFormat('html');
                        $Email->subject($emailTemplateArr['Emailtemplate']['name']);
                        $Email->send($message);
                    }
                    /* End Email */
                }
                if ($this->smsNotification) {
                    /* Send Sms */
                    $this->loadModel('Smstemplate');
                    $smsTemplateArr = $this->Smstemplate->findByType('RVN');
                    if ($smsTemplateArr['Smstemplate']['status'] == "Published") {
                        $url = "$this->siteDomain";
                        $message = eval('return "' . addslashes($smsTemplateArr['Smstemplate']['description']) . '";');
                        $this->CustomFunction->sendSms($mobileNo, $message, $this->smsSettingArr);
                    }
                    /* End Sms */
                }
                $this->Session->setFlash(__('A verification Code send to your Email inbox or Spam'), 'flash', array('alert' => 'success'));
                $this->redirect(array('controller' => 'Emailverifications', 'action' => 'index'));
                exit();
            } else {
                $this->Session->setFlash(__('Email Id already verified'), 'flash', array('alert' => 'success'));
                $this->redirect(array('controller' => 'Users', 'action' => 'index'));
                exit();
            }
        } else {
            $this->Session->setFlash(__('Email Id not registered in system'), 'flash', array('alert' => 'danger'));
        }
        $this->redirect(array('action' => 'resend'));
    }

    public function rest_emailVerify()
    {
        $message = __('Invalid Post');
        $status = false;
        $accountStatus = "Pending";
        $publicKey = null;
        $privateKey = null;
        $studentPhoto = null;
        $user = (object)array();
        $sysSetting = (object)array();
        $currency = null;
        $currencyCode = null;
        if ($this->request->is('post')) {
            if (isset($this->request->data['verification_code'])) {
                $this->loadModel('Student');
                $post = $this->Student->findByRegCodeAndRegStatus($this->request->data['verification_code'], "Live");
                if ($post) {
                    $this->loadModel('Configuration');
                    $post1 = $this->Configuration->findById(1);
                    $guestLogin = $post1['Configuration']['guest_login'];
                    if ($guestLogin == 1) {
                        $recordArr['Student']['reg_status'] = "Pending";
                    } else {
                        $recordArr['Student']['status'] = "Active";
                        $user = $post;
                        if ($user['Student']['photo'] != null) {
                            $studentPhoto = $this->siteDomain . '/img' . '/student_thumb/' . $user['Student']['photo'];
                        }
                        $passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
                        $publicKey = $this->CustomFunction->generate_rand(15);
                        $privateKey = $passwordHasher->hash($publicKey . time());
                        $sysSetting = $this->sysSetting;
                        $currency = $this->siteDomain . '/img' . '/currencies/' . $this->currencyArr['Currency']['photo'];
                        $currencyCode = $this->currencyType;
                    }
                    $this->Student->id = $post['Student']['id'];
                    $recordArr['Student']['reg_code'] = "";
                    $recordArr['Student']['reg_status'] = "Done";
                    $recordArr['Student']['public_key'] = $publicKey;
                    $recordArr['Student']['private_key'] = $privateKey;
                    if ($this->Student->save($recordArr)) {
                        if ($guestLogin == 1) {
                            $message = __('Email verified successfully. Please wait for admin approval to activate account.');
                        } else {
                            $message = __('Email verified successfully');
                            $accountStatus = "Active";
                        }
                        $status = true;
                    } else {
                        $message = __('The email could not be verified. Please, try again.');
                    }
                } else {
                    $message = __('Invalid Verification Code');
                }
            } else {
                $message = __('Verification code can not not empty.');
            }
        } else {
            $message = __('GET request not allowed!');
        }
        $this->set('public_key', $publicKey);
        $this->set('private_key', $privateKey);
        $this->set(compact('status', 'message', 'accountStatus', 'user', 'studentPhoto', 'sysSetting', 'currency', 'currencyCode'));
        $this->set('_serialize', array('status', 'message', 'accountStatus', 'user', 'public_key', 'private_key', 'studentPhoto', 'sysSetting', 'currency', 'currencyCode'));
    }

    public function rest_resendEmail()
    {
        $message = __('Invalid Post');
        $status = false;
        if ($this->request->is('post')) {
            if (isset($this->request->data['email'])) {
                $this->loadModel('Student');
                $id = $this->request->data['email'];
                $post = $this->Student->findByEmail($id);
                if ($post) {
                    if ($post['Student']['reg_status'] == "Live") {
                        $studentName = $post['Student']['name'];
                        $code = $post['Student']['reg_code'];
                        $email = $post['Student']['email'];
                        $mobileNo = $post['Student']['phone'];
                        $rand1 = $this->CustomFunction->generate_rand(35);
                        $rand2 = rand();
                        $url = "$this->siteDomain/crm/Emailverifications/emailcode/$code/$rand1/$rand2";
                        $siteName = $this->siteName;
                        $siteEmailContact = $this->siteEmailContact;
                        /* Send Email */
                        if ($this->emailNotification) {
                            $this->loadModel('Emailtemplate');
                            $emailTemplateArr = $this->Emailtemplate->findByType('RVN');
                            if ($emailTemplateArr['Emailtemplate']['status'] == "Published") {
                                $message = eval('return "' . addslashes($emailTemplateArr['Emailtemplate']['description']) . '";');
                                $Email = new CakeEmail();
                                $Email->transport($this->emailSettype);
                                if ($this->emailSettype == "Smtp")
                                    $Email->config(array('host' => $this->emailHost, 'port' => $this->emailPort, 'username' => $this->emailUsername, 'password' => $this->emailPassword, 'timeout' => 90));
                                $Email->from(array($this->siteEmail => $this->siteName));
                                $Email->to($email);
                                $Email->template('default');
                                $Email->emailFormat('html');
                                $Email->subject($emailTemplateArr['Emailtemplate']['name']);
                                $Email->send($message);
                            }
                            /* End Email */
                        }
                        if ($this->smsNotification) {
                            /* Send Sms */
                            $this->loadModel('Smstemplate');
                            $smsTemplateArr = $this->Smstemplate->findByType('RVN');
                            if ($smsTemplateArr['Smstemplate']['status'] == "Published") {
                                $url = "$this->siteDomain";
                                $message = eval('return "' . addslashes($smsTemplateArr['Smstemplate']['description']) . '";');
                                $this->CustomFunction->sendSms($mobileNo, $message, $this->smsSettingArr);
                            }
                            /* End Sms */
                        }
                        $message = __('A verification Code send to your Email inbox or Spam');
                        $status = true;
                    } else {
                        $message = __('Email Id already verified');
                    }
                } else {
                    $message = __('Email Id not registered in system');
                }
            } else {
                $message = __('Email can not not empty.');
            }
        } else {
            $message = __('GET request not allowed!');
        }
        $this->set(compact('status', 'message'));
        $this->set('_serialize', array('status', 'message'));
    }
}
