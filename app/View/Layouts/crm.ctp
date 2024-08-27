<!DOCTYPE html>
<html lang="<?php echo$configLanguage;?>" dir="<?php echo$dirType;?>">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="google-translate-customization" content="839d71f7ff6044d0-328a2dc5159d6aa2-gd17de6447c9ba810-f">
	<?php echo $this->Html->charset();?>
	<title><?php echo $metaTitle;?></title>
	<meta name="keyword" content="<?php echo$metaKeyword;?>"/>
	<meta name="description" content="<?php echo$metaContent;?>"/>
	<?php
		echo $this->Html->meta('icon');?>
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,400,300,700" />
		<?php
		echo $this->Html->css('/design500/assets/css/font-awesome.min');
		echo $this->Html->css('/design500/assets/css/bootstrap.min');
                echo $this->Html->css('/design500/assets/css/core');
		echo $this->Html->css('/design500/assets/css/system');
		echo $this->Html->css('/design500/assets/css/system-responsive');
		echo $this->Html->css('style.css');
                echo $this->Html->css('validationEngine.jquery');
		echo $this->Html->css('bootstrap-multiselect');
		if(strtolower($this->params['controller'])=="exams" && strtolower($this->params['action'])=="start")
		echo $this->Html->css('jquery.countdown');
		echo $this->fetch('meta');		
		echo $this->fetch('css');
                echo $this->Html->script('jquery-1.11.1.min');
		echo $this->Html->script('html5shiv');
                echo $this->Html->script('respond.min');                
                echo $this->Html->script('bootstrap.min');
                echo $this->Html->script('jquery.validationEngine-en');
                echo $this->Html->script('jquery.validationEngine');
		echo $this->Html->script('/design500/assets/js/jquery.metisMenu');
		echo $this->Html->script('/design500/assets/js/bootstrap-switch.min');
		echo $this->Html->script('/design500/assets/js/jquery.cookie');
		echo $this->Html->script('/design500/assets/js/core');
		echo $this->Html->script('/design500/assets/js/system-layout');
		echo $this->Html->script('/design500/assets/js/jquery-responsive');
		echo $this->Html->script('bootstrap-multiselect');
		echo $this->Html->script('waiting-dialog.min');
		echo $this->Html->script('custom.min');
		echo $this->Html->script("langs/$configLanguage");
		if($mathEditor)echo $this->Html->script('http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=AM_HTMLorMML-full');
		echo $this->fetch('script');
                echo $this->Js->writeBuffer();
		$UserArr=$userValue;
		if(strlen($UserArr['Student']['photo'])>0)
		$std_img='student_thumb/'.$UserArr['Student']['photo'];
		else
		$std_img='User.png';
if($mathEditor){?><script type="text/x-mathjax-config">MathJax.Hub.Config({extensions: ["tex2jax.js"],jax: ["input/TeX", "output/HTML-CSS"],tex2jax: {inlineMath: [["$", "$"],["\\(", "\\)"]]}});</script><?php }?>
<?php echo $this->Html->scriptBlock("jQuery(document).ready(function () {
    'use strict';
    JQueryResponsive.init();
    Layout.init();
});
",array('inline'=>true));?>
<?php if($translate>0){?>
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<?php }?>
  </head>
  <div id="google_translate_element"></div>
  <body class="sidebar-color-black font-source-sans-pro layout-sidebar-collapsed"><!--Modal Default-->
   <div class="fluid"><!--BEGIN TEMPLATE SETTING-->
    <div class="hidden-xs hidden-sm">
        <div id="template-setting">
            <div class="pull-right">
                    <div id="setting-sidebar-collapsed" data-on="success" data-off="default"
                         class="make-switch switch-small"><input type="checkbox" class="switch"/></div>
                </div>
                <div class="mbm clearfix"></div>
            </div>
        </div>
    </div>
    <!--END TEMPLATE SETTING--><!--BEGIN TOPBAR-->
    <div class="page-header-topbar">
        <nav id="topbar" role="navigation" class="navbar-default container pln prn">
            <div class="container-fluid pln prn">
                <div id="topbar-menu" class="navbar-collapse pln prn">
                    <ul class="nav navbar-nav logo-wrapper">                       
                        <li class="pull-left logo"><?php if(strlen($frontLogo)>0){?><?php echo $this->Html->link($this->Html->image($frontLogo,array('alt'=>$siteName,'class'=>'front-dash-logo')),array('controller'=>'../'),array('escape'=>false));} else{?>
		    <?php echo __('Welcome to %s Dashboard',$siteName);?>
		    <?php }?>
			  </li>
                    </ul>
                    <?php if($this->Session->check('Student')){?>
                    <ul class="nav navbar-nav navbar-right" style="color: #000;">
                         <li class="btn-menu-toggle">
                            <div id="menu-toggle" class="show-collapsed"><i class="fa fa-bars"></i></div>
                        </li>
			<li><?php echo$this->Html->link("<i class=\"fa fa-envelope\"></i><span class=\"badge badge-warning\">$totalInbox</span>",array('controller'=>'Mails','action'=>'index'),array('escape'=>false));?></li>
                        <li><?php if($frontExamPaid>0){echo __('Balance').': &nbsp;'.$currency.$walletBalance;}?></li>
			<li class="dropdown"><a data-toggle="dropdown" href="#" class="dropdown-toggle"><i
                                class="fa fa-user"></i>&nbsp;<span class="caret"></span></a>
                            <ul class="dropdown-menu dropdown-user pull-right">
                                <li>
                                    <div class="navbar-content">
                                        <div class="row">
					  <div class="col-md-4 col-xs-2">
					    <?php echo $this->Html->image($std_img, array('alt' => h($UserArr['Student']['name']),'class'=>'img-responsive img-circle'));?>
                                                <p class="text-center mtm"><?php echo$this->Html->link('<small>'.__('Set Avatar').'</small>',array('controller'=>'Profiles','action'=>'changePhoto'),array('class'=>'change-avatar','escape'=>false));?></p>
                                            </div>
                                             <div class="col-md-8 col-xs-8"><span class="text-danger"><?php echo h($UserArr['Student']['name']);?></span>
                                                <p class="text-muted small"><?php echo h($UserArr['Student']['email']);?></p>
                                                <div class="divider"></div>
						<?php echo $this->Html->link(__('My Profile'),array('controller' => 'Profiles','action' => 'index'),array('class'=>'btn btn-default btn-sm mrg'));?>
                                                </div>
                                        </div>
                                    </div>
                                    <div class="navbar-footer">
                                        <div class="navbar-footer-content">
                                            <div class="row">
                                                <div class="col-md-6 col-xs-6">
						<?php echo $this->Html->link('<span class="fa fa-cog"></span>&nbsp;'.__('Change Password'),array('controller' => 'Profiles','action' => 'changePass'),array('escape' => false,'class'=>'btn btn-primary btn-sm'));?>
						</div>
                                                <div class="col-md-6 col-xs-6">
						<?php echo $this->Html->link('<span class="fa fa-power-off"></span>&nbsp;'.__('Sign out'),array('controller' => 'Users', 'action' => 'logout'),array('escape' => false,'class'=>'btn btn-danger btn-sm'));?>
						</div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                       
                    </ul>
		    <?php }?>
                </div>
            </div>
        </nav>
    </div>
   <!--END TOPBAR-->
    <div id="setting-sidebar-collapsed"></div>
    <div id="wrapper"><!--BEGIN PAGE WRAPPER-->
        <div id="page-wrapper"><!--BEGIN SIDEBAR MAIN-->
	<?php if($this->Session->check('Student')){?>
            <div class="sidebar-main sidebar">
                <div class="sidebar-collapse sidebar-scroll">
                    <ul id="sidebar-main" class="nav">
	      <?php foreach($menuArr as $menuName=>$menu): $menuIcon=$menu['icon'];h($menuName);if($frontExamPaid && ($menuName=="Payment" || $menuName=="Transaction History")){?>
	      <li <?php echo (strtolower($this->params['controller'])==strtolower($menu['controller']))?"class=\"active\"":"";?>><?php echo $this->Html->link("<i class=\"icon- $menuIcon\"></i><span class=\"menu-title\">&nbsp;$menuName</span>",array('controller' => $menu['controller'],'action'=>$menu['action']),array('escape' => false));?></li><?php }else{
	      if($menuName!="Payment" && $menuName!="Transaction History"){?>
	      <li <?php echo (strtolower($this->params['controller'])==strtolower($menu['controller']))?"class=\"active\"":"";?>><?php echo $this->Html->link("<i class=\"icon- $menuIcon\"></i><span class=\"menu-title\">&nbsp;$menuName</span>",array('controller' => $menu['controller'],'action'=>$menu['action']),array('escape' => false));?></li><?php }}?>
	      <?php endforeach;unset($menu);unset($menuName);unset($menuIcon);?>    
	    </ul>
                </div>
            </div>
    <?php }?>
            <!--END SIDEBAR MAIN--><!--BEGIN PAGE CONTENT-->
            <div class="page-content"><!--BEGIN TITLE & BREADCRUMB PAGE-->
		<div class="box-content"><!--BEGIN CONTENT-->
                    <div class="content">
                        <div class="row">
                            <div class="col-md-12">
                    <?php echo $this->fetch('content'); ?>
			    </div>
			</div>
		    </div>
		</div>
	  </div>
	  <!--END PAGE CONTENT--></div>
        <!--END PAGE WRAPPER--></div>
<div id="footer">
        <div class="copyright"> <span><strong> <?php echo __('Powered by');?> </strong><?php echo$this->Html->Link('Eduexpression.com','http://www.eduexpression.com',array('target'=>'_blank'));?></span>
            <div class="pull-left"><?php echo __('Copyright &copy;');?> <?php echo$this->Time->format('Y',time());?><strong> <?php echo$siteName;?></strong>
	    </div><div class="text-center"><strong><?php echo __('Date &amp; Time');?> </strong><span><?php echo $this->Time->format('d-m-Y h:i:s A',time());?></span></div>
        </div>
    </div>
<div id="scriptUrl" style="display: none;"><?php echo $this->Html->url(array('crm'=>false,'controller'=>'app','action'=>'webroot','img'));?></div>
</body>
</html>