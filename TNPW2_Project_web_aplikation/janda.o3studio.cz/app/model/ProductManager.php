<?php

namespace App\Model;

use Nette;

/**
 * Product devices management.
 */
class ProductManager {

    use Nette\SmartObject;

    const
            TABLE_NAME = 'products';

    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    public function findAll() {
        return $this->database
                        ->where("exist", 1)
                        ->table(self::TABLE_NAME)
                        ->fetchAll();
    }

    public function add($data) {
        $this->database
                ->table(self::TABLE_NAME)
                ->insert($data);
    }

    public function findAllOfUser($user_id) {
        return $this->database
                        ->table(self::TABLE_NAME)
                        ->where("exist", 1)
                        ->where("user_id", $user_id)
                        ->fetchAll();
    }

    public function findId($id) {
        return $this->database
                        ->table(self::TABLE_NAME)
                        ->wherePrimary($id)
                        ->fetch();
    }

    public function update($id, $data) {
        $this->database
                ->table(self::TABLE_NAME)
                ->wherePrimary($id)
                ->update($data);
    }

}
