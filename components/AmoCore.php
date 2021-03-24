<?php

namespace Pixelion\AmoCrm\components;

class AmoCore
{
    protected $types = [
        self::NOTE_DEAL_CREATED => 'Сделка создана',
        self::NOTE_CONTACT_CREATED => 'Контакт создан',
        self::NOTE_DEAL_STATUS_CHANGED => 'Статус сделки изменен',
        self::NOTE_COMMON => 'Обычное примечание',
        self::NOTE_ATTACHMENT => 'Файл',
        self::NOTE_CALL => 'Звонок приходящий от iPhone-приложений',
        self::NOTE_EMAIL_MESSAGE => 'Письмо',
        self::NOTE_EMAIL_ATTACHMENT => 'Письмо с файлом',
        self::NOTE_CALL_IN => 'Входящий звонок',
        self::NOTE_CALL_OUT => 'Исходящий звонок',
        self::NOTE_COMPANY_CREATED => 'Компания создана',
        self::NOTE_TASK_RESULT => 'Результат по задаче',
        self::NOTE_SYSTEM => 'Системное сообщение',
        self::NOTE_SMS_IN => 'Входящее смс',
        self::NOTE_SMS_OUT => 'Исходящее смс',
    ];


    /**
     * @const int Типа задачи Контакт
     */
    const TYPE_CONTACT = 1;

    /**
     * @const int Типа задачи Сделка
     */
    const TYPE_LEAD = 2;

    /** @const int Типа задачи Компания */
    const TYPE_COMPANY = 3;

    /** @const int Типа задачи Задача */
    const TYPE_TASK = 4;

    /** @const int Типа задачи Покупатель */
    const TYPE_CUSTOMER = 12;







    const NOTE_DEAL_CREATED = 1;
    const NOTE_CONTACT_CREATED = 2;
    const NOTE_DEAL_STATUS_CHANGED = 3;
    const NOTE_COMMON = 4;
    const NOTE_ATTACHMENT = 5;
    const NOTE_CALL = 6;
    const NOTE_EMAIL_MESSAGE = 7;
    const NOTE_EMAIL_ATTACHMENT = 8;
    const NOTE_CALL_IN = 10;
    const NOTE_CALL_OUT = 11;
    const NOTE_COMPANY_CREATED = 12;
    const NOTE_TASK_RESULT = 13;
    const NOTE_SYSTEM = 25;
    const NOTE_SMS_IN = 102;
    const NOTE_SMS_OUT = 103;

    //add panix
    const NOTE_MAIL = 15;

    public function call()
    {

    }
}