<?php
namespace system\lib\ViewBuilder\FormBuild;

/**
 * Creates a text input
 */
class Text extends FormInput {
    public function __construct($Attribs=array()){
        $this->Code.=parent::getPend1($Attribs);
        $this->Code.='<input class="form-control" type="text"'.parent::parseAttribs($Attribs).' />';
        $this->Code.=parent::getPend2($Attribs);

        $this->Attribs=$Attribs;
    }
}
?>
