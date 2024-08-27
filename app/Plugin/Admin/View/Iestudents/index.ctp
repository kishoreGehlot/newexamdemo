<div class="panel">
    <div class="panel-heading"><div class="title-env"> <h3 class="title"><?php echo __('Import/Export Students');?></h3></div>
        <div class="btn-group">
        <?php echo $this->Html->link('<span class="fa fa-arrow-left"></span>&nbsp;'.__('Back To Students'),array('controller' => 'Students','action'=>'index'),array('escape' => false,'class'=>'btn btn-info'));?>
        <?php echo $this->Html->link('<span class="fa fa-upload"></span>&nbsp;'.__('Export Students'),array('controller' => 'Iestudents','action'=>'export'),array('escape' => false,'class'=>'btn btn-success'));?>
	</div>
    </div>
        <div class="panel-body"><?php echo $this->Session->flash();?>
                <?php echo $this->Form->create('Iestudent', array( 'controller' => 'Iestudents', 'action' => 'import','name'=>'post_req','id'=>'post_req','class'=>'form-horizontal','type' => 'file'));?>
                     <div class="form-group">
                        <label for="site_name" class="col-sm-3 control-label"><?php echo __('Select File');?></label>
                        <div class="col-sm-3">
                        <?php echo $this->Form->input('file',array('type' => 'file','label' => false,'div'=>false));?>
                        </div>
			<div class="col-sm-6"><?php echo $this->Html->link(__('Click here to download excel file format'),array('controller' => 'Iestudents','action'=>'download'),array('escape' => false,'class'=>'text-danger'));?></div>
                    </div>
		    <div class="form-group">
                        <label for="site_name" class="col-sm-3 control-label"><?php echo __('Group');?></label>
                        <div class="col-sm-6">
                           <?php echo $this->Form->select('StudentGroup.group_name',$group_id,array('multiple'=>true,'class'=>'form-control multiselectgrp','div'=>false));?>
                        </div>
                    </div>
                    <div class="form-group text-left">
                        <div class="col-sm-offset-3 col-sm-6">
			<?php echo$this->Form->button('<span class="fa fa-download"></span>&nbsp;'.__('Import Students'),array('class'=>'btn btn-success','escpae'=>false));?>
                         </div>
                    </div>
                <?php echo $this->Form->end();?>
	</div></div>
