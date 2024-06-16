<?php

namespace Core\Application\Form\Elements;

class SubmitButton extends BaseElement
{
    /**
     * Default HTML tag templates.
     *
     * @var array
     */
    protected $template = '<button type="submit" class=":class" :attributes>:text</button>';

    /**
     * The html text of
     * the HTML tag.
     *
     * @var string
     */
    protected $text;

    /**
     * The class attribute of
     * the HTML tag.
     *
     * @var string
     */
    protected $class;

    /**
     * Create a new HTML string instance.
     *
     * @param  string  $name
     * @return void
     */
    public function __construct($name)
    {
        $this->name($name)
             ->class('btn btn-primary')
             ->attributes();
    }

    /**
     * Get the HTML string.
     *
     * @return string
     */
    public function toHtml()
    {
        return strtr(
            $this->template,
            [
                ':class' => $this->class,
                ':attributes' => $this->attributes,
                ':text' => $this->text,
            ]
        );
    }
}
