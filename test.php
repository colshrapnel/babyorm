<?php
use BabyORM\DataMapper;
use BabyORM\DB;

require __DIR__.'/vendor/autoload.php';

$db = new DB("mysql", "127.0.0.1", "root", "", "test");

$db->pdo->query("create temporary table dmtestuser (id int primary key auto_increment, name varchar(60), email varchar(60), password varchar(60))");

class User {}

class UserMapper extends DataMapper
{
    protected $class = 'User';
    protected $table = 'dmtestuser';
    protected $fields = ['name','email','password'];
}
$dm = new UserMapper($db);

$user = new User();
$user->name = "Joe";
$dm->save($user);

$dm->delete($user);

$user = new User();
$user->name = "Bob";
$dm->save($user);

$id = $user->id;

$user = $dm->find($id);
$user->name = "Jane";
$dm->save($user);

$userList = $dm->findBySql("SELECT * FROM dmtestuser");
var_dump($userList);
