<?php
class Content extends AppModel
{
    public $validationDomain = 'validation';
    public $actsAs = array('search-master.Searchable');
    public $filterArgs = array('keyword' => array('type' => 'like','field'=>'Content.link_name'));
    public $validate =array('link_name' => array('alphaNumeric'=>array('rule' =>'alphaNumericCustom','required'=>true,'allowEmpty'=>false,'message'=>'Only Alphabets')),
                            'parent_id' => array('Numeric'=>array('rule' =>'Numeric','required'=>true,'allowEmpty'=>false)),
                            'url' => array('alphaNumeric'=>array('rule' =>'alphaNumericCustom','required'=>true,'allowEmpty'=>true,'message'=>'Only Url')),
                          );
    public function dropDownFromThreaded($arr, $elmkey, &$list = array(), $counter = 0)
    {
        if (!is_array($arr)){
            return array();    
        }
        foreach ($arr AS $listValue)
        {
            $list[$listValue[$elmkey]['id']] = str_repeat('__', $counter).$listValue[$elmkey]['link_name'];
            if ( ! empty($listValue['children']))
            {
                $this->dropDownFromThreaded($listValue['children'], $elmkey, $list, $counter + 1);
            }
        }
        return $list;
    }
}
?>