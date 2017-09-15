<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 15/09/17
 * Time: 09:36
 */

namespace Tests\Framework;



use Framework\Renderer;
use PHPUnit\Framework\TestCase;

class RendererTest extends TestCase {

    /**
     * @var Renderer
     */
    private $renderer;

    public function setUp() {
        $this->renderer = new Renderer();
    }

    public function testRenderTheRightPath() {
        $this->renderer->addPath('blog', __DIR__ .'/views');
        $content = $this->renderer->render('@blog/demo');
        $this->assertEquals('Salut', $content);
    }

    public function testRenderDefaultPath() {
        $this->renderer->addPath(__DIR__ .'/views');
        $content = $this->renderer->render('demo');
        $this->assertEquals('Salut', $content);
    }

    public function testRenderWithParams() {
        $this->renderer->addPath(__DIR__ .'/views');
        $content = $this->renderer->render('demoParams', ['nom' => 'Marc']);
        $this->assertEquals('Salut Marc', $content);
    }

    public function testGlobalParameters() {
        $this->renderer->addGlobal('nom', 'Marc');
        $content = $this->renderer->render('demoParams');
        $this->assertEquals('Salut Marc', $content);
    }

}