<?php

namespace App\Constants;

class Status{

    const ENABLE  = 1;
    const DISABLE = 0;

    const YES = 1;
    const NO  = 0;

    const VERIFIED   = 1;
    const UNVERIFIED = 0;

    const PAYMENT_INITIATE = 0;
    const PAYMENT_SUCCESS  = 1;
    const PAYMENT_PENDING  = 2;
    const PAYMENT_REJECT   = 3;

    CONST TICKET_OPEN   = 0;
    CONST TICKET_ANSWER = 1;
    CONST TICKET_REPLY  = 2;
    CONST TICKET_CLOSE  = 3;

    CONST PRIORITY_LOW    = 1;
    CONST PRIORITY_MEDIUM = 2;
    CONST PRIORITY_HIGH   = 3;

    const USER_ACTIVE = 1;
    const USER_BAN    = 0;

    const KYC_UNVERIFIED = 0;
    const KYC_VERIFIED   = 1;
    const KYC_PENDING    = 2;

    const ACTIVE   = 1;
    const INACTIVE = 0;

    const APPOINTMENT_COMPLETE   = 1;
    const APPOINTMENT_INCOMPLETE = 0;

    const APPOINTMENT_CASH_PAYMENT    = 0;
    const APPOINTMENT_PAID_PAYMENT    = 1;
    const APPOINTMENT_PENDING_PAYMENT = 2;

    const APPOINTMENT_CONFIRM_PENDING = 0;
    const APPOINTMENT_CONFIRM_DONE = 1;
}
