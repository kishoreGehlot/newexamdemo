<?php
App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');

class AdminAppController extends AppController
{
    public $helpers = array('Html', 'Form','Session','Paginator','MenuBuilder.MenuBuilder');
    public $components = array('Session','Paginator');
    public function authenticate()
    {
        // Check if the session variable User exists, redirect to loginform if not
        if(!$this->Session->check('User'))
        {
            $this->redirect(array('controller' => 'users', 'action' => 'login_form'));
            exit();
        }
    } 
    public function beforeFilter()
    {
        parent::beforeFilter();
        $currAction=strtolower($this->action);
        $currController=strtolower($this->params['controller']);
        if($currController=="admin")
        {
            $this->redirect(array('controller' => 'dashboards', 'action' => 'index'));
            exit();
        }
        if($currAction!='login_form' && $currController!='forgots')
        {
            $this->authenticate();
        }
        if($currAction!='login_form' && $currAction!='myprofile' && $currController!='eldialogs' && $currAction!='changepass' && $currAction!='logout' && $currController!='forgots')
        {
            $this->userPermission();
        }
        $menu=array();
        $menuArr=$this->userMenu();
        $menu=$this->convertToAdminMenu($menuArr, 'Page');
        if($menu){
            $menu=array('main-menu' =>call_user_func_array('array_merge',$menu));
        }
        $this->set(compact('menu'));
        $this->loadModel('Mail');
        if($this->adminValue['User']['ugroup_id']==1)
        $this->userStatus=false;
        else
        $this->userStatus=true;
        $this->luserId=$this->adminValue['User']['id'];
        $this->userGroupWiseId=$this->userGroup();
    }
    function convertToAdminMenu($arr, $elmkey) {
        $menu=array();
        foreach ($arr as $menuValue) {
                if (!empty($menuValue['children'])) {
                        $menu[]=array(array('title' =>'<i class="'.$menuValue[$elmkey]['icon'].'"></i>&nbsp;<span class="submenu-title">'.__($menuValue[$elmkey]['page_name']).'</span>','url' => array('controller' => null),'children'=>call_user_func_array('array_merge',$this->convertToAdminMenu($menuValue['children'], $elmkey)),'selName'=>$menuValue[$elmkey]['sel_name'],'type'=>'Page','target'=>'_self'));
                } else {
                        $linkUrl=array('controller' => $menuValue[$elmkey]['controller_name'], 'action' => $menuValue[$elmkey]['action_name']);
                        $menu[]=array(array('title' =>'<i class="'.$menuValue[$elmkey]['icon'].'"></i>&nbsp;<span class="submenu-title">'.__($menuValue[$elmkey]['page_name']).'</span>','url' => $linkUrl,'selName'=>$menuValue[$elmkey]['sel_name'],'type'=>'Page','target'=>'_self'));
                }
        }
        return$menu;
    }
    public function userPermission()
    {
        $this->loadModel('Page');
        $isPermission=true;
        $UserArr=$this->Session->read('User');
        if($UserArr['User']['ugroup_id']!=1)
        {
            $userPermissionArr=$this->Page->find('first',array('joins'=>array(array
                                                                       ('table'=>'page_rights','alias'=>'PageRight','type'=>'Inner',
                                                                        'conditions'=>array('Page.id=PageRight.page_id'))),
                                                        'conditions'=>array('PageRight.ugroup_id'=>$UserArr['User']['ugroup_id'],'LOWER(Page.controller_name)'=>strtolower($this->params['controller'])),
                                                        'fields'=>array('Page.*','PageRight.*')));
            if(!isset($userPermissionArr['PageRight']['view_right']) || $userPermissionArr['PageRight']['view_right']==0)
            $isPermission=false;
            if($isPermission==false)
            {
                $this->Session->setFlash(__('No Permission'),'flash',array('alert'=>'danger'));
                $this->redirect(array('controller'=>'','action' => 'Dashboards'));
            }
        }
    }
    public function userMenu()
    {
        $UserArr=$this->Session->read('User');
        $this->loadModel('Page');
        if($UserArr['User']['ugroup_id']==1)
        {
            $menuArr=$this->Page->find('threaded',array('conditions'=>array('published'=>'Yes'),'order'=>array('ordering'=>'asc')));
        }
        else
        {
            $menuArr=$this->Page->find('threaded',array('joins'=>array(array
                                                                   ('table'=>'page_rights','alias'=>'PageRight','type'=>'Inner',
                                                                    'conditions'=>array('Page.id=PageRight.page_id'))),
                                                    'conditions'=>array('PageRight.ugroup_id'=>$UserArr['User']['ugroup_id'],'published'=>'Yes'),
                                                    'order'=>array('Page.ordering'=>'asc')));
        }
        return$menuArr;
    }
    public function userGroup()
    {
        $UserArr=$this->Session->read('User');
        $this->loadModel('UserGroup');
        $post=$this->UserGroup->find('all',array('fields'=>'UserGroup.group_id','conditions'=>array('UserGroup.user_id'=>$this->luserId)));
        $userGroupArr=array();
        foreach($post as $v)
        {
            $userGroupArr[]=implode(",",$v['UserGroup']);
        }
        $userGroupList="";
        if(is_array($userGroupArr))
        $userGroupList=implode(",",$userGroupArr);
        if(strlen($userGroupList)==0)
        $userGroupList="''";
        return$userGroupList;
    }
}
