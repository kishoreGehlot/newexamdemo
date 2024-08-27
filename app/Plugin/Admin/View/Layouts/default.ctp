<?php
/**
 *
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

?>
<!DOCTYPE html>
  <html lang="<?php echo$configLanguage;?>" dir="<?php echo$dirType;?>">
    <head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	   <meta name="author" content="<?php echo$siteName;?>" />
	  <meta name="google-translate-customization" content="839d71f7ff6044d0-328a2dc5159d6aa2-gd17de6447c9ba810-f">
	  <?php echo $this->Html->charset();?>
	  <title><?php echo $metaTitle;?></title>
	 <meta name="keyword" content="<?php echo$metaKeyword;?>"/>
	 <meta name="description" content="<?php echo$metaContent;?>"/>
	  <?php
	  echo $this->Html->meta('icon');?>
	  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Arimo:400,700,400italic" />
	  <?php
	  echo $this->Html->css('/design600/assets/css/linecons');
	  echo $this->Html->css('/design600/assets/css/font-awesome.min');
	  echo $this->Html->css('/design600/assets/css/bootstrap');
	  echo $this->Html->css('/design600/assets/css/core');
	  echo $this->Html->css('/design600/assets/css/forms');
	  echo $this->Html->css('/design600/assets/css/components');
	  echo $this->Html->css('/design600/assets/css/skins');
	  echo $this->Html->css('/design600/assets/css/custom');
	  echo $this->Html->css('style.css');
	  echo $this->Html->css('validationEngine.jquery');
	  echo $this->Html->css('bootstrap-multiselect');
	  echo $this->Html->css('bootstrap-datetimepicker.min');
	  echo $this->fetch('meta');		
	  echo $this->fetch('css');
	  echo $this->Html->script('/design600/assets/js/jquery-1.11.1.min');
	  echo $this->Html->script('jquery.validationEngine-en');
          echo $this->Html->script('jquery.validationEngine');
	  echo $this->Html->script('html5shiv');
	  echo $this->Html->script('respond.min');
	  echo $this->Html->script('/design600/assets/js/bootstrap.min');
	  echo $this->Html->script('/design600/assets/js/TweenMax.min');
	  echo $this->Html->script('/design600/assets/js/resizeable');
	  echo $this->Html->script('/design600/assets/js/joinable');
	  echo $this->Html->script('/design600/assets/js/api');
	  echo $this->Html->script('/design600/assets/js/toggles');
	  echo $this->Html->script('/design600/assets/js/widgets');
	  echo $this->Html->script('/design600/assets/js/globalize.min');
	  echo $this->Html->script('/design600/assets/js/toastr.min');
	  echo $this->Html->script('/design600/assets/js/custom');
	  echo $this->Html->script('bootstrap-multiselect');
	  echo $this->Html->script('moment-with-locales');
	  echo $this->Html->script('bootstrap-datetimepicker.min');      
	  echo $this->Html->script('waiting-dialog.min');
	  echo $this->Html->script('main.custom.min');
	  echo $this->Html->script("langs/$configLanguage");
	  if($mathEditor)echo $this->Html->script('http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=AM_HTMLorMML-full');
	  echo $this->fetch('script');
	  echo $this->Js->writeBuffer();
	  $UserArr=$this->Session->read('User');
if($mathEditor){?><script type="text/x-mathjax-config">MathJax.Hub.Config({extensions: ["tex2jax.js"],jax: ["input/TeX", "output/HTML-CSS"],tex2jax: {inlineMath: [["$", "$"],["\\(", "\\)"]]}});</script><?php }?>
<?php if($translate>0){?>
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<?php }?>
</head>
    <?php if($this->Session->check('User')){?>
     <body class="page-body skin-navy">
     <?php if($translate>0){?><div id="google_translate_element"></div><?php }?>
     <div class="page-container">
      <div class="sidebar-menu toggle-others fixed">
	<div class="sidebar-menu-inner">
	   <header class="logo-env"> <!-- logo -->
	    <div class="logo"><?php if(strlen($frontLogo)>0){echo $this->Html->link($this->Html->image($frontLogo,array('alt'=>$siteName,'class'=>'admin-dash-logo')),array('controller'=>''),array('class'=>'logo-expanded','escape'=>false));
	    echo $this->Html->link($this->Html->image($frontLogo,array('alt'=>$siteName,'class'=>'admin-dash-collapsed-logo')),array('controller'=>''),array('class'=>'logo-collapsed','escape'=>false));} else{echo$siteName; }?></div>
	    <div class="mobile-menu-toggle visible-xs">
	      <a href="#" data-toggle="user-info-menu"> <i class="fa-user-secret"></i> <span class="badge badge-success"><?php echo$totalInbox;?></span> </a> <a href="#" data-toggle="mobile-menu"> <i class="fa-bars"></i> </a></div>
	  </header>
	 <?php echo $this->MenuBuilder->build('main-menu',array('childrenClass' => null,'firstClass'=>null,'menuClass'=>'main-menu','childrenDropdown'=>null,'activeClass'=>'active opened'),$menu);?>
	</div>
      </div>
      <div class="main-content">
      <nav class="navbar user-info-navbar" role="navigation">
	<ul class="user-info-menu left-links list-inline list-unstyled">
	  <li class="hidden-sm hidden-xs"> <a href="#" data-toggle="sidebar"> <i class="fa-bars"></i> </a> </li>
	  <li class="dropdown hover-line"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="fa-envelope-o"></i> <span class="badge badge-green"><?php echo$totalInbox;?></span> </a> <ul class="dropdown-menu messages"><li>
	  <ul class="dropdown-menu-list list-unstyled ps-scrollbar">
	    <?php foreach($mailArr as $post):$id=$post['Mail']['id'];$url=$this->Html->url(array('controller'=>'Mails','action'=>'View',$id));?>
	    <?php echo($post['Mail']['type']=="Unread") ? "<li class=\"active\"><strong>" : "<li>";?><a href="#" onclick="show_modal('<?php echo$url;?>')"><?php echo h($post['Mail']['from_email']);?> - <?php echo h($post['Mail']['subject']);?><?php echo($post['Mail']['type']=="Unread") ? "</strong>" : "";?></a></li>
	    <?php endforeach;unset($post);?>
	  </ul>
	  </li>
	      <li class="external"><?php echo$this->Html->link('<span>'.__('All Messages').'</span> <i class="fa-link-ext"></i>',array('controller'=>'Mails','action'=>'index'),array('escape'=>false));?></li>
	  </ul> </li>
	</ul>
	<ul class="user-info-menu right-links list-inline list-unstyled">
	  <li class="dropdown user-profile"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-user"></i> <span><?php echo h($UserArr['User']['name']);?><i class="fa-angle-down"></i> </span> </a>
	    <ul class="dropdown-menu user-profile-menu list-unstyled">
	      <li><?php echo $this->Html->link('<i class="fa-user"></i>'.__('My Profile'),array('controller' => 'Users','action' => 'myProfile'),array('escape'=>false));?></li>
	      <li><?php echo $this->Html->link('<i class="fa-info"></i>'.__('Help'),array('controller' => 'Helps','action' => 'index'),array('escape'=>false));?></li>
	      <li><?php echo $this->Html->link('<i class="fa-cog"></i>'.__('Change Password'),array('controller' =>'Users','action' =>'changePass'),array('escape'=>false));?></li>
	      <li class="last"><?php echo $this->Html->link('<i class="fa fa-power-off"></i>'.__('Logout'),array('controller' =>'Users','action'=>'logout'),array('escape' => false));?></li>
	    </ul>
	  </li>
	</ul>
      </nav>
      <?php echo $this->fetch('content');?>      
    <!-- Main Footer -->
    <footer class="main-footer sticky footer-type-1">
      <div class="footer-inner">
	<div class="footer-text">&copy; <?php echo$this->Time->format('Y',time());?> <?php echo$siteName;?> <?php echo __('Powered by');?> <?php echo$this->Html->Link('Eduexpression.com','http://www.eduexpression.com',array('target'=>'_blank'));?></div>
	<div class="text-center"><strong><?php echo __('Date &amp; Time');?> </strong><span><?php echo $this->Time->format('d-m-Y h:i:s A',time());?></span></div>
	<div class="go-up"> <a href="#" rel="go-top"> <i class="fa-angle-up"></i> </a> </div>
      </div>
    </footer>
    </div>
  </div>
<div class="page-loading-overlay"> <div class="loader-2"></div> </div>
<?php }else{?>
<body class="page-body login-page login-light">
<div class="login-container">
<div align="center"><?php if(strlen($frontLogo)>0){echo$this->Html->link($this->Html->image($frontLogo,array('alt'=>$siteName,'class'=>'global-logo')),array('controller'=>'../'),array('escape'=>false));}
else{ echo$this->Html->link($this->Html->image('logo-website.fw.png',array('alt'=>'Edu Expression','align'=>'center','style'=>'max-width:200px;max-height:77px;')),array('controller'=>'../'),array('escape'=>false));}?></div>
<?php echo $this->fetch('content');?>
</div> <?php }?>
<div class="modal fade" id="targetModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-content"></div>
</div>
<div id="scriptUrl" style="display: none;"><?php echo $this->Html->url(array('crm'=>false,'controller'=>'../app','action'=>'webroot','img'));?></div>
</body>
</html>