<?php

namespace Core\Application\Form\Elements;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

abstract class BaseElement extends HtmlString
{
    /**
     * Default HTML tag templates.
     *
     * @var array
     */
    protected $template = '<input id="%s" name="%s" type="text" value="%s" class="%s" %s>';

    /**
     * The id attribute of
     * the HTML tag.
     *
     * @var string
     */
    protected $id;

    /**
     * The name attribute of
     * the HTML tag.
     *
     * @var string
     */
    protected $name;

    /**
     * The html text of
     * the HTML tag.
     *
     * @var string
     */
    protected $text;

    /**
     * The type attribute of
     * the HTML tag.
     *
     * @var string
     */
    protected $type;

    /**
     * The value attribute of
     * the HTML tag.
     *
     * @var string
     */
    protected $value;

    /**
     * The class attribute of
     * the HTML tag.
     *
     * @var string
     */
    protected $class;

    /**
     * The attributes attribute of
     * the HTML tag.
     *
     * @var string
     */
    protected $attributes;

    /**
     * Create a new HTML string instance.
     *
     * @param  string  $name
     * @return void
     */
    public function __construct($name)
    {
        $this->id($name)
             ->name($name)
             ->value()
             ->class();
    }

    /**
     * Set the id of the input field.
     *
     * @param string $id
     * @return \Application\Form\Field
     */
    public function id($id = null)
    {
        $this->id = $id ?? 'field-'.time();

        return $this;
    }

    /**
     * Set the name of the input field.
     *
     * @param string $name
     * @return \Application\Form\Field
     */
    public function name($name = 'text')
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the name of the input field.
     *
     * @param string $text
     * @return \Application\Form\Field
     */
    public function text($text = 'Text')
    {
        $this->text = __($text);

        return $this;
    }

    /**
     * Set the value of the input field.
     *
     * @param string $value
     * @return \Application\Form\Field
     */
    public function value($value = null)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Set the class of the input field.
     *
     * @param string $class
     * @return \Application\Form\Field
     */
    public function class($class = 'form-control')
    {
        $this->class = $class;

        return $this;
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
            $this->id, $this->name, $this->value, $this->class,
            $this->attributes
        );
    }

    /**
     * Magically build other attributes.
     *
     * @param string $name
     * @param array $arguments
     * @return \Application\Form\Field
     */
    public function __call($name, $arguments)
    {
        $this->attributes .= sprintf('%s=%s', Str::kebab($name), json_encode($arguments[0] ?? true));

        return $this;
    }
}
