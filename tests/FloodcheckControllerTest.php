<?php

namespace Grayl\Test\Input;

use Grayl\Gateway\PDO\PDOPorter;
use Grayl\Input\Floodcheck\Controller\FloodcheckController;
use Grayl\Input\Floodcheck\FloodcheckPorter;
use PHPUnit\Framework\TestCase;

/**
 * Test class for the Floodcheck package
 *
 * @package Grayl\Input\Floodcheck
 */
class FloodcheckControllerTest extends
    TestCase
{

    /**
     * Test setup for sandbox environment
     */
    public static function setUpBeforeClass(): void
    {

        // Change the PDO API environment to sandbox mode
        PDOPorter::getInstance()
            ->setEnvironment('sandbox');
    }


    /**
     * Tests the creation of a FloodcheckController object
     *
     * @return FloodcheckController
     * @throws \Exception
     */
    public function testCreateFloodcheckController(): FloodcheckController
    {

        // Set a unique tag
        $tag = "test_" . $this->generateHash(10);

        // Create a FloodcheckController
        $floodcheck = FloodcheckPorter::getInstance()
            ->newFloodcheckController(
                $tag,
                3,
                'PT1M'
            );

        // Check the type of object created
        $this->assertInstanceOf(
            FloodcheckController::class,
            $floodcheck
        );

        // Return it
        return $floodcheck;
    }


    /**
     * Tests a FloodcheckController that should pass with no error
     *
     * @param FloodcheckController $floodcheck A FloodcheckController entity to test
     *
     * @depends testCreateFloodcheckController
     * @return FloodcheckController
     * @throws \Exception
     */
    public function testFloodcheckControllerSuccess(
        FloodcheckController $floodcheck
    ): FloodcheckController {

        // Make sure the first check passes
        $this->assertFalse($floodcheck->isFloodcheckExceeded());

        // Insert the log, three times
        $floodcheck->saveFloodcheckLog();
        $floodcheck->saveFloodcheckLog();
        $floodcheck->saveFloodcheckLog();

        // Return the controller
        return $floodcheck;
    }


    /**
     * Tests a FloodcheckController that should fail with error
     *
     * @param FloodcheckController $floodcheck A FloodcheckController entity to test
     *
     * @depends testFloodcheckControllerSuccess
     * @throws \Exception
     */
    public function testFloodcheckControllerFailure(
        FloodcheckController $floodcheck
    ): void {

        // Tell the tester we expect an exception to be thrown
        $this->expectException(\Exception::class);

        // Make sure the first check fails
        $this->assertTrue($floodcheck->isFloodcheckExceeded());

        // Insert the log, throwing an exception from too many attempts
        $floodcheck->saveFloodcheckLog();
    }


    /**
     * Generates a unique testing hash
     *
     * @param int $length The length of the hash
     *
     * @return string
     */
    private function generateHash(int $length): string
    {

        // Generate a random string
        $hash = openssl_random_pseudo_bytes($length);

        // Convert the binary data into hexadecimal representation and return it
        $hash = strtoupper(bin2hex($hash));

        // Trim to length and return
        return substr(
            $hash,
            0,
            $length
        );
    }

}
