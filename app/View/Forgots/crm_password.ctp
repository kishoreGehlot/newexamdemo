<div class="header-content text-center"><h1><?php echo __('Forgot Password');?></h1></div>
<?php echo $this->Form->create('Forgot', array('name'=>'post_req','id'=>'post_req','class'=>'form-horizontal','role'=>'form'));?>
<div class="body-content">
	<?php echo $this->Session->flash();?>        
    <div class="list-group">
         <div class="list-group-item">
             <?php echo $this->Form->input('email',array('type'=>'email','required'=>true,'label' => false,'class'=>'form-control','placeholder'=>__('Email'),'div'=>false));?>
         </div>
     </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-sm-5">
           <button type="submit" class="btn btn-success btn-circle btn-block btn-shadow mbs"><?php echo __('Submit');?></button>
        </div>
    </div>
    <hr>
    <div class="form-group">
        <p>
        <?php echo$this->Html->link(__('Click here to login'),array('controller'=>'Users','action'=>'index'),array('id'=>'btn-register','class'=>'btn-link','escape'=>false));?>
        </p>
    </div>
</div>
<?php echo$this->Form->end();?>	