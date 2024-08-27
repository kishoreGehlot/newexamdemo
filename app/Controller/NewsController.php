<?php
class NewsController extends AppController {
    public function show($id=null)
    {
        if (!$id)
        {
            $this->Session->setFlash(__('Invalid Post'),'flash',array('alert'=>'danger'));
            $this->redirect(array('controller'=>'pages','action' => 'home'));
        }
        $checkPost=$this->News->findByPageUrl($id);
        if(!$checkPost)
        {
            $this->Session->setFlash(__('Invalid Post'),'flash',array('alert'=>'danger'));
            $this->redirect(array('controller'=>'pages','action' => 'home'));
        }
        $this->set('newsPost',$checkPost);
        $this->set('news',$this->News->find('all',array('conditions'=>array('status'=>'Active'),'order'=>array('id'=>'desc'),'limit'=>15)));
    }
}
