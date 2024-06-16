<?php

namespace Core\Application\Form\Elements;

class LabelField extends BaseElement
{
    /**
     * Default HTML tag templates.
     *
     * @var array
     */
    protected $template = '<label for=":for" class=":class">:text</label>';

    /**
     * Create a new HTML string instance.
     *
     * @param  string  $text
     * @param  string  $for
     * @return void
     */
    public function __construct($text, $for)
    {
        $this->for($for)
             ->text($text);
    }

    /**
     * Set the label of the input field.
     *
     * @param string $for
     * @return \Application\Form\Field
     */
    public function for($for = null)
    {
        $this->for = $for;

        return $this;
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
                ':for' => $this->for,
                ':class' => $this->class,
                ':text' => $this->text,
            ]
        );
    }
}
