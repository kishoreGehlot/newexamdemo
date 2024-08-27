<div class="header-content text-center"><h1><?php echo __('Email Verification');?></h1></div>
<?php echo $this->Form->create('Emailverification', array('name'=>'post_req','id'=>'post_req','class'=>'form-horizontal','role'=>'form'));?>
<div class="body-content">
    <?php echo $this->Session->flash();?>        
    <div class="list-group">
        <div class="list-group-item">
            <?php echo $this->Form->input('vc',array('required'=>true,'label' => false,'class'=>'form-control','placeholder'=>__('Please Enter Verification Code in the Text Box'),'div'=>false));?>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-sm-5">
           <button type="submit" class="btn btn-success btn-circle btn-block btn-shadow mbs"><?php echo __('Submit');?></button>
        </div>
    </div>
    <hr>
    <?php if($frontRegistration==1){?>
        <div class="form-group">
            <p>
            <?php echo$this->Html->link(__('Re-Send Email Verification'),array('controller'=>'Emailverifications','action'=>'resend'),array('class'=>'btn-link','escape'=>false));?>
            </p>
        </div>
    <?php }?>
</div>
<?php echo$this->Form->end();?>