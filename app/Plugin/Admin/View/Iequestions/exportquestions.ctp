<div class="panel">
    <div class="panel-heading"><div class="title-env"> <h3 class="title"><?php echo __('Import/Export Qusetions');?></h3></div>
        <div class="btn-group">
        <?php echo $this->Html->link('<span class="fa fa-arrow-left"></span>&nbsp;'.__('Back To Questions'),array('controller' => 'Questions','action'=>'index'),array('escape' => false,'class'=>'btn btn-info'));?>
        <?php echo $this->Html->link('<span class="fa fa-upload"></span>&nbsp;'.__('Import Questions'),array('controller' => 'Iequestions','action'=>'index'),array('escape' => false,'class'=>'btn btn-success'));?>
	</div>
    </div>
        <div class="panel-body"><?php echo $this->Session->flash();?>
                <?php echo $this->Form->create('Iequestion', array( 'controller' => 'Iequestions', 'action' => 'export','name'=>'post_req','id'=>'post_req','class'=>'form-horizontal','type' => 'file'));?>
                     <div class="form-group">
                        <label for="site_name" class="col-sm-3 control-label"><?php echo __('Subject');?></label>
                        <div class="col-sm-9">
                           <?php echo $this->Form->input('subject_id',array('options'=>array($subject_id),'empty'=>__('Please Select'),'class'=>'form-control','div'=>false,'label'=>false));?>
                        </div>
                    </div>
                    <div class="form-group text-left">
                        <div class="col-sm-offset-3 col-sm-7">
			<?php echo$this->Form->button('<span class="fa fa-upload"></span>&nbsp;'.__('Export Questions'),array('class'=>'btn btn-success','escpae'=>false));?>
                        </div>
                    </div>
                <?php echo $this->Form->end();?>
	</div>
</div>
