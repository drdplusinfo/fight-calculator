<?php declare(strict_types=1);

namespace Granam\Tests\ExceptionsHierarchy\Exceptions\DummyExceptionsHierarchy\CanNotBeLoadedByAutoLoader\WithWronglyNamedInterface;

interface AutoLoaderCanNotFindMeBecauseSomeoneNamedMeWrongly extends Runtime
{

}