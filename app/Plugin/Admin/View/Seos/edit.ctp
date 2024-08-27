<div <?php if(!$isError){?>class="container"<?php }?>>
<div class="panel panel-custom mrg">
<div class="panel-heading"><?php echo __('Edit SEO');?><?php if(!$isError){?><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><?php }?></div>
<div class="panel">
                <div class="panel-body"><?php echo $this->Session->flash();?>
					<?php echo $this->Form->create('Seo', array( 'controller' => 'Seos','name'=>'post_req','id'=>'post_req','class'=>'form-horizontal'));?>
					<?php foreach ($Seo as $k=>$post): $id=$post['Seo']['id'];$form_no=$k+1;?>
						<div class="panel panel-default">
							<div class="panel-heading"><strong><small class="text-danger"><?php echo __('Form');?> <?php echo$form_no?></small></strong></div>
							<div class="panel-body">
								<div class="form-group">
									<label for="group_name" class="col-sm-2 control-label"><?php echo __('Name');?></label>
									<div class="col-sm-4">
									   <?php echo $this->Form->input("$k.Seo.controller",array('label' => false,'class'=>'form-control validate[required]','placeholder'=>__('Name'),'div'=>false));?>
									</div>
									<label for="group_name" class="col-sm-2 control-label"><?php echo __('Page Name');?></label>
									<div class="col-sm-4">
									   <?php echo $this->Form->input("$k.Seo.action",array('label' => false,'class'=>'form-control validate[required]','placeholder'=>__('Page Name'),'div'=>false));?>
									</div>
								</div>
								<div class="form-group">
								    <label for="group_name" class="col-sm-2 control-label"><?php echo __('Meta Title');?></label>
								    <div class="col-sm-4">
								       <?php echo $this->Form->input("$k.Seo.meta_title",array('label' => false,'class'=>'form-control validate[required]','placeholder'=>__('Meta Title'),'div'=>false));?>
								    </div>
								     <label for="group_name" class="col-sm-2 control-label"><?php echo __('Meta Keyword');?></label>
								    <div class="col-sm-4">
								       <?php echo $this->Form->input("$k.Seo.meta_keyword",array('label' => false,'class'=>'form-control','placeholder'=>__('Meta Keyword'),'div'=>false));?>
								    </div>
								</div>
								<div class="form-group">
								    <label for="group_name" class="col-sm-2 control-label"><?php echo __('Meta Content');?></label>
								    <div class="col-sm-4">
								       <?php echo $this->Form->input("$k.Seo.meta_content",array('label' => false,'class'=>'form-control','placeholder'=>__('Meta Content'),'div'=>false));?>
								    </div>
								</div>
								<div class="form-group text-left">
									<div class="col-sm-offset-2 col-sm-10">
										<?php echo $this->Form->input("$k.Seo.id", array('type' => 'hidden'));?>                            
									</div>
								</div>
							</div>	
						</div>						
                    <?php endforeach; ?>
                        <?php unset($post); ?>
                        <div class="form-group text-left">
		<div class="col-sm-offset-3 col-sm-6">
		    <?php echo$this->Form->button('<span class="fa fa-refresh"></span>&nbsp;'.__('Update'),array('class'=>'btn btn-success','escpae'=>false));?>
		    <?php if(!$isError){?><button type="button" class="btn btn-danger" data-dismiss="modal"><span class="fa fa-remove"></span>&nbsp;<?php echo __('Cancel');?></button><?php }else{
			echo$this->Html->link('<span class="fa fa-close"></span>&nbsp;'.__('Close'),array('action'=>'index'),array('class'=>'btn btn-danger','escape'=>false));}?>
		</div>
	    </div>
                   <?php echo $this->Form->end();?>
                </div>
            </div>