<?php

namespace BogdanKovachev\Borica;

/**
 * @author Bogdan Kovachev (https://1337.bg)
 */
class TransactionType {

    /**
     * Плащане
     */
    const SALE = 1;

    /**
    * Първоначална авторизация
    */
    const DEFERRED_AUTHORIZATION = 12;

    /**
     * Завършване на първоначална авторизация
     */
    const COMPLETE_DEFERRED_AUTHORIZATION = 21;

    /**
     * Отмяна на първоначална авторизация
     */
    const REVERSE_DEFERRED_AUTHORIZATION = 22;

    /**
     * Отмяна на плащане
     */
    const REVERSAL = 24;

    /**
     * Проверка за статус на трансакция
     */
    const STATUS_CHECK = 90;

}
