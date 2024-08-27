<script type="text/javascript">
    $(document).ready(function(){
	$( "#NewsNewsTitle").blur(function() {
	    var link_name=$('#NewsNewsTitle').val();
	    var link_url=escapeRegExp(link_name);
	$('#NewsPageUrl').val(link_url);
	});
	  });
</script>
<div class="page-title"> <div class="title-env"> <h1 class="title"><?php echo __('Add News');?></h1></div></div>
<div class="panel">
                <div class="panel-body"><?php echo $this->Session->flash();?>
                <?php echo $this->Form->create('News', array( 'controller' => 'News','class'=>'form-horizontal'));?>
                    <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('News Title');?></small></label>
                        <div class="col-sm-4">
                           <?php echo $this->Form->input('news_title',array('label' => false,'class'=>'form-control','placeholder'=>__('News Title'),'div'=>false));?>
                        </div>
			<label for="group_name" class="col-sm-2 control-label"><small>Page Url</small></label>
                        <div class="col-sm-4">
                           <?php echo $this->Form->input('page_url',array('label' => false,'class'=>'form-control input-sm validate[required]','data-errormessage'=>'Page Url Required','placeholder'=>'Page Url','div'=>false));?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('News Description');?></small></label>
                        <div class="col-sm-10">
                           <?php echo $this->Tinymce->input('news_desc', array('class'=>'form-control','placeholder'=>__('News Description'),'label' => false),array('language'=>$configLanguage,'directionality'=>$dirType),'full');?>
                        </div>
                    </div>
		   <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Meta Title');?></small></label>
                        <div class="col-sm-4">
                           <?php echo $this->Form->input('meta_title',array('label' => false,'class'=>'form-control','placeholder'=>__('Meta Title'),'div'=>false));?>
                        </div>
			<label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Meta Keyword');?></small></label>
                        <div class="col-sm-4">
                           <?php echo $this->Form->input('meta_keyword',array('label' => false,'class'=>'form-control','placeholder'=>__('Meta Keyword'),'div'=>false));?>
                        </div>
                    </div>
		     <div class="form-group">
                        <label for="group_name" class="col-sm-2 control-label"><small><?php echo __('Meta Content');?></small></label>
                        <div class="col-sm-4">
                           <?php echo $this->Form->input('meta_content',array('label' => false,'class'=>'form-control','placeholder'=>__('Meta Content'),'div'=>false));?>
                        </div>			
                    </div>
                    <div class="form-group text-left">
                        <div class="col-sm-offset-2 col-sm-7">
                            <?php echo$this->Form->button('<span class="fa fa-plus-circle"></span>&nbsp;'.__('Save'),array('class'=>'btn btn-success','escpae'=>false));?>
			    <?php echo$this->Html->link('<span class="fa fa-close"></span>&nbsp;'.__('Close'),array('action'=>'index'),array('class'=>'btn btn-danger','escape'=>false));?>
			 </div>
                    </div>
                <?php echo $this->Form->end();?>
                </div>
            </div>