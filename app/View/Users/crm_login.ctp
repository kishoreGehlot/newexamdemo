<div class="header-content text-center"><h1><?php echo __('Student Login Panel'); ?></h1></div>
<?php echo $this->Form->create('User', array('name' => 'post_req', 'id' => 'post_req', 'class' => 'form-horizontal', 'role' => 'form')); ?>
<div class="body-content">
    <?php echo $this->Session->flash(); ?>
    <div class="list-group">
        <div class="list-group-item">
            <?php echo $this->Form->input('email', array('required' => true, 'type' => 'email', 'label' => false, 'class' => 'form-control', 'placeholder' => __('Email'), 'div' => false)); ?>
        </div>
        <div class="list-group-item">
            <?php echo $this->Form->input('password', array('required' => true, 'label' => false, 'class' => 'form-control', 'placeholder' => __('Password'), 'value' => '', 'type' => 'password', 'div' => false)); ?>
        </div>
    </div>
    <?php if ($frontRegistration == 1) { ?>
        <div
            class="form-group pull-right"><?php echo $this->Html->link(__('Re-Send Email Verification'), array('controller' => 'Emailverifications', 'action' => 'resend'), array('class' => 'btn-link', 'escape' => false)); ?></div>
    <?php } ?>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-sm-5">
            <button type="submit"
                    class="btn btn-success btn-circle btn-block btn-shadow mbs"><?php echo __('Log in'); ?></button>
        </div>
        <div class="col-sm-7">
            <?php echo $this->Html->link(__('Forgot Password'), array('controller' => 'forgots', 'action' => 'password'), array('class' => 'btn-link', 'escape' => false)); ?>
        </div>
    </div>
    <hr>
    <?php if ($frontRegistration == 1) { ?>
        <div class="form-group">
            <p><?php echo __("Don't have an account?"); ?>
                <?php echo $this->Html->link(__('New User? Create Account'), array('controller' => '../Registers', 'action' => 'index'), array('id' => 'btn-register', 'class' => 'btn-link', 'escape' => false)); ?>
            </p>
        </div>
    <?php } ?>
</div>
<?php echo $this->Form->end(); ?>