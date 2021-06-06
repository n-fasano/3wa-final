<?php

namespace App\Controller\Dto\Base;

use App\Controller\Dto\DataTransferObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class DtoArgumentValueResolver implements ArgumentValueResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $interfaces = class_implements($argument->getType());
        return in_array(DataTransferObject::class, $interfaces);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        yield Builder::build($argument->getType(), $request);
    }
}