<?php

   namespace Grayl\Input\Floodcheck;

   use Grayl\Database\Main\DatabasePorter;
   use Grayl\Date\DatePorter;
   use Grayl\Input\Floodcheck\Controller\FloodcheckController;
   use Grayl\Input\Floodcheck\Entity\FloodcheckLog;
   use Grayl\Input\Floodcheck\Service\FloodcheckService;
   use Grayl\Input\Floodcheck\Storage\FloodcheckDatabaseMapper;
   use Grayl\Mixin\Common\Traits\StaticTrait;

   /**
    * Front-end for the Floodcheck package
    *
    * @package Grayl\Input\Floodcheck
    */
   class FloodcheckPorter
   {

      // Use the static instance trait
      use StaticTrait;

      /**
       * Creates a new FloodcheckController
       *
       * @param string $tag                The general tag for the log
       * @param int    $max_attempts       The amount of duplicate attempts allowed for this log
       * @param string $floodcheck_log_ttl The amount of time (in date interval format) that duplicate submissions are valid (EX: PT5M = 5 minutes)
       *
       * @return FloodcheckController
       * @throws \Exception
       */
      public function newFloodcheckController ( string $tag,
                                                int $max_attempts,
                                                string $floodcheck_log_ttl ): FloodcheckController
      {

         // Create a new FloodcheckLog
         $floodcheck_log = new FloodcheckLog( DatePorter::getInstance()
                                                        ->newDateController( null ),
                                              $tag,
                                              $_SERVER[ 'REMOTE_ADDR' ] );

         // Convert the log_tll into a date interval
         $floodcheck_log_ttl_interval = DatePorter::getInstance()
                                                  ->newDateInterval( $floodcheck_log_ttl );

         // Return a new FloodcheckController
         return new FloodcheckController( $floodcheck_log,
                                          $floodcheck_log_ttl_interval,
                                          $max_attempts,
                                          new FloodcheckService(),
                                          new FloodcheckDatabaseMapper( 'input_floodcheck',
                                                                        DatabasePorter::getInstance() ) );
      }

   }