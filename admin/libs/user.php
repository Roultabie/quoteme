<?php

class userQueries
{
    private static $table;
    public $config;
    public static $nbResult;
    private $user;
    public $datas;

    function __construct()
    {
        $this->getDatas();
        if (empty($this->user->private_token)) {
            $this->updateToken('private', $this->user->id);
        }
        if (empty($this->user->share_token)) {
            $this->updateToken('share', $this->user->id);
        }
    }

    /* Users generation for login */
    public static function initLogin()
    {
        self::$table = $GLOBALS['config']['tblPrefix'] . 'users';
        $query = 'SELECT id, hash, username, private_token, share_token, email, shaarli, type
                  FROM ' . self::$table;
        $stmt = dbConnexion::getInstance()->prepare($query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($users as $user) {
            $GLOBALS['config']['users'][$user['id']] = $user;
        }
        $stmt = NULL;
    }

    public function getDatas($id = '')
    {
        if (empty($id)) {
            $userObject = unserialize($_SESSION['userDatas']);
            $userConfig = $userObject->getConfig();
            $this->config = $userConfig;
            $id = $userConfig['id'];
            $query  = 'SELECT id, username, private_token, share_token, email, shaarli, type
                   FROM ' . self::$table .' WHERE id = "' . $id . '"' ;
            $stmt = dbConnexion::getInstance()->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            $stmt = NULL;
            $this->user = (object) $result[0];
            $this->datas = (object) $result[0];
        }
        elseif ($this->user['level'] === 0) {
            $query  = 'SELECT username, private_token, share_token, email, shaarli, type
                   FROM ' . self::$table .' WHERE id = "' . $id . '"';
            $stmt = dbConnexion::getInstance()->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            $stmt = NULL;
            return $result;
        }
    }

    public function getConfig()
    {
        return $this->user;
    }

    public function updateToken($type, $user)
    {
        if ($type === 'private') {
            $this->updateData('private_token', $this->random(35), $user);
        }
        if ($type === 'share') {
            $this->updateData('share_token', $this->random(35), $user);
        }
    }

    public function updatePassword($password, $confirm, $user)
    {
        if ($password === $confirm) {
            $hash = userWriter::returnHash($password);
            if (!empty($hash)) {
                updateData('hash', $hash, $user);
            }
        }
    }

    public function updateLevel($level, $user)
    {
        if (is_int($level)) {
            updateData('type', $level, $user);
        }
    }

    public function updateEmail($email, $user)
    {
        updateData('email', $email, $user);
    }

    public function updateShaarli($shaarli, $user)
    {
        updateData('shaarli', $shaarli, $user);
    }

    private function updateData($field, $data, $userId)
    {
        $stmt = dbConnexion::getInstance()->prepare('UPDATE ' . self::$table .'
                                            SET ' . $field . ' = :field
                                            WHERE id = :userId');
        if (is_int($data)) {
            $stmt->bindValue(':field', $data, PDO::PARAM_INT);
        }
        else {
            $stmt->bindValue(':field', $data, PDO::PARAM_STR);
        }
        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
        $stmt->execute();
    }

    private function random($len)
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $byteLen = intval(($len / 2) + 1);
            $result  = substr(bin2hex(openssl_random_pseudo_bytes($byteLen)), 0, $len);
        }
        elseif (@is_readable('/dev/urandom')) {
            $f       = fopen('/dev/urandom', 'r');
            $urandom = fread($f, $len);
            fclose($f);
            $result = '';
        }

        if (empty($result)) {
            for ($i = 0; $i < $len; ++$i) {
                if (!isset($urandom)) {
                    if ($i % 2 == 0) {
                        mt_srand(time() % 2147 * 1000000 + (double)microtime() * 1000000);
                    }
                    $rand = 48 + mt_rand() % 64;
                }
                else {
                    $rand = 48 + ord($urandom[$i]) % 64;
                }
                if ($rand > 57) $rand += 7;
                if ($rand > 90) $rand += 6;
                if ($rand == 123) $rand = 52;
                if ($rand == 124) $rand = 53;
                $result .= chr($rand);
            }
        }
        return $result;
    }
}