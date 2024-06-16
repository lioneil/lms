<?php

namespace Core\Application\Form\Elements;

class FormGroup extends BaseElement
{
    /**
     * Default HTML tag templates.
     *
     * @var array
     */
    protected $template = '<div class="%s" %s>%s</div>';

    /**
     * The class attribute of
     * the HTML tag.
     *
     * @var string
     */
    protected $class = 'form-group';

    /**
     * Create a new HTML string instance.
     *
     * @param  string  $name
     * @return void
     */
    public function __construct($name)
    {
        $this->class($name ?? $this->class);
    }

    /**
     * Get the HTML string.
     *
     * @return string
     */
    public function toHtml()
    {
        return sprintf(
            $this->template,
            $this->class, $this->attributes, '%s'
        );
    }
}
