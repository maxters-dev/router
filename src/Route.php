<?php

namespace Maxters\Router;

use Maxters\Router\Exceptions\RouteDoesNotMatchException;

/**
 * 
 * @author Wallace de Souza <wallacemaxters@gmail.com>
 */
class Route
{

    /**
     * @var array<string,string>
     */
    protected $wheres = [];

    public function __construct(
        public readonly string $pattern,
        public readonly HttpVerbs $verb,
        public readonly \Closure $action
    ) {
    }

    /**
     * @return array<string,string>
     */
    protected function getPatternReplacements(): array
    {
        [$params] = $this->getParametersNamesFromPattern();

        $replacements = array_map(fn ($p) => preg_quote($p), $params);

        return $this->wheres + array_fill_keys($replacements, '([a-zA-Z0-9-_]+)');
    }

    public function where(string $key, string $regex): static
    {
        $this->wheres[preg_quote("{{$key}}")] = "({$regex})";

        return $this;
    }

    public function toRegex(): string
    {
        $result = strtr(preg_quote($this->pattern, '/'), $this->getPatternReplacements());

        return '/^' . $result . '$/';
    }

    public function getParametersNamesFromPattern(): array
    {
        preg_match_all('/\{(\w+)\}/', $this->pattern, $matches);

        return $matches;
    }

    public function match(string $pattern): bool
    {
        return preg_match($this->toRegex(), $pattern) > 0;
    }

    public function execute(string $path, array $params = []): mixed
    {
        $pathParams = $this->extractParametersFromPath($path);

        return ($this->action)(...$params, ...$pathParams);
    }

    public function extractParametersFromPath(string $path): array
    {
        if (!preg_match($this->toRegex(), $path, $matches) > 0) {
            throw new RouteDoesNotMatchException(
                "Value {$path} doesnt not match with {$this->pattern}"
            );
        }

        return array_slice($matches, 1);
    }
}
