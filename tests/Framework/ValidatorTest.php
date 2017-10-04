<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 27/09/17
 * Time: 13:44
 */

namespace Tests\Framework;


use Framework\Validator;
use PHPUnit\Framework\TestCase;
use Tests\DatabaseTestCase;

class ValidatorTest extends DatabaseTestCase {

    public function testRequiredFailed() {
        $errors = $this->makeValidator([
            'name' => 'joe'
        ])
            ->required('name', 'content')
            ->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals("Le champ content est requis", (string)$errors['content']);
    }

    public function testRequiredSuccess() {
        $errors = $this->makeValidator([
            'name' => 'joe',
            'content' => 'content'
        ])
            ->required('name', 'content')
            ->getErrors();
        $this->assertCount(0, $errors);
    }

    public function testNotEmpty() {
        $errors = $this->makeValidator([
            'name' => 'joe',
            'content' => ''
        ])
            ->notEmpty('name', 'content')
            ->getErrors();
        $this->assertCount(1, $errors);
    }

    public function testSlugFailed() {
        $errors = $this->makeValidator([
            'slug1' => 'aze-aze-azeazaAaz99az',
            'slug2' => 'aze-aze_azeazaAaz99az',
            'slug3' => 'aze-aze--azeazaAaz99az',
            'slug4' => 'aze-aze-azeazaAaz99az-',
        ])
            ->slug('slug1')
            ->slug('slug2')
            ->slug('slug3')
            ->slug('slug4')
            ->getErrors();
        $this->assertEquals(['slug1', 'slug2', 'slug3', 'slug4'], array_keys($errors));
    }

    public function testSlugSuccess() {
        $errors = $this->makeValidator([
            'slug1' => 'aze-aze-azeazazaz99az',
            'slug2' => 'azeaze'
        ])
            ->slug('slug1')
            ->slug('slug2')
            ->getErrors();
        $this->assertCount(0, $errors);
    }

    public function testLength() {
        $params = ['slug' => '123456789'];
        $this->assertCount(0, $this->makeValidator($params)->length('slug', 3)->getErrors());
        $errors = $this->makeValidator($params)->length('slug', 12)->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals("Le champ slug doit contenir plus de 12 caractères", (string)$errors['slug']);
        $this->assertCount(1, $this->makeValidator($params)->length('slug', 3, 4)->getErrors());
        $this->assertCount(0, $this->makeValidator($params)->length('slug', 3, 20)->getErrors());
        $this->assertCount(0, $this->makeValidator($params)->length('slug', null, 20)->getErrors());
        $this->assertCount(1, $this->makeValidator($params)->length('slug', null, 8)->getErrors());
    }

    public function testDatetime() {
        $this->assertCount(0, $this->makeValidator(['date' => '2012-12-12 11:12:13'])->dateTime('date')->getErrors());
        $this->assertCount(0, $this->makeValidator(['date' => '2012-12-12 00:00:00'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2012-21-12'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2013-02-29 11:12:13'])->dateTime('date')->getErrors());
    }

    public function testExists() {
        $pdo = $this->getPDO();
        $pdo->exec('CREATE TABLE test (
          id INTEGER PRIMARY KEY AUTOINCREMENT,
          name VARCHAR(255)
        )');
        $pdo->exec('INSERT INTO test (name) VALUES ("a1")');
        $pdo->exec('INSERT INTO test (name) VALUES ("a2")');
        $this->assertTrue($this->makeValidator(['category' => 1])->exists('category', 'test', $pdo)->isValid());
        $this->assertFalse($this->makeValidator(['category' => 12345])->exists('category', 'test', $pdo)->isValid());
    }

    private function makeValidator(array $params) {
        return new Validator($params);
    }

}