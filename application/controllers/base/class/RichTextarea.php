<?php

/*
 * Campo de Texto Enriquecido.
 */

/**
 * Description of RichTextarea
 *
 * @author Mirco Bombieri
 */
class RichTextarea extends Textarea {

    function RichTextarea($pName, $pValue, $pLabel, $pClass = 'richtextarea', $pAttributes = '', $pEnabled = true, $pVisible = true) {
        parent::Textarea($pName, $pValue, $pLabel, $pClass, $pAttributes, $pEnabled, $pVisible);
        $this->_type = 'rich_textarea';
    }

    public function generate() {
        $defaults = array('id' => $this->getName(), 'name' => $this->getName());

        if ($this->isVisible()) {
            if (!$this->isEnabled()) {
                $this->addClass('disabled');
                $defaults['readonly'] = 'READONLY';
            }
        } else
            $this->addClass('invisible');
        $defaults['class'] = $this->getClass();
        $result = "<script type=\"text/javascript\" >
                    //<![CDATA[
                        bkLib.onDomLoaded(function() {
                            new nicEditor(
                                    {iconsPath : '" . base_url() . "img/base/textarea/nicEditorIcons.gif', 
                                     buttonList : ['bold','italic','underline','left','center','right','justify','ol','ul','indent','outdent','image','upload','link','unlink','forecolor','bgcolor'],
                                    }).panelInstance('" . $this->getName() . "'); 
                        });
                    //]]>
                    </script>";
        $result .= '<textarea ' . $this->_parse_form_attributes($this->attributes, $defaults) . ' >' . htmlentities($this->getValue()) . '</textarea>';
        return $result;
    }

}

?>
