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
       * Returns a DateController object for when to stop considering records
       *
       * @param DateController $now     A DateController for the current time
       * @param \DateInterval  $log_ttl The amount of time that existing logs for this tag are valid, specified as a DateInterval
       *
       * @return DateController
       */
      public function getFloodcheckLogCutoffDate ( DateController $now,
                                                   \DateInterval $log_ttl ): DateController
      {

         // Create a clone of the object we were passed
         $cutoff = clone $now;

         // Subtract the purge interval from the current time
         $cutoff->subtractDateIntervalFromDateTime( $log_ttl );

         // Return it
         return $cutoff;
      }

   }