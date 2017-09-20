<?php
/**
 * Created by PhpStorm.
 * User: amacabr2
 * Date: 20/09/17
 * Time: 11:17
 */

namespace Tests;


use PDO;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class DatabaseTestCase extends TestCase {

    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var Manager
     */
    private $manager;

    public function setUp() {

        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $configArray = require('phinx.php');
        $configArray['environments']['test'] = [
            'adapter' => 'sqlite',
            'connection' => $this->pdo
        ];

        $config = new Config($configArray);
        $this->manager = new Manager($config, new StringInput(' '), new NullOutput());
        $this->manager->migrate('test');
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

    }

    public function seedDatabase() {
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);
        $this->manager->seed('test');
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    }

}