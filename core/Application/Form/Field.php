<?php

namespace Core\Application\Form;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Field extends HtmlString
{
    /**
     * The type attribute of
     * the HTML tag.
     *
     * @var string
     */
    protected $type;

    /**
     * Create a new HTML string instance.
     *
     * @param  string $name
     * @return void
     */
    public function __construct($name)
    {
        $this->name($name);
    }

    /**
     * Set the name of the input field.
     *
     * @param  string $name
     * @return \Application\Form\Field
     */
    public function name($name = 'text')
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the type of the input field.
     *
     * @param  string $type
     * @return \Application\Form\Field
     */
    public function type($type = 'text')
    {
        $this->type = strtolower($type);

        switch ($this->type) {
            case Entities\FieldType::EMAIL:
            case Entities\FieldType::PASSWORD:
            case Entities\FieldType::TEXT:
                return with(new Elements\TextInput($this->name, $this->type));
                break;

            case Entities\FieldType::CHECKBOX:
            case Entities\FieldType::RADIO:
                return with(new Elements\CheckboxField($this->name, $this->type));
                break;

            case Entities\FieldType::SUBMIT:
                return with(new Elements\SubmitButton($this->name));
                break;

            default:
                break;
        }

        throw new Exceptions\FieldNotSupportedException("Field [{$this->type}] is unsupported", 1);
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
     * @param  string $method
     * @param  array  $attributes
     * @return \Application\Form\Field
     */
    public function __call($method, $attributes)
    {
        return call_user_func_array([$this->type(), $method], $attributes);
    }
}
