<?php

namespace Grayl\Input\Floodcheck\Controller;

use Grayl\Input\Floodcheck\Entity\FloodcheckLog;
use Grayl\Input\Floodcheck\Service\FloodcheckService;
use Grayl\Input\Floodcheck\Storage\FloodcheckDatabaseMapper;

/**
 * Class FloodcheckController
 * The controller for working with floodcheck logs from the database
 *
 * @package Grayl\Input\Floodcheck
 */
class FloodcheckController
{

    /**
     * The FloodcheckLog instance to interact with
     *
     * @var FloodcheckLog
     */
    private FloodcheckLog $floodcheck_log;

    /**
     * The amount of time that existing logs for this tag are valid, specified as a DateInterval
     *
     * @var \DateInterval
     */
    private \DateInterval $floodcheck_log_ttl;

    /**
     * The amount of matching attempts allowed for this log
     *
     * @var int
     */
    private int $max_attempts;

    /**
     * The FloodcheckService instance to interact with
     *
     * @var FloodcheckService
     */
    private FloodcheckService $floodcheck_service;

    /**
     * The FloodcheckDatabaseMapper instance to interact with
     *
     * @var FloodcheckDatabaseMapper
     */
    private FloodcheckDatabaseMapper $database_mapper;


    /**
     * The class constructor
     *
     * @param FloodcheckLog            $floodcheck_log     The FloodcheckLog instance to work with
     * @param \DateInterval            $floodcheck_log_ttl The amount of time that existing logs for this tag are valid, specified as a DateInterval
     * @param int                      $max_attempts       The amount of duplicate attempts allowed for this log
     * @param FloodcheckService        $floodcheck_service The FloodcheckService instance to use
     * @param FloodcheckDatabaseMapper $database_mapper    The FloodcheckDatabaseMapper instance to interact with
     */
    public function __construct(
        FloodcheckLog $floodcheck_log,
        \DateInterval $floodcheck_log_ttl,
        int $max_attempts,
        FloodcheckService $floodcheck_service,
        FloodcheckDatabaseMapper $database_mapper
    ) {

        // Set the class data
        $this->floodcheck_log     = $floodcheck_log;
        $this->floodcheck_log_ttl = $floodcheck_log_ttl;
        $this->max_attempts       = $max_attempts;

        // Set the service entity
        $this->floodcheck_service = $floodcheck_service;

        // Set the database mapper
        $this->database_mapper = $database_mapper;
    }


    /**
     * Checks the floodcheck database for matching records to see if the max attempts is exceeded
     *
     * @return bool
     * @throws \Exception
     */
    public function isFloodcheckExceeded(): bool
    {

        // Get the cutoff date based on the log creation time
        $cutoff = $this->floodcheck_service->getFloodcheckLogCutoffDate(
            $this->floodcheck_log->getCreated(),
            $this->floodcheck_log_ttl
        );

        // Use the service to check
        return $this->database_mapper->isFloodcheckExceeded(
            $this->floodcheck_log,
            $cutoff,
            $this->max_attempts
        );
    }


    /**
     * Saves the current FloodcheckLog to the database if the attempts hasn't been exceeded
     *
     * @throws \Exception
     */
    public function saveFloodcheckLog(): void
    {

        // Get the cutoff date based on the log creation time
        $cutoff = $this->floodcheck_service->getFloodcheckLogCutoffDate(
            $this->floodcheck_log->getCreated(),
            $this->floodcheck_log_ttl
        );

        // Use the database mapper to save this log and perform a check
        $this->database_mapper->saveFloodcheckLog(
            $this->floodcheck_log,
            $cutoff,
            $this->max_attempts
        );
    }

}