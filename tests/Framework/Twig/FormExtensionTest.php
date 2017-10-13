<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 28/09/17
 * Time: 14:07
 */

namespace Tests\Framework\Twig;


use Framework\Twig\FormExtension;
use PHPUnit\Framework\TestCase;

class FormExtensionTest extends TestCase {

    /**
     * @var FormExtension
     */
    private $formExtension;

    public function setUp() {
        $this->formExtension = new FormExtension();
    }

    private function trim(string $string) {
        $lines = explode(PHP_EOL, $string);
        $lines = array_map('trim', $lines);
        return implode('', $lines);
    }

    private function assertSimilar(string $expected, string $actual){
        $this->assertEquals($this->trim($expected), $this->trim($actual));
    }

    public function testField() {
        $html = $this->formExtension->field([], 'name', 'demo', 'Titre');
        $this->assertSimilar("
            <div class=\"form-group\">
                <label for=\"name\">Titre</label>
                <input type=\"text\" id=\"name\" name=\"name\" class=\"form-control\" value=\"demo\">
            </div>
        ", $html);
    }

    public function testFieldWithClass() {
        $html = $this->formExtension->field([], 'name', 'demo', 'Titre', ['class' =>'demo']);
        $this->assertSimilar("
            <div class=\"form-group\">
                <label for=\"name\">Titre</label>
                <input type=\"text\" id=\"name\" name=\"name\" class=\"form-control demo\" value=\"demo\">
            </div>
        ", $html);
    }

    public function testTextarea() {
        $html = $this->formExtension->field([], 'name', 'demo', 'Titre', ['type' => 'textarea']);
        $this->assertSimilar("
            <div class=\"form-group\">
                <label for=\"name\">Titre</label>
                <textarea id=\"name\" name=\"name\" class=\"form-control\" rows=\"10\">demo</textarea>
            </div>
        ", $html);
    }

    public function testFieldWithErrors() {
        $context = ['errors' => ['name' => 'erreur']];
        $html = $this->formExtension->field($context, 'name', 'demo', 'Titre');
        $this->assertSimilar("
            <div class=\"form-group has-danger\">
                <label for=\"name\">Titre</label>
                <input type=\"text\" id=\"name\" name=\"name\" class=\"form-control form-control-danger\" value=\"demo\">
                <small class=\"form-text text-muted\">erreur</small>
            </div>
        ", $html);
    }

    public function testSelect() {
        $html = $this->formExtension->field([], 'name', 2, 'Titre', ['options' => ['1' => 'Demo', '2' => 'Demo2']]);
        $this->assertSimilar('
             <div class="form-group">
                <label for="name">Titre</label>
                <select id="name" name="name" class="form-control">
                    <option value="1">Demo</option>
                    <option value="2" selected>Demo2</option>
                </select>
            </div>
        ', $html);
    }

}