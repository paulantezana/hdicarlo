<?php

class AppPaymentInterval extends Model
{
    public function __construct(PDO $connection)
    {
        parent::__construct('app_payment_intervals', 'app_payment_interval_id', $connection);
    }
}
