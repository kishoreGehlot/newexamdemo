<?php
App::uses('CakeEmail', 'Network/Email');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class IestudentsController extends AdminAppController {
    public $helpers = array('Html', 'Form','Session');
    public $components = array('Session','PhpExcel.PhpExcel');
    
    public function index()
    {
        try
        {
            $this->loadModel('Group');
            $this->set('group_id', $this->Group->find('list',array('fields'=>array('id','group_name'),'conditions'=>array("Group.id IN($this->userGroupWiseId)"))));
        }
        catch (Exception $e)
        {
            $this->Session->setFlash($e->getMessage(),'flash',array('alert'=>'danger'));
        }
    }    
    public function import()
    {
        try
        {
            if ($this->request->is('post'))
            {
                if(is_array($this->request->data['StudentGroup']['group_name']))
                {
                    $groupName=$this->request->data['StudentGroup']['group_name'];
                    $filename = null;$extension=null;
                    $fixed=array();
                    $extension = pathinfo($this->request->data['Iestudent']['file']['name'],PATHINFO_EXTENSION);
                    if($extension=="xls")
                    {
                        if (!empty($this->request->data['Iestudent']['file']['tmp_name']) && is_uploaded_file($this->request->data['Iestudent']['file']['tmp_name']))
                        {
                            $this->loadModel('Emailsetting');
                            $this->loadModel('Emailtemplate');
                            $this->loadModel('Smstemplate');
			    $emailTemplateArr=$this->Emailtemplate->findByType('SLC');
                            $smsTemplateArr=$this->Smstemplate->findByType('SLC');                            
                            $emailSettingArr=$this->Emailsetting->findById(1);
                            $filename = basename($this->request->data['Iestudent']['file']['name']);
			    $tmpPath=APP . DS . 'tmp' . DS . 'xls' . DS . $filename;
                            move_uploaded_file($this->data['Iestudent']['file']['tmp_name'],$tmpPath);
                            $this->PhpExcel->loadWorksheet();
			    $rowData=$this->PhpExcel->importData('Excel5',$tmpPath);
			    if($this->importInsert($rowData,$groupName,$fixed))
			    {
				if(file_exists($tmpPath))
				unlink($tmpPath);  
				$this->Session->setFlash(__('Students imported successfully'),'flash',array('alert'=>'success'));
				return $this->redirect(array('action' => 'index'));
			    }
			    else
			    {
				if(file_exists($tmpPath))
				unlink($tmpPath);  
				$this->Session->setFlash(__('File not uploaded'),'flash',array('alert'=>'danger'));
				return $this->redirect(array('action' => 'index'));
			    }                   
                        }
                        else
                        {
                            $this->Session->setFlash(__('File not uploaded'),'flash',array('alert'=>'danger'));
                            return $this->redirect(array('action' => 'index'));
                        }
                    }
                    else
                    {
                        $this->Session->setFlash(__('Only XLS File supported'),'flash',array('alert'=>'danger'));
                        return $this->redirect(array('action' => 'index'));
                    }                    
                }
                else
                {
                    $this->Session->setFlash(__('Please Select Group'),'flash',array('alert'=>'danger'));
                    return $this->redirect(array('action' => 'index'));
                }
            }
        }
        catch (Exception $e)
        {
            $this->Session->setFlash($e->getMessage(),'flash',array('alert'=>'danger'));
            return $this->redirect(array('action' => 'index'));
        }
    }
    public function importInsert($rowData,$groupArr,$fixed)
    {
	$passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
        foreach($rowData as $dataValue)
        {
            $dataValue=array_shift($dataValue);
	    $studentName=$dataValue[0];
	    $email=$dataValue[1];
	    $passArr=array();$statusArr=array();
	    $password=__('Not Changed');
	    if($dataValue[2]!=null)
	    {
		$password=$dataValue[2];
		$passArr=array('password'=>$passwordHasher->hash($password));
	    }
	    $mobileNo=$dataValue[3];
	    if(isset($dataValue[10]))
            {
                $id=$dataValue[10];
            }
	    else
	    {
              $id=null;  
            }
	    if($dataValue[7]==__('Unlimited'))
	    $expiryDays=0;
	    else
	    $expiryDays=$dataValue[7];
	    $admissionDate=CakeTime::format('Y-m-d',$dataValue[8]);
	    if($dataValue[9]=="Active")
	    $statusArr=array('status'=>'Active','reg_status'=>'Done','renewal_date'=>$admissionDate);
	    $recordArr=array('id'=>$id,'name'=>$studentName,'email'=>$email,'phone'=>$mobileNo,'enroll'=>$dataValue[4],
                                      'guardian_phone'=>$dataValue[5],'address'=>$dataValue[6],'expiry_days'=>$expiryDays,'created'=>$admissionDate);
	    $recordArr=Set::merge($recordArr,$fixed,$passArr,$statusArr);
            $this->Iestudent->create();
            if($this->Iestudent->save($recordArr))
            {
		$this->loadModel('StudentGroup');
                $studentId=$this->Iestudent->id;
		if($id!=null)
                $this->StudentGroup->deleteAll(array('StudentGroup.student_id'=>$studentId));
                $StudentGroup=array();
                foreach($groupArr as $groupId)
                {
                    $StudentGroup[]=array('student_id'=>$studentId,'group_id'=>$groupId);                       
                }
                $this->StudentGroup->create();
                $this->StudentGroup->saveAll($StudentGroup);
		$siteName=$this->siteName;$siteEmailContact=$this->siteEmailContact;$url=$this->siteDomain;
		if($email)
		{
		    if($this->emailNotification)
		    {                          
			/* Send Email */
			$this->loadModel('Emailtemplate');
			$emailSettingArr=$this->Emailtemplate->findByType('SLC');
			if($emailSettingArr['Emailtemplate']['status']=="Published")
			{
			    $message=eval('return "' . addslashes($emailSettingArr['Emailtemplate']['description']) . '";');
			    $Email = new CakeEmail();
			    $Email->transport($this->emailSettype);
			    if($this->emailSettype=="Smtp")
			    $Email->config(array('host' => $this->emailHost,'port' =>  $this->emailPort,'username' => $this->emailUsername,'password' => $this->emailPassword,'timeout'=>90));
			    $Email->from(array($this->siteEmail =>$this->siteName));
			    $Email->to($email);
			    $Email->template('default');
			    $Email->emailFormat('html');
			    $Email->subject($emailSettingArr['Emailtemplate']['name']);
			    $Email->send($message);
			    /* End Email */
			}
		    }
		}
		if($this->smsNotification)
		{
		    /* Send Sms */
		    $this->loadModel('Smstemplate');
		    $smsTemplateArr=$this->Smstemplate->findByType('SLC');
		    if($smsTemplateArr['Smstemplate']['status']=="Published")
		    {
			$url="$this->siteDomain";
			$message=eval('return "' . addslashes($smsTemplateArr['Smstemplate']['description']) . '";');
			$this->CustomFunction->sendSms($mobileNo,$message,$this->smsSettingArr);
		    }
		    /* End Sms */
		}
            }
            else
            {
                $this->Iestudent->rollback('Iestudent');
                $this->StudentGroup->rollback('StudentGroup');
                return false;
            }
            $this->Iestudent->commit();
            $this->StudentGroup->commit();
        }
        return true;
    }
    public function export()
    {
	$this->layout=null;
        $this->autoRender=false;
        try
        {
            $data=$this->exportData();
            $this->PhpExcel->createWorksheet();
            $this->PhpExcel->addTableRow($data);
            $this->PhpExcel->output('Student',$this->siteName,'student.xls','Excel2007');
        }
        catch (Exception $e)
        {
            $this->Session->setFlash($e->getMessage(),'flash',array('alert'=>'danger'));
            return $this->redirect(array('action' => 'index'));
        }
    }    
    private function exportData()
    {
        try
        {
            $this->Iestudent->UserWiseGroup($this->userGroupWiseId);
            $post=$this->Iestudent->find('all',array('joins'=>array(array('table'=>'student_groups','type'=>'INNER','alias'=>'StudentGroup','conditions'=>array('Iestudent.id=StudentGroup.student_id')),
                                                              array('table'=>'user_groups','type'=>'INNER','alias'=>'UserGroup','conditions'=>array('StudentGroup.group_id=UserGroup.group_id'))),
                                         'conditions'=>array('UserGroup.user_id'=>$this->luserId),
                                         'group'=>array('Iestudent.id'),
                                         'order'=>array('Iestudent.name'=>'asc')));
            $data=$this->showStudentData($post);
            return $data;
        }
        catch (Exception $e)
        {
            $this->Session->setFlash($e->getMessage(),'flash',array('alert'=>'danger'));
            return $this->redirect(array('action' => 'index'));
        }
    }
    private function showStudentData($post)
    {
        $showData=array(array(__('Name'),__('Email'),__('Password'),__('Phone'),__('Enrolment Number'),__('Alternate Number'),__('Address'),__('Expiry Days'),__('Admission Date'),__('Status'),__('ID'),__('Groups')));
        foreach($post as $rank=>$value)
        {
	    if($value['Iestudent']['expiry_days']==0)
	    $expiryDays=__('Unlimited');
	    else
	    $expiryDays=$value['Iestudent']['expiry_days'];
            $showData[]=array('name'=>$value['Iestudent']['name'],'email'=>$value['Iestudent']['email'],'password'=>'','phone'=>$value['Iestudent']['phone'],
                              'enroll'=>$value['Iestudent']['enroll'],'guardian_phone'=>$value['Iestudent']['guardian_phone'],'address'=>$value['Iestudent']['address'],
                              'expiry_days'=>$expiryDays,'created'=>CakeTime::format($this->dtFormat,$value['Iestudent']['created']),'status'=>$value['Iestudent']['status'],'id'=>$value['Iestudent']['id'],'groups'=>$this->CustomFunction->showGroupName($value['Group']));
        }
        return$showData;
    }
    public function download()
    {
        $this->viewClass = 'Media';
        $params = array(
            'id'        => 'sample-student.xls',
            'name'      => 'SampleStudent',
            'download'  => true,
            'extension' => 'xls',
            'mimeType'  => array('xls' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            'path'      => APP . 'tmp' . DS.'download'.DS
        );
        $this->set($params);
    }
}
