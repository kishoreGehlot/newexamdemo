<?php
App::uses('Component','Controller');
App::uses('HttpSocket', 'Network/Http');
class CustomfunctionComponent extends Component
{
    public function bcmod( $x, $y ) 
    { 
        $mod = $x % $y;    
        return (int)$mod; 
    }
    public function secondsToWords($seconds,$msg="Unlimited")
    {
        $ret = "";
        if($seconds>0)
        {
            /*** get the hours ***/
            $hours = intval(intval($seconds) / 3600);
            if($hours > 0)
            {
                $ret .= $hours.' '.__('Hours').' ';
            }
            /*** get the minutes ***/
            $minutes = $this->bcmod((intval($seconds) / 60),60);
            if($minutes > 0)
            {
                $ret .= $minutes.' '.__('Mins').' ';
            }
            $tarMinutes = $this->bcmod((intval($seconds)),60);
            if(strlen($ret)==0 || $tarMinutes>0)
            {
                if($tarMinutes>0)
                $ret .= $tarMinutes.' '.__('Sec');
                else
                $ret .= $seconds.' '.__('Sec');
            }
        }
        else
        {
            $ret=$msg;
        }
        return $ret;
    }
    public function generate_rand($digit=6)
    {
      $no=substr(strtoupper(md5(uniqid(rand()))),0,$digit);
      return $no;
    }
    public function WalletInsert($student_id,$amount,$amount_type,$date,$type,$remarks,$user_id=null)
    {
        $Wallet=ClassRegistry::init('Wallet');
        $in_amount=null;
        $out_amount=null;
        if($amount_type=="Added")
        $in_amount=$amount;
        else
        $out_amount=$amount;
        if($in_amount==null && $out_amount==null)
        {
            return false;
        }
        elseif($amount<=0)
        {
            return false;
        }
        else
        {
            $Wallet->virtualFields= array('in_amount'=>'SUM(in_amount)','out_amount'=>'SUM(out_amount)');
            $AmountArr=$Wallet->find('first',array('fields'=>array('in_amount','out_amount'),'conditions'=>array('student_id'=>$student_id)));
            $total_in_amount=$AmountArr['Wallet']['in_amount'];
            $total_out_amount=$AmountArr['Wallet']['out_amount'];
            if($total_in_amount=="")
            $total_in_amount=0;
            if($total_out_amount=="")
            $total_out_amount=0;
            $balance=$total_in_amount-$total_out_amount+$in_amount-$out_amount;
            $record_arr=array('student_id'=>$student_id,'in_amount'=>$in_amount,'out_amount'=>$out_amount,'balance'=>$balance,'date'=>$date,'type'=>$type,
                              'remarks'=>$remarks,'user_id'=>$user_id);
            $Wallet->create();
            if($Wallet->save($record_arr))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }
    public function WalletBalance($student_id)
    {
        $Wallet=ClassRegistry::init('Wallet');
        $balanceWallet=$Wallet->find('first',array('conditions'=>array('student_id'=>$student_id),
                                                   'fields'=>array('balance'),
                                                   'order'=>array('id DESC'),
                                                   'limit'=>1));
        $balance="0.00";
        if(count($balanceWallet)>0)
        {
            $balance=$balanceWallet['Wallet']['balance'];
        }
        return $balance;
    }
    public function secondsToHourMinute($seconds)
    {
        $ret = "";
        if($seconds>0)
        {
            /*** get the hours ***/
            $hours = intval(intval($seconds) / 3600);
            if($hours > 0)
            {
                $ret .= "$hours.";
            }
            /*** get the minutes ***/
            $minutes = $this->bcmod((intval($seconds) / 60),60);
            if($hours > 0 || $minutes > 0)
            {
                $ret .= "$minutes";
            }
        }
        else
        {
            $ret="";
        }
        return (float) $ret;
    }
    public function showGroupName($gropArr,$string=" | ")
    {
        $groupNameArr=array();
        foreach($gropArr as $groupName)
        {
            $groupNameArr[]=$groupName['group_name'];
        }
        unset($groupName);
        $showGroup= implode($string,$groupNameArr);
        unset($groupNameArr);
        return h($showGroup);
    }
    public function sendSms($mobileNo,$message,$smsArr=array())
    {
        $url=$smsArr['Smssetting']['api'];
        $postType=$smsArr['Smssetting']['post_type'];
        $postData=array($smsArr['Smssetting']['husername']=>$smsArr['Smssetting']['username'],$smsArr['Smssetting']['hpassword']=>$smsArr['Smssetting']['password'],$smsArr['Smssetting']['hsenderid']=>$smsArr['Smssetting']['senderid'],$smsArr['Smssetting']['hmobile']=>$mobileNo,$smsArr['Smssetting']['hmessage']=>$message);
        $othersFields=$smsArr['Smssetting']['others'];
        if(strlen($othersFields)>0){
            $othersFieldsArr=explode("&",$othersFields);
            foreach($othersFieldsArr as $fldArr){
                $fieldValArr=explode("=",$fldArr);
                $heading=$fieldValArr[0];
                $value=$fieldValArr[1];
                $postData[$heading]=$value;
            }
            $query=$postData;            
        }
        else{
            $query=$postData;
        }
        //$file = new File(TMP.'sms.txt',true,0777);
        //$file->write($url.'\n'.$mobileNo.'\n'.$message.'\n','a',true);
        //$file->close();
        if (!$this->HttpSocket) {
            $this->HttpSocket = new HttpSocket();
        }
        if($postType=="GET") {
            $response=$this->HttpSocket->get($url, $query);
        }
        else {
            $response=$this->HttpSocket->post($url, $query);
        }
        $parsed = str_replace(array('{"{\"','\"','}":""}'),"",json_encode($this->parseApiResponse($response)));
        return$parsed;
    }
    public function parseApiResponse($response) {
        parse_str($response , $parsed);
        return $parsed;
    }
}