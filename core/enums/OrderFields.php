<?php

namespace app\core\enums;

/** Парсинг полей заказа */
enum OrderFields: string {
    case date = 'дата оформления заказа';
    case delivery = 'способ доставки';
    case delivery_date = 'дата доставки';
    case delivery_price = 'цена доставки';
    case comment = 'комментарий';
    case delivery_address = 'адрес доставки';
    case number = 'номер заказа';
    case payment = 'способ оплаты';
    case price = 'цена';
    case status = 'статус заказа';
}
