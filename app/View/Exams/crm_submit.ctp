<style type="text/css">
    .modal-backdrop {
        background-color: #000;
    }

    .modal-backdrop.in {
        opacity: .5;
    }
</style>
<script type="text/javascript">
    function firstQuestion() {
        navigation(1);
        $('#targetModal').modal('hide');
    }
    function submitExam() {
        window.location='<?php echo $this->Html->url(array('controller'=>'Exams','action'=>'finish',$post['Exam']['id'],'null'));?>/'+currentQuesNo();
    }
</script>
<div class="container">
    <div class="row">
        <div class="col-md-7 col-sm-offset-2 mrg">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="widget-modal">
                        <h4 class="widget-modal-title">
                            <span><?php echo __('Finalize %s', __($post['Exam']['type'])); ?></span>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        </h4>
                    </div>
                </div>
                <div class="panel-body">
                    <p><?php echo __('Do you wish to submit and close the %s ?', __($post['Exam']['type'])); ?><?php echo __('Once you submit, you will not be able to review the %s.', __($post['Exam']['type'])); ?></p>
                    <p><?php echo __('Summary of your attempts in this %s as show below', __($post['Exam']['type'])); ?></p>
                    <div class="exam-divLegend" style="margin:5px; line-height: 40px;">
                        <table>
                            <tbody>
                            <tr>
                                <td>
                                    <button
                                        class="exam-ButtonAnswered"><?php echo $answered; ?></button>
                                    <?php echo __('Answered'); ?>
                                </td>
                                <td>
                                    <button class="exam-ButtonNotAnswered"><?php echo $notAnswered; ?></button>
                                    <?php echo __('Not Answered'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button class="exam-ButtonNotAnsweredMarked"><?php echo $notansmarked; ?></button>
                                    <?php echo __('Marked'); ?>
                                </td>
                                <td>
                                    <button class="exam-ButtonNotVisited"><?php echo $notAttempted; ?></button>
                                    <?php echo __('Not Visited'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button class="exam-ButtonAnsweredMarked"><?php echo $ansmarked; ?></button>
                                    <?php echo __('Answered &amp; Marked for Review'); ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <?php echo $this->Form->button('<span class="glyphicon glyphicon-lock"></span>&nbsp;' . __('Finish %s', __($post['Exam']['type'])), array('type' => 'button', 'onclick' => 'submitExam();', 'class' => 'btn btn-success', 'escape' => false)); ?>
                        </div>
                        <div class="col-sm-3">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><span
                                    class="glyphicon glyphicon-remove"></span>&nbsp;<?php echo __('Cancel'); ?></button>
                        </div>
                        <div class="col-sm-4">
                            <?php echo $this->Form->button('&larr;' . __('Return To First Question'), array('type' => 'button', 'onclick' => "firstQuestion()", 'class' => 'btn btn-warning', 'escape' => false)); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
