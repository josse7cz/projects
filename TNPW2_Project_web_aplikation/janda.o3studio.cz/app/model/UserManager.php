<?php

namespace App\Model;

use Nette;
use Nette\Security\Passwords;

/**
 * Users management.
 */
class UserManager implements Nette\Security\IAuthenticator {

    use Nette\SmartObject;

    const
            TABLE_NAME = 'users',
            COLUMN_ID = 'id',
            COLUMN_NAME = 'username',
            COLUMN_PASSWORD_HASH = 'password',
            COLUMN_EMAIL = 'email',
            COLUMN_ROLE = 'role';

    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    public function findById($id) {
        return $this->database
                        ->table(self::TABLE_NAME)
                        ->wherePrimary($id)
                        ->fetch();
    }

    public function update($id, $data) {
        return $this->database
                        ->table(self::TABLE_NAME)
                        ->wherePrimary($id)
                        ->update($data);
    }

    /**
     * Performs an authentication.
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials) {
        list($username, $password) = $credentials;

        $row = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_NAME, $username)->fetch();

        if (!$row) {
            throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
        } elseif (!Passwords::verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
            throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
        } elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
            $row->update([
                self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
            ]);
        }

        $arr = $row->toArray();
        unset($arr[self::COLUMN_PASSWORD_HASH]);
        return new Nette\Security\Identity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
    }

    public function loginOverride($id) {
        $row = $this->database->table(self::TABLE_NAME)->wherePrimary($id)->fetch();

        if (!$row) {
            throw new Nette\Security\AuthenticationException('The user id is incorrect.', self::IDENTITY_NOT_FOUND);
        }

        $arr = $row->toArray();

        return new Nette\Security\Identity($row[self::COLUMN_ID], explode(",", $row[self::COLUMN_ROLE]), $arr); //Role odděleny ,
    }

    public function changePassword($user_id, $password) {
        $data["password"] = Passwords::hash($password);

        $this->database
                ->table(self::TABLE_NAME)
                ->wherePrimary($user_id)
                ->update($data);
    }

    /**
     * Adds new user.
     * @param  string
     * @param  string
     * @param  string
     * @return void
     * @throws DuplicateNameException
     */
    public function add($username, $email, $password) {
        try {
            $this->database->table(self::TABLE_NAME)->insert([
                self::COLUMN_NAME => $username,
                self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
                self::COLUMN_EMAIL => $email,
            ]);
        } catch (Nette\Database\UniqueConstraintViolationException $e) {
            throw new DuplicateNameException;
        }
    }

    public function findByEmail($email) {
        return $this->database
                        ->table(self::TABLE_NAME)
                        ->where("email", $email)
                        ->fetch();
    }

    public function findByHash($hash) {
        return $this->database
                        ->table(self::TABLE_NAME)
                        ->where("hash", $hash)
                        ->fetch();
    }

    public function findAll() {
        return $this->database
                        ->table(self::TABLE_NAME)
                        ->fetchAll();
    }

    public function delete($id) {
        $this->database
                ->table(self::TABLE_NAME)
                ->wherePrimary($id)
                ->delete();
    }

    public function findByFbId($fbid) {
        return $this->database
                        ->table(self::TABLE_NAME)
                        ->where("fbid", $fbid)
                        ->fetch();
    }

    public function addFbuser($fbid, $email) {
        $data["fbid"] = $fbid;
        $data["username"] = $email;
        $data["password"] = "xxx";
        $data["email"] = $email;

        $existing = $this->database->table(self::TABLE_NAME)->where("username", $data["username"])->fetch();
        if ($existing === false) {

            $this->database
                    ->table(self::TABLE_NAME)
                    ->insert($data);
        } else {
            unset($data["password"]);
            $existing->update($data);
        }
    }

}

class DuplicateNameException extends \Exception {
    
}
