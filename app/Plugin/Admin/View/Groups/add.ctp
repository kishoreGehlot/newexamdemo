<div class="panel panel-custom">
    <div class="panel-heading"><?= __('Add Groups');?></div>
    <div class="panel-body">
        <?= $this->Form->create('Group', ['class'=>'form-horizontal']);?>
        <div class="form-group">
            <label for="group_name" class="col-sm-3 control-label"><small><?= __('Group Name');?></small></label>
            <div class="col-sm-9">
               <?= $this->Form->input('group_name', ['label' => false,'class'=>'form-control','placeholder'=> __('Group Name'),'div'=>false]); ?>
            </div>
        </div>
        <div class="form-group text-left">
            <div class="col-sm-offset-3 col-sm-6">
                <?= $this->Form->button('<span class="fa fa-plus-circle"></span>&nbsp;'.__('Save'), ['class'=>'btn btn-success','escpae'=>false]); ?>
                <?= $this->Html->link('<span class="fa fa-close"></span>&nbsp;'.__('Close'), ['action'=>'index'], ['class'=>'btn btn-danger','escape'=>false]); ?>
            </div>
        </div>
        <?= $this->Form->end(); ?>
    </div>
</div>