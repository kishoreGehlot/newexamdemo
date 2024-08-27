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
		echo $this->Html->css('/design300/css/font-awesome.min');
		echo $this->Html->css('/design300/css/bootstrap.min');
		echo $this->Html->css('/design300/css/font');
		echo $this->Html->css('/design300/css/settings');
		echo $this->Html->css('/design300/css/style');
		echo $this->Html->css('validationEngine.jquery');
		echo $this->Html->css('bootstrap-multiselect');
		echo $this->Html->css('style');
		echo $this->fetch('meta');		
		echo $this->fetch('css');
		echo $this->Html->script('/design300/js/jquery.min');
		echo $this->Html->script('html5shiv');
                echo $this->Html->script('respond.min');
		echo $this->Html->script('jquery.validationEngine-en');
                echo $this->Html->script('jquery.validationEngine');		
		echo $this->Html->script('/design300/js/bootstrap.min');
		echo $this->Html->script('/design300/js/rs-slider');
		echo $this->Html->script('bootstrap-multiselect');
		echo $this->Html->script('waiting-dialog.min');
		echo $this->Html->script("langs/$configLanguage");
		echo $this->Html->script('custom.min');
                if($mathEditor)echo $this->Html->script('http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=AM_HTMLorMML-full');
		if($this->params['controller']=="pages"){$this->params['controller']="";}
if($mathEditor){?><script type="text/x-mathjax-config">MathJax.Hub.Config({extensions: ["tex2jax.js"],jax: ["input/TeX", "output/HTML-CSS"],tex2jax: {inlineMath: [["$", "$"],["\\(", "\\)"]]}});</script><?php }?>
<?php if($translate>0){?>
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<?php }?>
</head>
<body>
<div id="preloader">
<div id="status">&nbsp;</div>
</div>
<div id="wrapper">
<div class="h-wrapper">
 
<div class="topbar">
<div class="container">
<div class="row">
<div class="col-sm-6">
  
</div>
<div class="col-sm-6">
<div class="pull-right hidden-xs">
<ul class="social-icon unstyled">
 <?php if(strlen($contact[0])>0){?>
	    <li><a href="#"><i class="fa fa-phone"></i><span><?php echo$contact[0];?></span></a></li><?php }?>
	    <?php if(strlen($contact[1])>0){?>
	    <li><a href="mailto:<?php echo$contact[1];?>"><i class="fa fa-envelope"></i><span><?php echo$contact[1];?></span></a></li><?php }?>
	    <?php if(strlen($contact[2])>0){?>
	    <li><a href="<?php echo$contact[2];?>" target="_blank"><i class="fa fa-facebook"></i><span><?php echo __('follow on facebook');?></span></a></li><?php }?>
</ul>
</div>
</div>
</div>
</div>
</div>
<?php if($this->params['controller']==""){?>
<header class="header-wrapper header-transparent with-topbar">
 <?php }else {?>
<header class="header-wrapper header-transparent header-stylecol with-topbar">
 <?php }?>
<div class="main-header">
<div class="container">
<div class="row">
<div class="col-sm-12 col-md-3">
  <div class="logo-text"><?php if(strlen($frontLogo)>0){?><?php echo $this->Html->link($this->Html->image($frontLogo,array('alt'=>$siteName,'class'=>'img-responsive front-logo')),array('controller'=>'/'),array('escape'=>false));} else{?><?php echo$siteName;?><?php }?></div></div>
<div class="col-sm-12 col-md-9" >
<nav class="navbar-right">
<?php echo $this->MenuBuilder->build('frontMenu',array('childrenClass' => '','firstClass'=>'','menuClass'=>'menu','childrenDropdown'=>'submenu','activeClass'=>'active','wrapperFormat'=>'<ul %s><li class="toggle-menu"><i class="fa icon_menu"></i></li>%s</ul>'),$frontMenu);?>
</nav>
</nav>
</div>
</div>
</div>  
</div>  
</header>
</div>
<div class="push-top"></div>
<div class="tp-banner-container rs_fullwidth">
<?php if($frontSlides==1 && $this->params['controller']==""){?>
<div class="tp-banner">
			  <ul>
			  <?php foreach($slides as $k=>$value): $photoImg='slides_thumb/'.$value['Slide']['photo'];?>
			  <li data-transition="fade" data-masterspeed="500" data-slotamount="7" data-delay="8000" data-title="<?php echo$value['Slide']['slide_name'];?>">
			  <?php echo $this->Html->image($photoImg,array('alt'=>$value['Slide']['slide_name'],'width'=>'1140'));?>
			  <div class="bg-overlay op5"></div>
			  </li>
			  <?php endforeach;unset($k);unset($value);?>
			  </ul>
	</div>

</div>
		<?php }?>
 <?php if($this->params['controller']!=""){?>
<section class="section mt80"><?php }else{?>
  <section class="section"><?php }?>
<div class="container">
<?php echo $this->fetch('content');?>
<?php if($this->params['controller']!=""){?>
<div class="col-sm-12 col-md-3 sm-box2">
<div class="box-services-b">
<div class="box-left"><i class="fa fa-bullhorn"></i></div>
<div class="box-right-all">
  <h3 class="title-small "> <?php echo __('Latest News & Events');?> </h3>
				    <ul>				
					<?php foreach($news as $value):$id=$value['News']['page_url'];?>
					<li><?php echo$this->Html->link($value['News']['news_title'],array('controller'=>'News','action'=>'show',$id));?></li>
					<?php endforeach;unset($value);?>				  
				  </ul>
</div>
</div>
<div class="mb50"></div>
</div>
<?php }?>
</div>
</section>
</div>

<div id="footer_index"></div>
<footer class="footer-wrapper footer-bg">
<div class="container">
<div class="row">
<div class="col-sm-6 col-md-4 col-sm-push-6 col-md-push-4 xs-box">
<?php echo __('Time');?> <span><?php echo $this->Time->format('d-m-Y h:i:s A',time());?></span>
</div>
<div class="col-sm-6 col-md-4 col-sm-pull-6 col-md-pull-4">
<p class="copyright"><?php echo __('&copy; Copyright');?> <?php echo$this->Time->format('Y',time());?><span> <?php echo$siteName;?></span></p>
</div>
<span><?php echo __('Powered by');?> <?php echo$this->Html->Link('Eduexpression.com','http://www.eduexpression.com',array('target'=>'_blank'));?></span>
</div>
</div>
</footer>
</div>
<div id="_include_main_plugins"></div>
<div id="_include_owl_carousel"></div>
<div id="_include_isotope"></div> 
<?php echo $this->Html->script('/design300/js/script');
echo $this->fetch('script');
echo $this->Js->writeBuffer();?>
<div id="scriptUrl" style="display: none;"><?php echo $this->Html->url(array('crm'=>false,'controller'=>'app','action'=>'webroot','img'));?></div>
</body></html>