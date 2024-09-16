<?php
declare(strict_types = 1);

namespace Tests\Unit\FormBuilder;

use App\FormBuilder\InputType;
use App\FormBuilder\FormMethod;
use PHPUnit\Framework\TestCase;
use App\FormBuilder\FormBuilder;

class FormBuilderTest extends TestCase{
    private FormBuilder $formBuilder;

    public function setUp(): void{
        parent::setUp();

        $this->formBuilder = new FormBuilder();
    }

    /** @test */
    public function itSetsRequestMethod(){
        $this->formBuilder->setMethod(FormMethod::POST);

        $strPos = strpos($this->formBuilder->getForm(), "method='post'");
        $this->assertTrue($strPos !== false);
    }

    /** @test */
    public function itSetsFormAction(){
        $this->formBuilder->setAction('testaction');
        $strPos = strpos($this->formBuilder->getForm(), "action='testaction'");
        $this->assertTrue($strPos !== false);
    }

    /** @test */
    public function itAddsInputElement(){
        $expected = "<input type='" . InputType::TEXT->value . "' name='testname'>";
        $this->formBuilder->addInput('testname', InputType::TEXT);
        $this->assertEquals($expected, $this->formBuilder->getFormFields());
    }

    /** @test */
    public function itAddsTextareaElement(){
        $testName = 'testname';
        $testContent = 'content';
        $expected = "<textarea name='{$testName}'>{$testContent}</textarea>";
        $this->assertEquals($expected, $this->formBuilder->addTextArea($testName, $testContent)->getFormFields());
    }

    /** @test */
    public function itAddsButton(){
        $name = "buttonname";
        $value = "submit";
        $expected = "<button name='{$name}'>{$value}</button>";

        $this->formBuilder->addButton($name, $value);
        $this->assertEquals($expected, $this->formBuilder->getFormFields());
    }

    /** @test */
    public function itAddsFormSelectElement(){
        $name = "testname";
        $expected = "<select name='{$name}'></select>";

        $this->formBuilder->addSelect($name);
        $this->assertEquals($expected, $this->formBuilder->getFormFields());
    }

    /** @test */
    public function itAddsFormSelectElementWithOptions(){
        $name = "testname";
        $options = [
            'test' => 'Test',
            'value' => 'Label'
        ];
        $selectedValue = 'value';
        $expected = "<select name='{$name}'>" 
        . "<option value='test' >Test</option>"
        . "<option value='value' selected>Label</option>"
        . "</select>";

        $this->formBuilder->addSelect($name, $options, $selectedValue);
        $this->assertEquals($expected, $this->formBuilder->getFormFields());
    }

    /**
     * @test
     * @todo Implement method
     */
    public function itAddsFormRadioButtons(){

    }

    /** @test */
    public function itAddsFormCheckboxElement(){
        $name = 'test';
        $value = 'value';
        $label = 'label';
        $expected = "<label><input type='checkbox' name='{$name}' value='{$value}' checked> {$label}</label>";

        $this->formBuilder->addCheckbox($name, $value, $label, true);
        $this->assertEquals($expected, $this->formBuilder->getFormFields());
    }

    /** @test */
    public function itCreatesADivider(){
        $expected = "<hr size='1'>";
        $this->formBuilder->addDivider();
        $this->assertEquals($expected, $this->formBuilder->getFormFields());
    }

    /**
     * @test
     * @dataProvider addHeadingProvider
     */
    public function itCreatesAllHeadings(int $headingLevel, string $headingText){
        $expected = "<h{$headingLevel}>{$headingText}</h{$headingLevel}>";
        $this->formBuilder->addHeading($headingLevel, $headingText);
        $this->assertEquals($expected, $this->formBuilder->getFormFields());
    }

    protected function addHeadingProvider(){
        return [
            [1, 'Heading 1'],
            [2, 'Heading 2'],
            [3, 'Heading 3'],
            [4, 'Heading 4'],
            [5, 'Heading 5'],
            [6, 'Heading 6'],
        ];
    }

    /** @test */
    public function itCanCreateAForm(){
        $expected = "<form method='get' action='' class='formbuilder'></form>";
        $this->assertEquals($expected, $this->formBuilder->getForm());
    }

    /** @test */
    public function itGetsEmptyFields(){
        $this->assertEmpty($this->formBuilder->getFormFields());
    }

    /** @test */
    public function itAddsAttributes(){
        $expected = "<form method='get' action='' class='formbuilder' id='id'></form>";
        $formBuilder = new FormBuilder(['id' => 'id']);
        $this->assertEquals($expected, $formBuilder->getForm());
    }
}