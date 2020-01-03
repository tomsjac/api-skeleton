<?php
namespace App\services\tools\auth;

/**
 * Interface Token
 *
 * @author thomas
 */
interface Itoken
{
    public function hydrate($object);

    public function getRaw();

    public function getData();

    public function getScope();

    public function getExpired();

    public function getCreate();

    public function isInit();

    public function hasScope(array $scope);

    public static function generate(array $data, array $scope, int $expire, string $secret);
}
