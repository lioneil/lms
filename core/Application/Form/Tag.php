<?php

namespace Core\Application\Form;

class Tag extends Field
{
    /**
     * The HTML tag type.
     *
     * @var string
     */
    protected $type;

    /**
     * Create a new HTML string instance.
     *
     * @param  string  $name
     * @return void
     */
    public function __construct($type)
    {
        $this->type($type);
    }

    /**
     * Set the type of the input field.
     *
     * @param string $type
     * @return \Application\Form\Field
     */
    public function type($type = 'div')
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the HTML string.
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->type();
    }

    /**
     * @param string $method
     * @param array $attributes
     */
    public function __call($method, $attributes)
    {
        return call_user_func_array([$this, $method], $attributes);
    }
}
