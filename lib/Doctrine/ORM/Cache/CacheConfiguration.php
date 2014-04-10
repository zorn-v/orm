<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\ORM\Cache;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Cache\Logging\CacheLogger;

/**
 * Configuration container for second-level cache.
 *
 * @since   2.5
 * @author  Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class CacheConfiguration
{
    /**
     * @var \Doctrine\ORM\Cache\CacheFactory|null
     */
    private $cacheFactory;

    /**
     * @var \Doctrine\ORM\Cache\RegionsConfiguration|null
     */
    private $regionsConfig;

    /**
     * @var \Doctrine\ORM\Cache\Logging\CacheLogger|null
     */
    private $cacheLogger;

    /**
     * @var \Doctrine\ORM\Cache\QueryCacheValidator|null
     */
    private $queryValidator;

    /**
     * @var callable|null
     */
    private $cacheInstantiator;

    /**
     * @var string
     */
    private $cacheClassName = 'Doctrine\ORM\Cache\DefaultCache';

    /**
     * @return \Doctrine\ORM\Cache\CacheFactory|null
     */
    public function getCacheFactory()
    {
        return $this->cacheFactory;
    }

    /**
     * @param \Doctrine\ORM\Cache\CacheFactory $factory
     *
     * @return void
     */
    public function setCacheFactory(CacheFactory $factory)
    {
        $this->cacheFactory = $factory;
    }

    /**
     * @return \Doctrine\ORM\Cache\Logging\CacheLogger|null
     */
    public function getCacheLogger()
    {
         return $this->cacheLogger;
    }

    /**
     * @param \Doctrine\ORM\Cache\Logging\CacheLogger $logger
     */
    public function setCacheLogger(CacheLogger $logger)
    {
        $this->cacheLogger = $logger;
    }

    /**
     * @return \Doctrine\ORM\Cache\QueryCacheValidator
     */
    public function getRegionsConfiguration()
    {
        if ($this->regionsConfig === null) {
            $this->regionsConfig = new RegionsConfiguration();
        }

        return $this->regionsConfig;
    }

    /**
     * @param \Doctrine\ORM\Cache\RegionsConfiguration $regionsConfig
     */
    public function setRegionsConfiguration(RegionsConfiguration $regionsConfig)
    {
        $this->regionsConfig = $regionsConfig;
    }

    /**
     * @return \Doctrine\ORM\Cache\QueryCacheValidator
     */
    public function getQueryValidator()
    {
        if ($this->queryValidator === null) {
            $this->queryValidator = new TimestampQueryCacheValidator();
        }

         return $this->queryValidator;
    }

    /**
     * @param \Doctrine\ORM\Cache\QueryCacheValidator $validator
     */
    public function setQueryValidator(QueryCacheValidator $validator)
    {
        $this->queryValidator = $validator;
    }

    /**
     * @param callable $instantiator responsible of retrieving an {@see \Doctrine\ORM\Cache} instance given
     *                               a {@see \Doctrine\ORM\EntityManagerInterface} instance
     *
     * @throws ORMException if the given instantiator is not a valid callable
     */
    public function setCacheInstantiator($instantiator)
    {
        if ( ! is_callable($instantiator)) {
            throw ORMException::invalidSecondLevelCacheInstantiator($instantiator);
        }

        $this->cacheInstantiator = $instantiator;
    }

    /**
     * @return callable that
     */
    public function getCacheInstantiator()
    {
        if ( ! $this->cacheInstantiator) {
            $this->cacheInstantiator = function (EntityManagerInterface $entityManager) {
                return new DefaultCache($entityManager);
            };
        }

        return $this->cacheInstantiator;
    }

    /**
     * @param string $className
     *
     * @throws \Doctrine\ORM\ORMException If is not a \Doctrine\ORM\Cache
     */
    public function setCacheClassName($className)
    {
        $reflectionClass = new \ReflectionClass($className);

        if ( ! $reflectionClass->implementsInterface('Doctrine\ORM\Cache')) {
            throw ORMException::invalidSecondLevelCache($className);
        }

        $this->cacheClassName = $className;
    }

    /**
     * @return string A \Doctrine\ORM\Cache class name
     */
    public function getCacheClassName()
    {
        return $this->cacheClassName;
    }
}
