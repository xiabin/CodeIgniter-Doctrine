<?php
/**
 * Created by PhpStorm.
 * User: binxia
 * Date: 2016/1/26
 * Time: 11:53
 */


use Doctrine\Common\ClassLoader,
    Doctrine\Common\Annotations\Annotation,
    Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager;

/**
 * Class Doctrine
 */
class Doctrine
{
    public $em;

    public function __construct()
    {
        // load database configuration from CodeIgniter
        require_once APPPATH . 'config/database.php';

        include_once APPPATH . 'third_party/Doctrine/vendor/autoload.php';

        // load the entities
        $entityClassLoader = new ClassLoader('Entity', APPPATH . 'models');
        $entityClassLoader->register();
        // load the proxy entities
        $proxiesClassLoader = new ClassLoader('Proxies', APPPATH . 'models');
        $proxiesClassLoader->register();

        // Set up the configuration
        $config = new Configuration;

        // Set up caches
        if (ENVIRONMENT == 'development')  // set environment in index.php
            // set up simple array caching for development mode
            $cacheDriver = new \Doctrine\Common\Cache\ArrayCache;
        else {
            // set up caching with APC for production mode
            $redis = new Redis();
            $redis->connect('redis_host', 'redis_port');
            $cacheDriver = new \Doctrine\Common\Cache\RedisCache();
            $cacheDriver->setRedis($redis);
        }
        $config->setMetadataCacheImpl($cacheDriver);
        $config->setQueryCacheImpl($cacheDriver);

        $driverImpl = $config->newDefaultAnnotationDriver(APPPATH . 'models/Entity');
        $config->setMetadataDriverImpl($driverImpl);

        // Proxy configuration
        $config->setProxyDir(APPPATH . '/models/Proxies');
        $config->setProxyNamespace('Proxies');

        // Set up logger
        if (ENVIRONMENT == 'development') {
            $logger = new \Doctrine\DBAL\Logging\EchoSQLLogger();
            $config->setSQLLogger($logger);
        }


        $config->setAutoGenerateProxyClasses(true); // only for development


        $connectionOptions = array(
            'driver' => 'pdo_mysql',
            'user' => $db['default']['username'],
            'password' => $db['default']['password'],
            'host' => $db['default']['hostname'],
            'dbname' => $db['default']['database'],
            'charset' => $db['default']['char_set'],
            'driverOptions' => array(
                'charset' => $db['default']['char_set'],
            ),
        );
        $this->em = EntityManager::create($connectionOptions, $config);
//        $connection_options = array(
//            'driver'        => 'pdo_mysql',
//            'user'          => $db['default']['username'],
//            'password'      => $db['default']['password'],
//            'host'          => $db['default']['hostname'],
//            'dbname'        => $db['default']['database'],
//            'charset'       => $db['default']['char_set'],
//            'driverOptions' => array(
//                'charset'   => $db['default']['char_set'],
//            ),
//        );
//
//        // With this configuration, your model files need to be in application/models/Entity
//        // e.g. Creating a new Entity\User loads the class from application/models/Entity/User.php
//        $models_namespace = 'Entity';
//        $models_path = APPPATH . 'models';
//        $proxies_dir = APPPATH . 'models/Proxies';
//        $metadata_paths = array(APPPATH . 'models/Entity');
//
//        // Set $dev_mode to TRUE to disable caching while you develop
//        $dev_mode = true;
//
//        // If you want to use a different metadata driver, change createAnnotationMetadataConfiguration
//        // to createXMLMetadataConfiguration or createYAMLMetadataConfiguration.
//        $config = Setup::createAnnotationMetadataConfiguration($metadata_paths, $dev_mode, $proxies_dir);
//        $this->em = EntityManager::create($connection_options, $config);
//
//        $loader = new ClassLoader($models_namespace, $models_path);
//        $loader->register();
    }


}