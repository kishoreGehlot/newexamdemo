<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('Add SEO');?></h1></div></div>
<div class="panel">
                <div class="panel-body"><?php echo $this->Session->flash();?>
                <?php echo $this->Form->create('Seo', array('controller' => 'Seos', 'action' => 'add','name'=>'post_req','id'=>'post_req','class'=>'form-horizontal'));?>
                   <div class="form-group">
			<label for="group_name" class="col-sm-2 control-label"><?php echo __('Name');?></label>
			<div class="col-sm-4">
			   <?php echo $this->Form->input('controller',array('label' => false,'class'=>'form-control validate[required]','placeholder'=>__('Name'),'div'=>false));?>
			</div>
			<label for="group_name" class="col-sm-2 control-label"><?php echo __('Page Name');?></label>
			<div class="col-sm-4">
			   <?php echo $this->Form->input('action',array('label' => false,'class'=>'form-control validate[required]','placeholder'=>__('Page Name'),'div'=>false));?>
			</div>
		    </div>
		    <div class="form-group">
			<label for="group_name" class="col-sm-2 control-label"><?php echo __('Meta Title');?></label>
			<div class="col-sm-4">
			   <?php echo $this->Form->input('meta_title',array('label' => false,'class'=>'form-control validate[required]','placeholder'=>__('Meta Title'),'div'=>false));?>
			</div>
			<label for="group_name" class="col-sm-2 control-label"><?php echo __('Meta Keyword');?></label>
			<div class="col-sm-4">
			   <?php echo $this->Form->input('meta_keyword',array('label' => false,'class'=>'form-control','placeholder'=>__('Meta Keyword'),'div'=>false));?>
			</div>
		    </div>
		    <div class="form-group">
			<label for="group_name" class="col-sm-2 control-label"><?php echo __('Meta Content');?></label>
			<div class="col-sm-4">
			   <?php echo $this->Form->input('meta_content',array('label' => false,'class'=>'form-control','placeholder'=>__('Meta Content'),'div'=>false));?>
			</div>
		    </div>
                    <div class="form-group text-left">
                        <div class="col-sm-offset-2 col-sm-10">
                            <?php echo$this->Form->button('<span class="fa fa-plus-circle"></span>&nbsp;'.__('Save'),array('class'=>'btn btn-success','escpae'=>false));?>
                <?php echo$this->Html->link('<span class="fa fa-close"></span>&nbsp;'.__('Close'),array('action'=>'index'),array('class'=>'btn btn-danger','escape'=>false));?>
                        </div>
                    </div>
                   <?php echo $this->Form->end();?>
                </div>
            </div>