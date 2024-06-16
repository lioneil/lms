<?php

namespace Core\Application\Form\Elements;

class CheckboxField extends TextInput
{
    /**
     * Default HTML tag templates.
     *
     * @var array
     */
    protected $template = '<div class="custom-control custom-:type"><input id=":id" name=":name" type=":type" value=":value" class=":class" :attributes>:label:help-block</div>';

    /**
     * Set the id of the input field.
     *
     * @param string $id
     * @return \Application\Form\Field
     */
    public function id($id = null)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set the class of the input field.
     *
     * @param string $class
     * @return \Application\Form\Field
     */
    public function class($class = 'custom-control-input')
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Set the label of the input field.
     *
     * @param string $label
     * @return \Application\Form\Field
     */
    public function label($label = null)
    {
        $this->label = $label ? with(new LabelField($label, $this->id))->class('custom-control-label')->toHtml() : null;

        return $this;
    }
}
