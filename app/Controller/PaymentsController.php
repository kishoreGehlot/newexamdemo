<?php
App::uses('CakeTime', 'Utility');
App::uses('Paypal', 'Paypal.Lib');

class PaymentsController extends AppController
{
    public $currencyType;

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->studentId = $this->userValue['Student']['id'];
        $this->loadModel('PaypalConfig');
        $paySetting = $this->PaypalConfig->findById('1');
        if (strlen($paySetting['PaypalConfig']['username']) == 0 || strlen($paySetting['PaypalConfig']['password']) == 0 || strlen($paySetting['PaypalConfig']['signature']) == 0) {
            $this->Session->setFlash(__('Paypal Payment not set'), 'flash', array('alert' => 'danger'));
            $this->redirect(array('controller' => 'Dashboards', 'action' => 'index'));
        }
        if ($paySetting['PaypalConfig']['sandbox_mode'] == 1)
            $sandboxMode = true;
        else
            $sandboxMode = false;
        $this->Paypal = new Paypal(array(
            'sandboxMode' => $sandboxMode,
            'nvpUsername' => $paySetting['PaypalConfig']['username'],
            'nvpPassword' => $paySetting['PaypalConfig']['password'],
            'nvpSignature' => $paySetting['PaypalConfig']['signature']
        ));
    }

    public function crm_index($id = null)
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $this->layout = 'rest';
        }
        if (isset($this->request->query['public_key']) && isset($this->request->query['private_key'])) {
            if (!$this->authenticateRest($this->request->query)) {
                echo __('Invalid Token');
                die();
            } else {
                $this->loadModel('Student');
                $post = $this->Student->findByPublicKeyAndPrivateKey($this->request->query['public_key'], $this->request->query['private_key']);
                $this->Session->write('Student', $post);
            }
        } else {
            $this->authenticate();
        }
        if (isset($_REQUEST['token'])) {
            $this->Session->setFlash(__('Payment Cancel'), 'flash', array('alert' => 'danger'));
        }
    }

    public function crm_checkout()
    {
        $this->authenticate();
        $description = $this->request->data['Payment']['remarks'];
        $amount = $this->request->data['Payment']['amount'];
        if ($amount > 0) {
            $returnUrl = $this->siteDomain . '/crm/Payments/postpayment/';
            $cancelUrl = $this->siteDomain . '/crm/Payments/index/';
            $order = array(
                'description' => $description,
                'currency' => $this->currencyType,
                'return' => $returnUrl,
                'cancel' => $cancelUrl,
                'items' => array(
                    0 => array(
                        'name' => __('Wallet Payment'),
                        'tax' => 0.00,
                        'shipping' => 0.00,
                        'description' => $description,
                        'subtotal' => $amount,
                    ),
                )
            );
            try {
                $token = $this->Paypal->setExpressCheckout($order);
                $this->redirect($token);
            } catch (PaypalRedirectException $e) {
                $this->redirect($e->getMessage());
            } catch (Exception $e) {
                $this->Session->setFlash($e->getMessage(), 'flash', array('alert' => 'danger'));
                $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('Try again! Can not connect to paypal'), 'flash', array('alert' => 'danger'));
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__('Invalid Amount'), 'flash', array('alert' => 'danger'));
            $this->redirect(array('action' => 'index'));
        }
    }

    public function crm_postpayment($id = null)
    {
        $this->authenticate();
        if (isset($_REQUEST['token']) && isset($_REQUEST['PayerID'])) {
            $token = $_REQUEST['token'];
            try {
                $detailsArr = $this->Paypal->getExpressCheckoutDetails($token);
                if (is_array($detailsArr)) {
                    $amount = $detailsArr['AMT'];
                    $description = $detailsArr['DESC'];
                    $payerId = $_REQUEST['PayerID'];
                    if ($detailsArr['ACK'] == "Success") {
                        $order = array(
                            'description' => $description,
                            'currency' => $this->currencyType,
                            'return' => $this->siteDomain . '/crm/Payments/postpayment/',
                            'cancel' => $this->siteDomain . '/crm/Payments/index/',
                            'items' => array(
                                0 => array(
                                    'name' => __('Wallet Payment'),
                                    'tax' => 0.00,
                                    'shipping' => 0.00,
                                    'description' => $description,
                                    'subtotal' => $amount,
                                ),
                            )
                        );
                        try {
                            $paymentDetails = $this->Paypal->doExpressCheckoutPayment($order, $token, $payerId);
                            if (is_array($paymentDetails)) {
                                if ($paymentDetails['PAYMENTINFO_0_PAYMENTSTATUS'] == "Completed" && $paymentDetails['PAYMENTINFO_0_ACK'] == "Success") {
                                    $transactionId = $paymentDetails['PAYMENTINFO_0_TRANSACTIONID'];
                                    $total = $this->Payment->find('count', array('conditions' => array('Payment.transaction_id' => $transactionId)));
                                    if ($total == 0) {
                                        $record_arr = array('student_id' => $this->studentId, 'transaction_id' => $transactionId, 'amount' => $amount, 'remarks' => $description, 'status' => 'Approved');
                                        $this->Payment->save($record_arr);
                                        $this->CustomFunction->WalletInsert($this->studentId, $amount, "Added", $this->currentDateTime, "PG", $description);
                                        $this->Session->setFlash(__d('default', "Payment successfully! Amount %s added in your wallet ", $amount), 'flash', array('alert' => 'success'));
                                    } else {
                                        $this->Session->setFlash(__('Payment already done'), 'flash', array('alert' => 'danger'));
                                    }
                                }
                                if ($paymentDetails['PAYMENTINFO_0_PAYMENTSTATUS'] == "Pending" && $paymentDetails['PAYMENTINFO_0_ACK'] == "Success") {
                                    $transactionId = $paymentDetails['PAYMENTINFO_0_TRANSACTIONID'];
                                    $total = $this->Payment->find('count', array('conditions' => array('Payment.transaction_id' => $transactionId)));
                                    if ($total == 0) {
                                        $record_arr = array('student_id' => $this->studentId, 'transaction_id' => $transactionId, 'amount' => $amount, 'remarks' => $description, 'status' => 'Pending');
                                        $this->Payment->save($record_arr);
                                        $this->Session->setFlash(__d('default', "Payment pending! Amount %s added in your wallet automatically when approved by paypal", $amount), 'flash', array('alert' => 'success'));
                                    } else {
                                        $this->Session->setFlash(__('Payment already done'), 'flash', array('alert' => 'danger'));
                                    }
                                }
                            }
                        } catch (PaypalRedirectException $e) {
                            $this->redirect($e->getMessage());
                        } catch (Exception $e) {
                            $this->Session->setFlash($e->getMessage(), 'flash', array('alert' => 'danger'));
                        }
                    } else {
                        $this->Session->setFlash(__('Payment not done'), 'flash', array('alert' => 'danger'));
                    }
                }
            } catch (Exception $e) {
                $this->Session->setFlash($e->getMessage(), 'flash', array('alert' => 'danger'));
            }
        }
        $this->redirect(array('action' => 'index'));
    }
}