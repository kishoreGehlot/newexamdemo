<div class="page-title"> <div class="title-env"><h1 class="title"><?php echo __('Help');?></h1></div></div>
<div class="panel"><div class="panel">
    <div class="panel-body">
            <div class="cust-supt"><?php echo __('Customer Support Service');?></div>
            <div class="row">
                <div class="col-md-6">
                    <ul class="cust-list">
                        <li><?php echo$this->Html->link(__('Knowledge Base (for Documentation)'),'https://eduexpression.com/knowledgbase.html',array('target'=>'_blank'));?></li>
                        <li><?php echo$this->Html->link(__('Forum (24x7x)'),'https://eduexpression.com/support-forum.html',array('target'=>'_blank'));?></li>
                        <li><?php echo$this->Html->link(__('Live Chat (For Sales & Billing Only)'),'https://eduexpression.com/ ',array('target'=>'_blank'));?></li>
                        <li><?php echo$this->Html->link(__('Support (For Technical Support)'),'https://eduexpression.com/technical-support.html',array('target'=>'_blank'));?></li>                        
                    </ul>		    
                </div>
                <div class="col-md-4">
                    <?php echo$this->Html->image('customer-support.png',array('class'=>'img-rounded'));?>
                </div>		
            </div>
	    <div class="cust-head2"><?php echo __('Visit Our Website');?> <?php echo$this->Html->Link('Eduexpression.com','https://eduexpression.com',array('target'=>'_blank'));?> <?php echo __('for more documentation & Details');?></div>
        </div>
    </div></div>