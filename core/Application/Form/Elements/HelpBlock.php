<?php

namespace Core\Application\Form\Elements;

class HelpBlock extends BaseElement
{
    /**
     * Default HTML tag templates.
     *
     * @var array
     */
    protected $template = '<div class="has-:context text-:context :class">:text</div>';

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
     * The class context.
     *
     * @var string
     */
    protected $context;

    /**
     * Create a new HTML string instance.
     *
     * @param  string  $text
     * @param  string  $info
     * @return void
     */
    public function __construct($text, $context = 'info')
    {
        $this->text($text)
             ->context($context)
             ->class('help-block');
    }

    /**
     * Set the context of the class.
     *
     * @param string $context
     * @return \Application\Form\Field
     */
    public function context($context)
    {
        $this->context = $context;

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
                ':text' => $this->text,
                ':class' => $this->class,
                ':context' => $this->context,
            ]
        );
    }
}
