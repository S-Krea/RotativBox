<?php

namespace App\ParamResolver;

use App\Model\Box;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

#[AutoconfigureTag('controller.argument_value_resolver', ['priority'=>150])]
class BoxParamResolver implements ValueResolverInterface
{

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->supports($argument)) {
            return [];
        }

        return $this->apply($request);
    }

    protected function supports(ArgumentMetadata $argumentMetadata)
    {
        return $argumentMetadata->getType() === Box::class;
    }

    protected function apply(Request $request)
    {
        return [$request->getSession()->get(Box::BOX_SESSION_KEY, null)];
    }
}