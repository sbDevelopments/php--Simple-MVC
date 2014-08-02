<?php

class UserModel extends Model
{

    protected static $usersList = array(
        array('id' => 1, 'login' => 'admin', 'authkey' => 'admin', 'name' => 'Главный администратор', 'isAdmin' => TRUE),
        array('id' => 2, 'login' => 'demo', 'authkey' => 'demo', 'name' => 'Демо пользователь', 'isAdmin' => FALSE),
        array('id' => 3, 'login' => 'user', 'authkey' => 'user', 'name' => 'Пользователь, просто пользователь', 'isAdmin' => FALSE),
        array('id' => 4, 'login' => 'test', 'authkey' => 'test', 'name' => 'Тестовый пользователь', 'isAdmin' => FALSE),
        array('id' => 5, 'login' => 'root', 'authkey' => 'R00t', 'name' => 'Самый самый главный', 'isAdmin' => FALSE),
    );

    public function __construct(array $properties = array())
    {
        parent::__construct();

        foreach ($properties as $name => $val) {
            $this->$name = $val;
        }
    }

    /**
     * @return array Возвращает список пользователей в виде массива
     * Ключ авторизации удаляется из элементов массива
     */
    public static function getList()
    {
        $st = self::db()->query('SELECT * FROM '.APP_DB_PREFIX.'users');
        if($st){
            $arr = $st->fetchAll(PDO::FETCH_ASSOC);
        }

        array_walk($arr, function (&$val, $key) {
            unset($val['authkey']);
        });
        return $arr;
    }

    /**
     * Находит первого пользователя, который подпадает под указанные условия поиска
     * Если пользователь не найден - вернет null
     * @param array $condition Массив пар поле пользователя - значение
     * @return null|UserModel
     */
    public static function findBy(array $condition)
    {
        $query = 'SELECT * FROM '.APP_DB_PREFIX.'users';
        if(!empty($condition)){
            $query .= ' WHERE ';

            $whereConditions = array();
            foreach($condition as $key => $val){
                $whereConditions[] = "$key = :$key";
            }
            $query .= implode(' AND ', $whereConditions);
        }
        $st = self::db()->prepare($query);
        $stResult = $st->execute($condition);
        $uData = $stResult? $st->fetch(PDO::FETCH_ASSOC): null;
        if($stResult && $uData){
            return new self($uData);
        }

        return null;
    }
} 