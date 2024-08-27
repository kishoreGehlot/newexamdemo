<?php echo $this->Session->flash();?>
<div class="page-title-breadcrumb">
    <div class="page-header pull-left">
	<div class="page-title"><?php echo __('View Student Information');?></div>
    </div>
</div>
		<div class="panel">
                <div class="panel-body">
						<div class="col-md-2 text-center">
							<p><?php echo $this->Html->image($std_img, array('alt' => h($post['Student']['name']),'class'=>'img-thumbnail'));?></p>
							<?php echo$this->Html->link(__('Edit Profile'),array('action'=>'editProfile'),array('class'=>'btn btn-warning btn-sm btn-block'));?>
							<?php echo$this->Html->link(__('Update Photo'),array('action'=>'changePhoto'),array('class'=>'btn btn-primary btn-sm btn-block'));?>
							<?php echo$this->Html->link(__('Change Password'),array('controller'=>'Profiles','action'=>'changePass'),array('class'=>'btn btn-danger btn-sm btn-block'));?>
						</div>
						<div class="col-md-10"> 
							<div class="row">
								<table class="table table-striped table-bordered">
									<tr class="text-primary">
										<td><strong><small><?php echo __('Full Name');?></small></strong></td>
										<td><strong><small><?php echo __('Phone Number');?></small></strong></td>
									</tr>
									<tr>
										<td><strong><small><?php echo h($post['Student']['name']);?></small></strong></td>
										<td><strong><small><?php echo h($post['Student']['phone']);?></small></strong></td>
									</tr>
									<tr class="text-primary">
										<td><strong><small><?php echo __('Registered Email');?></small></strong></td>
										<td><strong><small><?php echo __('Alternate Number');?></small></strong></td>
									</tr>
									<tr>
										<td><strong><small><?php echo h($post['Student']['email']);?></small></strong></td>
										<td><strong><small><?php echo h($post['Student']['guardian_phone']);?></small></strong></td>
									</tr>
									</tr>
									<tr class="text-primary">
										<td><strong><small><?php echo __('Enrolment No');?><strong><small></td>
										<td><strong><small><?php echo __('Admission Date');?></small></strong></td>
									</tr>
									<tr>
										<td><strong><small><?php echo h($post['Student']['enroll']);?></small></strong></td>
										<td><strong><small><?php echo $this->Time->format($sysDay.$dateSep.$sysMonth.$dateSep.$sysYear,$post['Student']['created']); ?></small></strong></td>
									</tr>
									<tr class="text-primary"> 
										<td><strong><small><?php echo __('Groups');?> <strong><small></td>
										<td></td>
									</tr>
									<tr>
										<td colspan="2"><small><strong>
										<?php foreach($groupSelect as $groupValue):
										echo h($groupValue['Groups']['group_name']);?> |
										<?php endforeach;unset($groupValue);?>
										</small></strong></td>
									</tr>
								</table>
							</div>
						</div>
					</div>	
				</div>
      