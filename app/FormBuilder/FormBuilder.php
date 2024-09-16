<?php 
declare(strict_types = 1);

namespace App\FormBuilder;

class FormBuilder{
    /** @var array<string> The HTML form fields */
    private array $fields = [];

    /** @var FormMethod The form method */
    private FormMethod $method = FormMethod::GET;

    /** @var string The URL to send the form to */
    private string $action = '';

    /** @var array Form attributes */
    private array $formAttributes = [];

    public function __construct(array $formAttributes = []){
        $this->formAttributes = $formAttributes;
    }

    /**
     * Set the form method.
     *
     * @param FormMethod $method
     * @return FormBuilder The current object
     */
    public function setMethod(FormMethod $method): static{
        $this->method = $method;
        return $this;
    }

    /**
     * Set the form action value.
     *
     * @param string $action
     * @return FormBuilder The current object
     */
    public function setAction(string $action): static{
        $this->action = $action;
        return $this;
    }

    /**
     * Create an input field.
     * 
     * @param $name
     * @param InputType $type
     * @return FormBuilder Current object
     */
    public function addInput(string $name, InputType $type = InputType::TEXT, array $attributes = []): static{
        $field = "<input type='{$type->value}' name='{$name}'";
        $field = $this->addAttributes($field, $attributes) . ">";
        $this->fields[] = $field;
        return $this;
    }

    /**
     * Add textarea field in the form.
     * 
     * @param string $name
     * @param string $value
     * @param array $attributes
     * @return FormBuilder
     */
    public function addTextArea(string $name, string $value = '', array $attributes = []): static{
        $field = "<textarea name='{$name}'";
        $field = $this->addAttributes($field, $attributes) . ">{$value}</textarea>";
        $this->fields[] = $field;
        return $this;
    }

    /**
     * Add button field in the form.
     * 
     * @param string $name
     * @param string $value
     * @param array $attributes
     * @return FormBuilder
     */
    public function addButton(string $name, string $value, array $attributes = []): static{
        $field = "<button name='{$name}'";
        $field = $this->addAttributes($field, $attributes);
        $field .= ">{$value}</button>";
        $this->fields[] = $field;
        return $this;
    }

    /**
     * Add a select field with options.
     *
     * @param string $name
     * @param array $options
     * @param array $attributes
     * @return static
     */
    public function addSelect(string $name, array $options = [], ?string $selectedValue = null, array $attributes = []): static{
        $field = "<select name='{$name}'";
        $field = $this->addAttributes($field, $attributes) . ">";
        $htmlOptions = $this->getHtmlSelectOptions($options, $selectedValue);
        $field .= $htmlOptions . "</select>";

        $this->fields[] = $field;
        return $this;
    }

    /**
     * Generate html options for the select field.
     *
     * @param array $options
     * @return string
     */
    protected function getHtmlSelectOptions(array $options, ?string $selectedValue): string{
        $html = "";
        foreach($options as $value => $label){
            $selected = ($value == $selectedValue)? 'selected': '';
            $html .= "<option value='{$value}' {$selected}>{$label}</option>";
        }
        return $html;
    }

    /**
     * Add radio buttons.
     * 
     * @param string $name
     * @param array $options
     * @param string $selctedValue
     * @param array $attributes
     * @todo Write a test for this method
     */
    public function addRadioButtons(string $name, array $options = [], ?string $selectedValue = null, array $attributes = []): static{
        $fields = "";
        foreach($options as $value => $label){
            $selected = ($selectedValue == $value)? 'checked': '';
            $fields .= "<label><input type='radio' name='{$name}' value='{$value}' {$selected}";
            $fields = $this->addAttributes($fields, $attributes);
            $fields .= "> {$label}</label>";
        }
        $this->fields[] = $fields;
        return $this;
    }

    /**
     * Add a checkbox.
     *
     * @param string $name
     * @param string $value
     * @param string $label
     * @param boolean $isChecked
     * @param array $attributes
     * @return static
     */
    public function addCheckbox(string $name, string $value, string $label, bool $isChecked = false, array $attributes = []): static{
        $selected = ($isChecked)? 'checked': '';
        $field = "<label><input type='checkbox' name='{$name}' value='{$value}' {$selected}";
        $field = $this->addAttributes($field, $attributes);
        $field .= "> {$label}</label>";
        $this->fields[] = $field;
        return $this;
    }

    /**
     * Add a horizontal rule divider.
     *
     * @param integer $size
     * @return static
     */
    public function addDivider(int $size = 1): static{
        $this->fields[] = "<hr size='{$size}'>";
        return $this;
    }

    /**
     * Add a heading in the form.
     *
     * @param integer $level The heading size (1-6)
     * @param string $text The heading text
     * @param array $attributes
     * @return static
     */
    public function addHeading(int $level, string $text, array $attributes = []): static{
        $heading = "<h{$level}";
        $heading = $this->addAttributes($heading, $attributes);
        $heading .= ">{$text}</h{$level}>";
        $this->fields[] = $heading;
        return $this;
    }

    /**
     * Add a div container.
     *
     * @param array $attributes
     * @return static
     * @todo Write a test for this method
     */
    public function addContainer(array $attributes = []): static{
        $this->fields[] = $this->addAttributes("<div", $attributes) . ">";
        return $this;
    }

    /**
     * Close a div container.
     * 
     * @return static
     * @todo Write a test for this method
     */
    public function closeContainer(): static{
        $this->fields[] = "</div>";
        return $this;
    }

    /**
     * Get the HTML form.
     *
     * @return string The HTML form
     * @todo Write a test for this method
     */
    public function getForm(): string{
        $htmlForm = "<form method='{$this->method->value}' action='{$this->action}' class='formbuilder'";
        $htmlForm = $this->addAttributes($htmlForm, $this->formAttributes) . ">";
        $htmlForm .= implode("", $this->fields);
        $htmlForm .= "</form>";
        return $htmlForm;
    }

    /**
     * Get form fields without the form element as a parent element.
     * 
     * You get the bare elements.
     * 
     * @return string The form elements
     * @todo Write a test for this method
     */
    public function getFormFields(): string{
        return implode("", $this->fields);
    }

    /**
     * Add the attributes in the associative array to the form field.
     *
     * @param string $field The form field being created
     * @param array $attrs The attributes to add to the field
     * @return string The field with the added attributes
     * @todo Write a test for this method
     */
    private function addAttributes(string $field, array $attrs): string{
        if(count($attrs) == 0) 
            return $field;

        if($field[-1] !== ' ')
            $field = $field . ' ';

        foreach($attrs as $attrName => $attrValue){
            $field .= "{$attrName}='{$attrValue}' ";
        }

        return trim($field);
    }

    /**
     * Add HTML attributes statically.
     *
     * @param string $field
     * @param array $attributes
     * @return string
     * @todo Write a test for this method
     */
    private static function staticAddAttributes(string $field, array $attributes = []): string{
        return (new static)->addAttributes($field, $attributes) . '>';
    }

    /**
     * Add an error container with all the errors.
     * 
     * The container is a div element with a list of errors 
     * that occured in the current form. If the errors array
     * is empty the errors container won't created.
     * 
     * @param array<string> $errors All the form errors
     * @return string
     * @todo Write a test for this method
     */
    public static function addFormErrors(array $errors = [], array $attributes = []): string{
        if(count($errors) == 0) 
            return '';

        $el = "<div class='formbuilder-errors' ";
        $el = FormBuilder::staticAddAttributes($el, $attributes);
        $el .= ">";
        $el .= "<ul>";
        foreach($errors as $error){
            $el .= "<li>" . $error . "</li>";
        }
        $el .= "</ul></div>";

        return $el;
    }
}