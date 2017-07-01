<?php
/**
 * This file is part of the MobileDetectModule package.
 * 
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 * 
 * For full copyright and license information, please refer to the LICENSE file, 
 * located at the package root folder.
 */

namespace MobileDetectModuleTest\Service;

use Detection\MobileDetect;
use MobileDetectModule\Service\MobileDetectFactory;
use MobileDetectModuleTest\ServiceManagerFactory;
use PHPUnit\Framework\TestCase;
use Zend\Http\PhpEnvironment\Request;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class MobileDetectFactoryTest extends TestCase
{
    /**
     * @var MobileDetectFactory 
     */
    protected $factory;
    
    protected function setUp()
    {
        $this->factory = new MobileDetectFactory();
    }

    /**
     * @test
     */
    public function it_initializes_mobile_detect_with_http_request_data()
    {
        $request = new Request();
        $request->setServer(new Parameters(array(
            'HTTP_HOST' => 'www.example.com',
            'HTTP_USER_AGENT' => 'test'
        )));
        $container = new ServiceManager([
            'services' => [
                'Request' => $request,
            ],
        ]);

        $mobileDetect = $this->factory->__invoke($container);

        $this->assertSame('www.example.com', $mobileDetect->getHttpHeader('Host'));
        $this->assertSame('test', $mobileDetect->getUserAgent());
    }

    /**
     * @test
     */
    public function it_raises_exception_if_request_not_in_container()
    {
        try {
            $this->factory->__invoke(new ServiceManager());

            $this->fail('Exception should have been raised');
        } catch (ServiceNotCreatedException $ex) {
            $this->assertSame('Request object required for creating MobileDetect was not found in the container', $ex->getMessage());
        }
    }
    
    public function it_creates_mobile_detect_from_module_manager()
    {
        $container = ServiceManagerFactory::getServiceManager();
        
        $mobileDetect = $this->factory->__invoke($container);
        
        $this->assertInstanceOf(MobileDetect::class, $mobileDetect);
    }
}
