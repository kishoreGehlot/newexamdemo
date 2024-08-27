<div id="msg_alert" class="alert alert-<?php echo (isset($alert)? $alert : 'info' ) ?>">
<button type="button" class="close" data-dismiss="alert">x</button>
<center><?php echo (isset($message)? $message : __('Something went wrong') ) ?></center>
</div>
<script type="text/javascript">
    if($('#msg_alert').show()){
        <?php if($alert!="danger"){?>
        setTimeout(function(){$('#msg_alert').fadeOut('slow');},3000);
        <?php }?>
    }
</script>