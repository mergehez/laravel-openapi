<?php

namespace Vyuldashev\LaravelOpenApi\Attributes;

use Attribute;
use InvalidArgumentException;
use Vyuldashev\LaravelOpenApi\Factories\SecuritySchemeFactory;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class SecurityRequirement
{
    public ?string $scheme;

    /** @var array<string> */
    public ?array $scopes;

    /**
     * @param  string|null  $scheme
     * @param  array  $scopes
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string|null $scheme, array $scopes = [])
    {
        if ($scheme) {
            $factory = class_exists($scheme) ? $scheme : app()->getNamespace().'OpenApi\\SecuritySchemes\\'.$scheme;

            if (! is_a($factory, SecuritySchemeFactory::class, true)) {
                throw new InvalidArgumentException(
                    sprintf('Security class is either not declared or is not an instance of %s', SecuritySchemeFactory::class)
                );
            }
        }

        $this->scheme = $scheme;
        $this->scopes = $scopes;
    }
}
