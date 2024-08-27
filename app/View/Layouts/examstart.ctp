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
<html lang="<?php echo $configLanguage; ?>" dir="<?php echo $dirType; ?>">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-translate-customization" content="839d71f7ff6044d0-328a2dc5159d6aa2-gd17de6447c9ba810-f">
    <?php echo $this->Html->charset(); ?>
    <title><?php echo $metaTitle; ?></title>
    <meta name="keyword" content="<?php echo $metaKeyword; ?>"/>
    <meta name="description" content="<?php echo $metaContent; ?>"/>
    <?php
    echo $this->Html->meta('icon');
    echo $this->Html->css('/design500/assets/css/bootstrap.min');
    echo $this->Html->css('style.css');
    echo $this->Html->css('exam.css');
    echo $this->Html->css('jquery.countdown');
    echo $this->Html->css('msgBoxLight');
    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->Html->script('jquery-1.8.2.min');
    echo $this->Html->script('html5shiv');
    echo $this->Html->script('respond.min');
    echo $this->Html->script('bootstrap.min');
    echo $this->Html->script('waiting-dialog.min'); ?>
    <script type="text/javascript">
        var msgBoxImagePathServer='<?php echo $this->webroot;?>img/';
        var lang = new Array();
        lang['lastQuestion'] = '<?php echo __('You have reached last question of test, do you want to go to first question again?');?>';
        lang['AreYouSure'] = '<?php echo __('Are You Sure?');?>';
    </script>
    <?php echo $this->Html->script('jquery.msgBox');
    echo $this->Html->script('exam.custom.min');
    echo $this->Html->script("langs/$configLanguage");
    echo $this->Html->script('jquery.plugin.min');
    echo $this->Html->script('jquery.countdown.min');
    if ($mathEditor) echo $this->Html->script('http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=AM_HTMLorMML-full');
    echo $this->fetch('script');
    echo $this->Js->writeBuffer();
    $UserArr = $userValue;
    if (strlen($UserArr['Student']['photo']) > 0)
        $studentImage = 'student_thumb/' . $UserArr['Student']['photo'];
    else
        $studentImage = 'User.png';
    if ($mathEditor) {
        ?>
        <script type="text/x-mathjax-config">
            MathJax.Hub.Config({extensions: ["tex2jax.js"],jax: ["input/TeX", "output/HTML-CSS"],tex2jax: {inlineMath: [["$", "$"],["\\(", "\\)"]]}});
        </script><?php } ?>
    <?php echo $this->Html->scriptBlock("jQuery(document).ready(function () {
    'use strict';
    document.body.oncopy = function() { return false; }
    document.body.oncut = function() { return false; }
});
", array('inline' => true)); ?>
    <?php if ($translate > 0) { ?>
        <script type="text/javascript">
            function googleTranslateElementInit() {
                new google.translate.TranslateElement({
                    pageLanguage: 'en',
                    layout: google.translate.TranslateElement.InlineLayout.SIMPLE
                }, 'google_translate_element');
            }
        </script>
        <script type="text/javascript"
                src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <?php } ?>
</head>
<body>
<div id="google_translate_element"></div>
<div class="col-md-12">
    <div
        class="col-md-9"><?php if (strlen($frontLogo) > 0) { ?><?php echo $this->Html->image($frontLogo, array('alt' => $siteName, 'class' => 'img-responsive'));
        } else { ?>
            <div class="exam-logo"><?php echo $siteName; ?></div>
        <?php } ?>
    </div>
    <?php if (strtolower($this->params['controller']) == "exams" && strtolower($this->params['action']) == "start") { ?>
        <div
            class="col-md-3 exam-photo"><?php echo $this->Html->image($studentImage, array('height' => '50', 'title' => $UserArr['Student']['name'])); ?></div><?php } ?>
</div>
<div class="col-md-12">
    <div class="exam-border">&nbsp;</div>
</div>
<div>
    <?php echo $this->fetch('content'); ?>
</div>
<div id="scriptUrl" style="display: none;"><?php echo $this->Html->url(array('crm'=>false,'controller'=>'app','action'=>'webroot','img'));?></div>
</body>
</html>