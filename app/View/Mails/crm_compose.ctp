<?php echo $this->Session->flash();?>
<div class="page-title-breadcrumb">
    <div class="page-header pull-left">
	<div class="page-title"><?php echo __('Compose Mail');?></div>
    </div>
</div>
<div class="panel">
                <div class="panel-body">
		<?php echo $this->Form->create('Mail', array( 'controller' => 'Mails', 'action' => 'compose','name'=>'post_req','id'=>'post_req','class'=>'form-horizontal'));?>
                    <div class="form-group">
                        <label for="group_name" class="col-sm-3 control-label"><small><?php echo __('To');?></small></label>
                        <div class="col-sm-9">
                           <?php echo $this->Form->select('to_email',$userFinal,array('empty'=>__('Select'),'label' => false,'class'=>'form-control','div'=>false));?>
                    </div>
                    </div>
		    <div class="form-group">
                        <label for="group_name" class="col-sm-3 control-label"><small><?php echo __('Subject');?></small></label>
                        <div class="col-sm-9">
                           <?php echo $this->Form->input('subject',array('label' => false,'class'=>'form-control','placeholder'=>__('Subject'),'div'=>false));?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="group_name" class="col-sm-3 control-label"><small><?php echo __('Message');?></small></label>
                        <div class="col-sm-9">
                           <?php echo $this->Tinymce->input('message', array('class'=>'form-control','placeholder'=>__('Message'),'label' => false),array('language'=>$configLanguage,'directionality'=>$dirType),'full');?>
                        </div>
                    </div>
                    <div class="form-group text-left">
                        <div class="col-sm-offset-3 col-sm-7">
                            <button type="submit" class="btn btn-success"><span class="fa fa-send"></span>&nbsp;<?php echo __('Send');?></button>
			    <?php echo$this->Html->link('<span class="fa fa-close"></span>&nbsp;'.__('Close'),array('controller'=>'Mails','action'=>'index'),array('class'=>'btn btn-danger','escape'=>false));?>
                        </div>
                    </div>
                <?php echo $this->Form->end();?>
                </div>
            </div>