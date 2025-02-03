<?php

namespace Vyuldashev\LaravelOpenApi\Builders\Paths\Operation;

use GoldSpecDigital\ObjectOrientedOAS\Objects\SecurityRequirement;
use Vyuldashev\LaravelOpenApi\Attributes\Operation as OperationAttribute;
use Vyuldashev\LaravelOpenApi\Attributes\SecurityRequirement as SecurityRequirementAttribute;
use Vyuldashev\LaravelOpenApi\RouteInformation;

class SecurityBuilder
{
    public function build(RouteInformation $route): array
    {
        return $route->actionAttributes
            ->filter(static fn (object $attribute) => $attribute instanceof OperationAttribute || $attribute instanceof SecurityRequirementAttribute)
            ->filter(static fn (OperationAttribute|SecurityRequirementAttribute $attribute) => $attribute instanceof SecurityRequirementAttribute || isset($attribute->security))
            ->map(static function (OperationAttribute|SecurityRequirementAttribute $attribute) {
                if ($attribute instanceof SecurityRequirementAttribute) {
                    if (!$attribute->scheme) {
                        return SecurityRequirement::create()->securityScheme(null);
                    }
                    $factory = app($attribute->scheme);
                    $scheme = $factory->build();

                    $requirement = SecurityRequirement::create()->securityScheme($scheme);

                    if ($attribute->scopes) {
                        $requirement = $requirement->scopes(...$attribute->scopes);
                    }

                    return $requirement;
                } else {
                    // return a null scheme if the security is set to ''
                    if ($attribute->security === '') {
                        return SecurityRequirement::create()->securityScheme(null);
                    }
                    $security = app($attribute->security);
                    $scheme = $security->build();

                    return SecurityRequirement::create()->securityScheme($scheme);
                }
            })
            ->values()
            ->toArray();
    }
}