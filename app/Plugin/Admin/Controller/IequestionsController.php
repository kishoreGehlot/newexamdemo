<?php
ini_set('max_execution_time', 300);
class IequestionsController extends AdminAppController {
    public $helpers = array('Html', 'Form','Session');
    public $components = array('Session','PhpExcel.PhpExcel');
    
    public function index()
    {
        try
        {
            $this->loadModel('Subject');
            $this->loadModel('Group');
            $this->set('subject_id', $this->Subject->find('list',array('fields'=>array('id','subject_name'),
                                                                       'joins'=>array(array('table'=>'subject_groups','type'=>'INNER','alias'=>'SubjectGroup','conditions'=>array('Subject.id=SubjectGroup.subject_id'))),
                                                                       'conditions'=>array("SubjectGroup.group_id IN($this->userGroupWiseId)"))));
            $this->set('group_id', $this->Group->find('list',array('fields'=>array('id','group_name'),'conditions'=>array("Group.id IN($this->userGroupWiseId)"))));
        }
        catch (Exception $e)
        {
            $this->Session->setFlash($e->getMessage(),'flash',array('alert'=>'danger'));
        }
    }
    public function exportquestions()
    {
        try
        {
            $this->loadModel('Subject');
            $this->set('subject_id', $this->Subject->find('list',array('fields'=>array('id','subject_name'),
                                                                       'joins'=>array(array('table'=>'subject_groups','type'=>'INNER','alias'=>'SubjectGroup','conditions'=>array('Subject.id=SubjectGroup.subject_id'))),
                                                                       'conditions'=>array("SubjectGroup.group_id IN($this->userGroupWiseId)"))));
            
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
                if(is_array($this->request->data['QuestionGroup']['group_name']))
                {
                    if(strlen($this->request->data['Iequestion']['subject_id'])>0)
                    {
                        $fixed = array('subject_id'=>$this->request->data['Iequestion']['subject_id']);
                        $groupName=$this->request->data['QuestionGroup']['group_name'];
                        $filename = null;$extension=null;
                        $extension = pathinfo($this->request->data['Iequestion']['file']['name'],PATHINFO_EXTENSION);
                        if($extension=="xls")
                        {
                            if (!empty($this->request->data['Iequestion']['file']['tmp_name']) && is_uploaded_file($this->request->data['Iequestion']['file']['tmp_name']))
                            {
                                $filename = basename($this->request->data['Iequestion']['file']['name']);
                                $tmpPath=APP . DS . 'tmp' . DS . 'xls' . DS . $filename;
                                move_uploaded_file($this->data['Iequestion']['file']['tmp_name'],APP . DS . 'tmp' . DS . 'xls' . DS . $filename);
                                $this->PhpExcel->loadWorksheet();
                                $rowData=$this->PhpExcel->importData('Excel5',$tmpPath);
                                if($this->importInsert($rowData,$groupName,$fixed))
                                {
                                    if(file_exists($tmpPath))
                                    unlink($tmpPath);  
                                    $this->Session->setFlash(__('Questions imported successfully'),'flash',array('alert'=>'success'));
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
                        $this->Session->setFlash(__('Please Select Subject'),'flash',array('alert'=>'danger'));
                        return $this->redirect(array('action' => 'index'));
                    }
                }
                else
                {
                    $this->Session->setFlash(__('Please Select any Group'),'flash',array('alert'=>'danger'));
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
        
        foreach($rowData as $dataValue)
        {
            $dataValue=array_shift($dataValue);
            
            if($dataValue[0]=="E")
            $dataValue[0]=1;
            elseif($dataValue[0]=="M")
            $dataValue[0]=2;
            elseif($dataValue[0]=="H")
            $dataValue[0]=3;					
            else
            $dataValue[0]=1;
            
            if($dataValue[1]=="M")
            $dataValue[1]=1;
            elseif($dataValue[1]=="T")
            $dataValue[1]=2;
            elseif($dataValue[1]=="F")
            $dataValue[1]=3;
            elseif($dataValue[1]=="S")
            $dataValue[1]=4;
            else
            $dataValue[1]=1;
            
            if(isset($dataValue[16]))
            {
                $id=$dataValue[16];
            }else{
              $id=null;  
            }
            
            $recordArr=array('id'=>$id,'diff_id'=>$dataValue[0],'qtype_id'=>$dataValue[1],'question'=>$dataValue[2],'option1'=>$dataValue[3],'option2'=>$dataValue[4],
                                      'option3'=>$dataValue[5],'option4'=>$dataValue[6],'option5'=>$dataValue[7],'option6'=>$dataValue[8],'marks'=>$dataValue[9],
                                      'negative_marks'=>$dataValue[10],'hint'=>$dataValue[11],'explanation'=>$dataValue[12],'answer'=>$dataValue[13],'true_false'=>$dataValue[14],'fill_blank'=>$dataValue[15]);           
            
            $recordArr=Set::merge($recordArr,$fixed);
            if($this->Iequestion->save($recordArr))
            {
                $this->loadModel('QuestionGroup');
                $questionId=$this->Iequestion->id;
                if($id!=null)
                $this->QuestionGroup->deleteAll(array('QuestionGroup.question_id'=>$questionId));
                $QuestionGroup=array();
                foreach($groupArr as $groupId)
                {
                    $QuestionGroup[]=array('question_id'=>$questionId,'group_id'=>$groupId);                       
                }
                $this->QuestionGroup->create();
                $this->QuestionGroup->saveAll($QuestionGroup);                
            }
            else
            {
                $this->Iequestion->rollback('Iequestion');
                $this->QuestionGroup->rollback('QuestionGroup');
                return false;
            }
            $this->Iequestion->commit();
            $this->QuestionGroup->commit();
        }
        return true;
    }
    public function export()
    {
        $this->layout=null;
        $this->autoRender=false;        
        try
        {
            if(strlen($this->request->data['Iequestion']['subject_id'])==0)
            {
                $this->Session->setFlash('Invalid Post!','flash',array('alert'=>'danger'));
                return $this->redirect(array('action' => 'exportquestions'));  
            }
            $data=$this->exportData($this->request->data['Iequestion']['subject_id']);
            $this->PhpExcel->createWorksheet();
            $this->PhpExcel->addTableRow($data);
            $this->PhpExcel->output('Question',$this->siteName,'question.xls','Excel2007');
        }
        catch (Exception $e)
        {
            $this->Session->setFlash($e->getMessage(),'flash',array('alert'=>'danger'));
            return $this->redirect(array('action' => 'index'));
        }
    }
    private function exportData($subjectId)
    {
        try
        {
            $this->Iequestion->UserWiseGroup($this->userGroupWiseId);
            $post=$this->Iequestion->find('all',array('joins'=>array(array('table'=>'question_groups','type'=>'INNER','alias'=>'QuestionGroup','conditions'=>array('Iequestion.id=QuestionGroup.question_id')),
                                                                     array('table'=>'user_groups','type'=>'INNER','alias'=>'UserGroup','conditions'=>array('QuestionGroup.group_id=UserGroup.group_id'))),
                                                      'conditions'=>array('UserGroup.user_id'=>$this->luserId,'subject_id'=>$subjectId),
                                                      'group'=>array('Iequestion.id')));
            $data=$this->showQuestionData($post);
            return $data;
        }
        catch (Exception $e)
        {
            $this->Session->setFlash($e->getMessage(),'flash',array('alert'=>'danger'));
            return $this->redirect(array('action' => 'index'));
        }
    
    }
    function showQuestionData($post)
    {
        $showData=array(array('Difficulty Level','Question Type','Question','Option1','option2','option3',
                                                  'Option4','Option5','Option6','Marks','Negative Marks','Hint','Explanation','Correct Answer','True & False','Fill in the blanks','Id','Groups','Subject'));
        foreach($post as $rank=>$value)
        {
            $showData[]=array('diff_id'=>$value['Diff']['type'],
                          'qtype_id'=>$value['Qtype']['type'],
                          'question'=>$value['Iequestion']['question'],'option1'=>$value['Iequestion']['option1'],'option2'=>$value['Iequestion']['option2'],'option3'=>$value['Iequestion']['option3'],
                                             'option4'=>$value['Iequestion']['option4'],'option5'=>$value['Iequestion']['option5'],'option6'=>$value['Iequestion']['option6'],
                                             'marks'=>$value['Iequestion']['marks'],'negative_marks'=>$value['Iequestion']['negative_marks'],'hint'=>$value['Iequestion']['hint'],
                                             'explanation'=>$value['Iequestion']['explanation'],'answer'=>$value['Iequestion']['answer'],'true_false'=>$value['Iequestion']['true_false'],
                                             'fill_blank'=>$value['Iequestion']['fill_blank'],'id'=>$value['Iequestion']['id'],
                                             'groups'=>$this->CustomFunction->showGroupName($value['Group']),
                          'subject'=>$value['Subject']['subject_name']);
        }
        return$showData;
    }
    public function download()
    {
        $this->viewClass = 'Media';
        $params = array(
            'id'        => 'sample-question.xls',
            'name'      => 'SampleQuestion',
            'download'  => true,
            'extension' => 'xls',
            'mimeType'  => array('xls' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            'path'      => APP . 'tmp' . DS.'download'.DS
        );
        $this->set($params);
    }
}
