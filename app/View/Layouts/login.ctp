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
		echo $this->Html->css('/design500/assets/css/login');
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
		echo $this->fetch('script');
                echo $this->Js->writeBuffer();
		?>
<?php if($translate>0){?>
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<?php }?>
  </head>
<body class="page-signin">
  <div class="page-form">
    <?php if(strlen($frontLogo)>0){?><div align="center"><?php echo$this->Html->link($this->Html->image($frontLogo,array('alt'=>$siteName,'class'=>'global-logo')),array('controller'=>'../'),array('escape'=>false));?></div><?php }
    else{?>
    <div align="center"><h3><?php echo$this->Html->link(__('Welcome to %s',$siteName),array('controller'=>'../'));?></h3></div>
    <?php }?>
    <?php echo $this->fetch('content');?>
  </div>
  <div id="scriptUrl" style="display: none;"><?php echo $this->Html->url(array('crm'=>false,'controller'=>'app','action'=>'webroot','img'));?></div>
</body>
</html>