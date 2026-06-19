<?php

namespace app\core\enums;

enum ResponseMessage: string {
    case ERROR_PRODUCT_NOT_FOUND = 'Товар не найден';
    case ERROR_CATALOG_NOT_FOUND = 'Товары не найдены';
    case ERROR_ORDER_NOT_FOUND = 'Заказ не найден';
    case ERROR_NOT_ENOUGH_DATA = 'Не достаточно данных';
    case ERROR_DATA = 'Некорректные данные';
    case ERROR_GET_DATA = 'Ошибка при получении данных';
    case ERROR_NEW_USER = 'Ошибка при добавлении нового пользователя';
    case ERROR_AUTH_PHONE_DATA = 'Неверный номер телефона или пароль';
    case ERROR_AUTH_LOGIN_DATA = 'Неверный логин или пароль';
    case ERROR_AUTH = 'Ошибка авторизации';
    case ERROR_NOT_AUTH = 'Не авторизирован';
    case ERROR_PERMISSIONS = 'Недостаточно прав';
    case ERROR_UPDATE = 'Не удалось обновить данные';
    case ERROR_ADD = 'Не удалось добавить данные';
    case ERROR_ADD_PRODUCTS = 'Не удалось добавить товары';
    case ERROR_ADD_PRODUCT = 'Не удалось добавить товар';
    case ERROR_ADD_FILTERS = 'Не удалось добавить новые фильтры';
    case ERROR_DELETE = 'Не удалось удалить позиции!';
    case ERROR_DUPLICATE = 'Не удалось добавить данные. Позиция уже существует!';
    case ERROR_CREATE_ORDER = 'Не удалось создать заказ';
    case ERROR_USER_PHONE_ISSET = 'Пользователь с таким номером зарегистрирован!';
    case ERROR_UPDATE_STATUS = 'Не удалось обновить статус';
    case ERROR_TOKEN = 'Истек токен авторизации';
    case SUCCESS_SUBSCRIBE = 'Вы успешно подписаны на обновления!';
    case SUCCESS_FORM = 'Ваша заявка в обработке';
    case SUCCESS_AUTH = 'Успешная авторизация';
    case SUCCESS_LOGOUT = 'Успешная деавторизация';
    case SUCCESS_ADD = 'Успешное добавление';
    case SUCCESS_ADD_BASKET = 'Товар добавлен в корзину';
    case SUCCESS_ADD_FAVORITES = 'Товар добавлен в избранное';
    case SUCCESS_REMOVE = 'Успешное удаление';
    case SUCCESS_REMOVE_ITEMS = 'Успешное удаление позиций';
    case SUCCESS_REMOVE_ORDERS = 'Заказы успешно удалены!';
    case SUCCESS_REMOVE_BASKET = 'Товар удален из корзины';
    case SUCCESS_REMOVE_FAVORITES = 'Товар удален из избранного';
    case SUCCESS_CLEAR_FAVORITES = 'Избранное отчищено';
    case SUCCESS_CLEAR_BASKET = 'Корзина очищена';
    case SUCCESS_CREATE_ORDER = 'Заказ успешно создан!';
    case SUCCESS_UPDATE_DATA = 'Данные успешно обновленны!';
    case SUCCESS_UPDATE_STATUS = 'Статус обновлен!';
    case SUCCESS_EDIT = 'Данные отредактированы';
    case SUCCESS_PARSED = 'Товары успешно добавлены';
    case USER_AUTH = 'Пользователь авторизован';
    case USER_ALREADY_AUTH = 'Пользователь уже авторизован';
}