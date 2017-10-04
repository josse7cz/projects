<?php

namespace App\Model;

use Nette;

/**
 * Product devices management.
 */
class BasketManager {

    use Nette\SmartObject;

    const
            TABLE_NAME = 'basket';

    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    public function changeAmount($user_id, $bid, $amount) {
        $data["amount"] = $amount;
        $this->database
                ->table(self::TABLE_NAME)
                ->where("user_id", $user_id)
                ->where("id", $bid)
                ->update($data);
    }

    public function vytvorUctenku($placeno, $fik, $bkp, $pkp, $user_id) {

        $now = new \DateTime();
        $data["datetime"] = $now->format("Y-m-d H:i:s");
        $data["placeno"] = $placeno;
        $data["user_id"] = $user_id;
        $data["fik"] = $fik;
        $data["bkp"] = $bkp;
        $data["pkp"] = $pkp;

        $receiptRow = $this->database->table("receipts")
                ->insert($data);

        unset($data);
        $data["receipt_id"] = $receiptRow->id;

        $selectionBasketRows = $this->database
                ->table(self::TABLE_NAME)
                ->where("user_id", $user_id)
                ->where("receipt_id IS NULL")
                ->update($data);
        $basketRows = $this->database
                ->table(self::TABLE_NAME)
                ->where("receipt_id", $receiptRow->id)
                ->fetchAll();
        foreach ($basketRows as $br) {
            $productManager = new ProductManager($this->database);
            $product = $productManager->findId($br->product_id);
            unset($data);
            $data["amount"] = $product->amount - $br->amount;
            $productManager->update($br->product_id, $data);
        }

        return $receiptRow->id;
    }

    public function findReceipt($user_id, $id) {
        return $this->database
                        ->table("receipts")
                        ->where("user_id", $user_id)
                        ->wherePrimary($id)
                        ->fetch();
    }

    public function findAll() {
        return $this->database
                        ->table(self::TABLE_NAME)
                        ->fetchAll();
    }

    public function add($data) {

        $data["price"] = $this->database
                        ->table("products")
                        ->where("user_id", $data["user_id"])
                        ->where("id", $data["product_id"])
                        ->fetch()->price;

        $this->database
                ->table(self::TABLE_NAME)
                ->insert($data);
    }

    public function findAllReceiptsOfUser($user_id) {
        return $this->database
                        ->query("
SELECT sum(B.price * B.amount) AS celkem, R.id, R.datetime, B.product_id FROM (SELECT * FROM receipts WHERE user_id=?) AS R
JOIN basket AS B ON R.id = B.receipt_id GROUP BY R.id"
                                , $user_id)
                        ->fetchAll();
    }

    public function findAllOfUser($user_id, $receiptId = null) {
        $receiptWhere = "receipt_id IS NULL";
        if ($receiptId !== null) {
            $receiptWhere = "receipt_id = " . floatval($receiptId);
        }
        return $this->database
                        ->query("SELECT B.amount, P.name, P.price, B.id AS bid FROM basket AS B JOIN products AS P ON B.product_id = P.id WHERE B.user_id = ? AND " . $receiptWhere, $user_id)
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

    public function delete($id) {
        $this->database
                ->table(self::TABLE_NAME)
                ->wherePrimary($id)
                ->delete();
    }

}
