<li class="toggle-menu"><i class="fa icon_menu"></i></li>
<?php
foreach($frontmenuArr as $menuName=>$menu):
$menuIcon=$menu['icon'];
h($menuName);
if($this->params['controller']=="pages"){
    $this->params['controller']="";
}
$isMenu=true;
if($menuName=="Register" && $frontRegistration!=1){$isMenu=false;}?>
<li <?php if($isMenu==true){ echo (strtolower($this->params['controller'])==strtolower($menu['controller']))?"class=\"current-menu-item\"":"";?>>
<?php echo $this->Html->link("<span class=\"$menuIcon\"></span>&nbsp;$menuName",array('controller' => $menu['controller'],'action'=>$menu['action']),array('escape' => false));?></li>
<?php } endforeach;unset($menu);unset($menuName);unset($menuIcon);?>
<?php $contents=$this->Function->userMenu(0);
foreach($contents as $menu):
$menuName=h($menu['Content']['link_name']);
if($menu['Content']['is_parent']=="Y"){
  $menuName.='<span class="caret"></span>';
  $menuLink='javascript:void(0);';
}
else{
  if($menu['Content']['is_url']=="Internal"){
    $menuLink=array('controller' => 'Contents','action'=>'pages',$menu['Content']['page_url']);
  }
  else{
    $menuLink=str_replace("{siteUrl}",$siteDomain,$menu['Content']['url']);
  }
}
$liClass='class=""';
if(!isset($menuType)){
    $submenuStart='<ul class="submenu">';
$submenuEnd='</ul>';
if($menu['Content']['is_parent']=="Y"){
    $hrefClass='';
}
else{
    $hrefClass=null;
}
}
else{
    $submenuStart='<div id="'.$menu['Content']['page_url'].'">
    <ul class="submenu">';
    $submenuEnd='</ul>';
    if($menu['Content']['is_parent']=="Y"){
        $hrefClass='';
    }
    else{
        $hrefClass=null;
    }
}
?>
<li <?php if($menu['Content']['is_parent']=="Y"){echo$liClass;}?>><?php echo$this->Html->link($menuName,$menuLink,array('class'=>$hrefClass,'target'=>$menu['Content']['url_target'],'escape'=>false));
if($menu['Content']['is_parent']!="Y"){?></li><?php }?>
<?php if($menu['Content']['is_parent']=="Y"){echo$submenuStart;}?>
<?php $submenuArr=$this->Function->userMenu($menu['Content']['id']);
foreach($submenuArr as $subValue):
$menuName=h($subValue['Content']['link_name']);
if($subValue['Content']['is_parent']=="Y"){
  $menuName.='<span class="caret"></span>';
  $menuLink='javascript:void(0);';
}
else{
  if($subValue['Content']['is_url']=="Internal"){
    $menuLink=array('controller' => 'Contents','action'=>'pages',$subValue['Content']['page_url']);
  }
  else{
    $menuLink=str_replace("{siteUrl}",$siteDomain,$subValue['Content']['url']);
  }
}
?>
<li><?php echo$this->Html->link($menuName,$menuLink,array('target'=>$subValue['Content']['url_target'],'escape'=>false));?></li>
<?php endforeach;unset($subValue);
if($submenuArr){
    echo$submenuEnd;
    unset($submenuArr);
    echo '</li>';
}
endforeach;unset($menu);unset($menuName);?>