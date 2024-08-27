<?php echo$this->Html->scriptBlock("function closeExamWindow(){var ww = window.open(window.location, '_self'); ww.close();}",array('inline'=>true));
if($isClose=="Yes"){echo $this->Html->scriptBlock("setTimeout(function(){closeExamWindow(); }, 1500);",array('inline'=>true));}?>
<div class="col-md-9 col-sm-offset-2">
<?php echo $this->Session->flash();?>
    <div class="panel panel-default">
	<div class="panel-heading"><center><?php if($isClose=="Yes"){echo $this->Session->flash();}else{echo __("Thank you for using the test");}?></center></div>
	<div class="panel-body">
	    <?php echo $this->Form->create('Exam', array( 'controller' => 'Exams', 'action' => "feedbacks/$id",'name'=>'post_req','id'=>'post_req','class'=>'form-horizontal'));?>
	    <div class="form-group">
		<label for="subject_name" class="col-sm-3 control-label"><small><?php  echo $feedbackArr[0];?></small></label>
		<div class="col-sm-4">
		   <?php echo $this->Form->select('comment1',array('Largely Clear'=>__('Largely Clear'),'Medium Clear'=>__('Medium Clear'),'Not Clear'=>__('Not Clear')),array('empty'=>false,'label' => false,'class'=>'form-control','div'=>false));?>
		</div>
	    </div>
	    <div class="form-group">
		<label for="subject_name" class="col-sm-3 control-label"><small><?php  echo $feedbackArr[1];?></small></label>
		<div class="col-sm-4">
		   <?php echo $this->Form->select('comment2',array('Largely Clear'=>__('Largely Clear'),'Medium Clear'=>__('Medium Clear'),'Not Clear'=>__('Not Clear')),array('empty'=>false,'label' => false,'class'=>'form-control','div'=>false));?>
		</div>
	    </div>
	    <div class="form-group">
		<label for="subject_name" class="col-sm-3 control-label"><small><?php  echo $feedbackArr[2];?></small></label>
		<div class="col-sm-4">
		   <?php echo $this->Form->select('comment3',array('Good'=>__('Good'),'Better'=>__('Better'),'Best'=>__('Best')),array('empty'=>false,'label' => false,'class'=>'form-control','div'=>false));?>
		</div>
	    </div>
	    <div class="form-group">
		<label for="subject_name" class="col-sm-3 control-label"><small><?php  echo $feedbackArr[3];?></small></label>
		<div class="col-sm-4">
		   <?php echo $this->Form->input('comments',array('type'=>'textarea','required'=>'required','label' => false,'class'=>'form-control','div'=>false));?>
		</div>
	    </div>
	    <div class="form-group text-left">
		<div class="col-sm-offset-3 col-sm-7">
		    <button type="submit" class="btn btn-success"><span class="fa fa-plus"></span> <?php echo __('Submit');?></button>
		    <?php echo$this->Html->link('<span class="fa fa-close"></span> '.__('Close'),'#',array('onClick'=>'closeExamWindow();','class'=>'btn btn-danger','escape'=>false));?>
		</div>
	    </div>
	    <?php echo $this->Form->end();?>
	</div>
    </div>
</div>