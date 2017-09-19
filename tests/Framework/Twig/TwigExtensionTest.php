<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 19/09/17
 * Time: 11:29
 */

namespace Tests\Framework\Twig;


use Framework\Twig\TextExtension;
use PHPUnit\Framework\TestCase;

class TwigExtensionTest extends TestCase {

    /**
     * @var TextExtension
     */
    private $textExtension;

    public function setUp() {
        $this->textExtension = new TextExtension();
    }

    public function testExcerptWithShortText() {
        $text = "Salut";
        $this->assertEquals($text, $this->textExtension->excerpt($text, 10));
    }

    public function testExcerptWithLongText() {
        $text = "Salut les gens";
        $this->assertEquals("Salut...", $this->textExtension->excerpt($text, 7));
        $this->assertEquals("Salut les...", $this->textExtension->excerpt($text, 12));
    }

}