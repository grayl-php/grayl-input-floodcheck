<?php

   namespace Grayl\Input\Floodcheck\Service;

   use Grayl\Date\Controller\DateController;

   /**
    * Class FloodcheckService
    * The service for working with floodchecks
    *
    * @package Grayl\Input\Floodcheck
    */
   class FloodcheckService
   {

      /**
       * Subtracts a log TTl (DateInterval) from a DateController to get an expiration date
       *
       * @param DateController $now     A DateController for the current time
       * @param \DateInterval  $log_ttl The amount of time this log is valid, specified as a DateInterval
       */
      public function setFloodcheckLogExpirationDate ( DateController $now,
                                                       \DateInterval $log_ttl ): void
      {

         // Subtract the ttl interval from the current time
         $now->addDateIntervalToDateTime( $log_ttl );
      }

   }