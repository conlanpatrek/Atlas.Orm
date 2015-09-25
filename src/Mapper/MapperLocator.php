<?php
/**
 *
 * This file is part of the Aura Project for PHP.
 *
 * @package Atlas.Atlas
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 */
namespace Atlas\Mapper;

use Atlas\Exception;

/**
 *
 * A ServiceLocator implementation for loading and retaining Mapper objects;
 * note that new Mappers cannot be added after construction.
 *
 * @package Atlas.Atlas
 *
 */
class MapperLocator
{
    /**
     *
     * A registry of callable factories to create Mapper instances.
     *
     * @var array
     *
     */
    protected $factories = [];

    /**
     *
     * A registry of Mapper instances created by the factories.
     *
     * @var array
     *
     */
    protected $instances = [];

    public function set($class, callable $factory)
    {
        $this->factories[$class] = $factory;
    }

    public function has($class)
    {
        return isset($this->factories[$class]);
    }

    /**
     *
     * Gets a Mapper instance by class; if it has not been created yet, its
     * callable factory will be invoked and the instance will be retained.
     *
     * @param string $class The class of the Mapper instance to retrieve.
     *
     * @return Mapper A Mapper instance.
     *
     * @throws Exception When an Mapper type is not found.
     *
     */
    public function get($class)
    {
        if (! isset($this->factories[$class])) {
            throw new Exception("{$class} not found in locator");
        }

        if (! isset($this->instances[$class])) {
            $factory = $this->factories[$class];
            $this->instances[$class] = call_user_func($factory);
        }

        return $this->instances[$class];
    }
}