<?php

namespace Core\Application\Form\Elements;

use Core\Application\Form\Entities\FieldContext;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class TextInput extends HtmlString
{
    /**
     * Default HTML tag templates.
     *
     * @var array
     */
    protected $template = '<div class="form-group">:label<input id=":id" name=":name" type=":type" value=":value" class=":class" :attributes>:help-block</div>';

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
     * The label tag.
     *
     * @var string
     */
    protected $label;

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
     * @param  string  $type
     * @return void
     */
    public function __construct(string $name, string $type = 'text')
    {
        $this->id($name)
             ->name($name)
             ->type($type)
             ->template()
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
    public function name($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the type of the input field.
     *
     * @param string $type
     * @return \Application\Form\Field
     */
    public function type($type = null)
    {
        $this->type = $type;

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
        $this->value = $value ?? Request::old($this->name);

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
     * Set the label of the input field.
     *
     * @param string $label
     * @return \Application\Form\Field
     */
    public function label($label = null)
    {
        $this->label = $label ? with(new LabelField($label, $this->id))->toHtml() : null;

        return $this;
    }

    /**
     * Set the helpblock element.
     *
     * @return string
     */
    public function helpBlock()
    {
        $errors = Session::get('errors');
        $message = $errors ? $errors->first($this->name) : null;
        $context = $errors ? FieldContext::DANGER : FieldContext::NUETRAL;

        return $message ? with(new HelpBlock($message, $context))->toHtml() : null;
    }

    /**
     * Retrieve the HTML template of the input tag.
     *
     * @return $this
     */
    public function template()
    {
        $this->template = file_get_contents(config('forms.fields.text', resource_path('views/fields/input.template.html')));

        return $this;
    }

    /**
     * Get the HTML string.
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->template = strtr(
            $this->template,
            [
                ':id' => $this->id,
                ':name' => $this->name,
                ':type' => $this->type,
                ':label' => $this->label,
                ':value' => $this->value,
                ':class' => $this->class,
                ':attributes' => $this->attributes,
                ':help-block' =>  $this->helpBlock(),
            ]
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
