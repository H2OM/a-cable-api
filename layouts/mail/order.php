<?php
/**
 * >**Обязательные поля:**
 *
 *      [
 *          'main_info' => ['field_name' => 'field_value'],
 *          'order_url' => string,
 *          'site_url' => string,
 *          'products' => [
 *                ['image' => string, 'price' => string|int 'title' => string, 'count' => int]
 *           ]
 *       ]
 *
 *   >**НЕ обязательные поля:**
 *
 *       [
 *           'user' => [
 *               'first_name' => string,
 *               'second_name' => string,
 *               'password' => string,
 *               'personal_url' => string
 *           ]
 *       ]
 *
 * @var $main_info
 * @var $order_url
 * @var $site_url
 * @var $products
 * @var $user
 */
?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ваш заказ уже в пути!</title>
    <style type="text/css">
        html {
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
        }

        td[style*="padding"],
        td[class*="em-mob-width"] {
            box-sizing: border-box;
        }
    </style>
    <style em="styles">
        @media only screen and (max-device-width: 660px), only screen and (max-width: 660px) {
            .em-narrow-table {
                width: 100% !important;
                max-width: 660px !important;
                min-width: 320px !important;
            }

            .em-mob-wrap.em-mob-wrap-cancel {
                display: table-cell !important;
            }

            .em-mob-height-20px {
                height: 20px !important;
            }

            .em-mob-width-100perc {
                width: 100% !important;
                max-width: 100% !important;
            }

            .em-mob-wrap {
                display: block !important;
            }

            .em-mob-padding_left-20 {
                padding-left: 20px !important;
            }

            .em-mob-padding_right-20 {
                padding-right: 20px !important;
            }

            .em-mob-width-auto {
                width: auto !important;
            }

            .em-mob-line_height-16px {
                line-height: 16px !important;
            }

            .em-mob-text_align-center {
                text-align: center !important;
            }

            .em-mob-line_height-18px {
                line-height: 18px !important;
            }

            .em-mob-line_height-24px {
                line-height: 24px !important;
            }

            .em-mob-font_size-18px {
                font-size: 18px !important;
            }

            .em-mob-width-100px {
                width: 100px !important;
                max-width: 100px !important;
                min-width: 100px !important;
            }

            .em-mob-width-80px {
                width: 80px !important;
                max-width: 80px !important;
                min-width: 80px !important;
            }

            .em-mob-font_size-16px {
                font-size: 16px !important;
            }

            .em-mob-font_size-14px {
                font-size: 14px !important;
            }
        }
    </style>
</head>

<body style="margin: 0; padding: 0; background-color: #F8F8F8;" class="null">
<!--[if !mso]><!-->
<div style="font-size:0px;color:transparent;opacity:0;">
</div>
<!--<![endif]-->
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="font-size: 1px; line-height: normal;"
       bgcolor="#F8F8F8">
    <tbody>
    <tr em="group">
        <td align="center" bgcolor="#FCF7EC" style="background-color: #fcf7ec; background-repeat: repeat;">
            <table cellpadding="0" cellspacing="0" width="100%" border="0"
                   style="max-width: 660px; min-width: 660px; width: 660px;" class="em-narrow-table">
                <tbody>
                <tr em="block" class="em-structure">
                    <td align="center" style="padding: 30px 40px;"
                        class="em-mob-padding_left-20 em-mob-padding_right-20">
                        <table border="0" cellspacing="0" cellpadding="0" class="em-mob-width-100perc">
                            <tbody>
                            <tr>
                                <td class="em-mob-wrap em-mob-wrap-cancel em-mob-width-auto">
                                    <!--                                    <img src="https://emcdn.ru/278815/240527_4213_NWSUEbr.png" width="100" border="0"-->
                                    <!--                                         alt="" style="display: block; width: 100%; max-width: 100px;">-->
                                    X_cable
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr em="block" class="em-structure">
                    <td align="center" style="padding: 30px 40px;"
                        class="em-mob-padding_left-20 em-mob-padding_right-20">
                        <table align="center" border="0" cellspacing="0" cellpadding="0" class="em-mob-width-100perc">
                            <tbody>
                            <tr>
                                <td width="580" valign="top" class="em-mob-wrap em-mob-width-100perc">
                                    <table cellpadding="0" cellspacing="0" border="0" width="100%" em="atom">
                                        <tbody>
                                        <tr>
                                            <td align="center">
                                                <img src="https://emcdn.ru/278815/240527_4213_4x4NaRm.png" width="300"
                                                     border="0" alt=""
                                                     style="display: block; width: 100%; max-width: 300px;">
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <table cellpadding="0" cellspacing="0" border="0" width="100%" em="atom">
                                        <tbody>
                                        <tr>
                                            <td style="padding: 20px 0px;">
                                                <div style="font-family: Helvetica, Roboto, Arial, sans-serif; font-size: 24px; line-height: 32px; color: #333333;"
                                                     align="center"><strong>Ваш заказ уже в пути!<br></strong></div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <?php foreach ($main_info as $field => $info) { ?>
                                        <table cellpadding="0" cellspacing="0" border="0" width="100%" em="atom">
                                            <tbody>
                                            <tr>
                                                <td style="padding-bottom: 5px;">
                                                    <div style="font-family: Helvetica, Roboto, Arial, sans-serif; font-size: 18px; line-height: 25px; color: #000000;">
                                                        <?= $field ?>:&nbsp;
                                                        <?php if($field === 'цена') {
                                                            echo number_format(
                                                                    num: $info,
                                                                    decimals: 2,
                                                                    decimal_separator: ',',
                                                                    thousands_separator: ' '
                                                            ) . ' ₽';
                                                        } else {
                                                            echo $info;
                                                        } ?>
                                                        <br>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    <?php } ?>
                                    <table cellpadding="0" cellspacing="0" border="0" width="100%" em="atom">
                                        <tbody>
                                        <tr>
                                            <td style="padding: 20px 0px;" align="center">
                                                <table cellpadding="0" cellspacing="0" border="0" width="205"
                                                       style="width: 205px;">
                                                    <tbody>
                                                    <tr>
                                                        <td align="center" valign="middle" height="41"
                                                            style="background-color: #cb5652; border-radius: 5px; height: 41px;"
                                                            bgcolor="#CB5652">
                                                            <a style="display: block; height: 41px; font-family: -apple-system, 'Segoe UI', 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #ffffff; font-size: 16px; line-height: 41px; text-decoration: none; white-space: nowrap;"
                                                               href="<?= $order_url ?>"
                                                               target="_blank">Отследить заказ <br></a>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <?php if(!empty($user)) { ?>
                                        <table cellpadding="0" cellspacing="0" border="0" width="100%" em="atom">
                                            <tbody><tr>
                                                <td style="padding-bottom: 5px;">
                                                    <div style="font-family: Helvetica, Roboto, Arial, sans-serif; font-size: 18px; line-height: 25px; color: #000000;" align="">
                                                        <?= $user['first_name'] ?> <?= $user['second_name'] ?>,&nbsp;
                                                        <?php if(!empty($user['password'])) { ?>
                                                            для входа в личный кабинет используйте временный пароль - <?= $user['password'] ?>.<br>
                                                        <?php } else { ?>
                                                            вся информация о статусе вашего заказа находится в личном кабинете.
                                                        <?php } ?>

                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <table cellpadding="0" cellspacing="0" border="0" width="100%" em="atom">
                                            <tbody><tr>
                                                <td style="padding: 20px 0px;" align="center">
                                                    <table cellpadding="0" cellspacing="0" border="0" width="205" style="width: 205px;">
                                                        <tbody><tr>
                                                            <td align="center" valign="middle" height="41" style="background-color: #cb5652; border-radius: 5px; height: 41px;" bgcolor="#CB5652">
                                                                <a style="display: block; height: 41px; font-family: -apple-system, 'Segoe UI', 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #ffffff; font-size: 16px; line-height: 41px; text-decoration: none; white-space: nowrap;"
                                                                   href="<?= $user['personal_url'] ?>"
                                                                   target="_blank">
                                                                    Личный кабинет<br>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    <?php } ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr em="block" class="em-structure">
                    <td align="center" style="padding: 20px 40px;"
                        class="em-mob-padding_left-20 em-mob-padding_right-20">
                        <table align="center" border="0" cellspacing="0" cellpadding="0" class="em-mob-width-100perc">
                            <tbody>
                            <tr>
                                <td width="580" valign="top" class="em-mob-wrap em-mob-width-100perc">
                                    <table cellpadding="0" cellspacing="0" border="0" width="100%" em="atom">
                                        <tbody>
                                        <tr>
                                            <td height="0"
                                                style="padding: 20px 0px 0px; border-top: 1px solid #333333;">&nbsp;
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <table cellpadding="0" cellspacing="0" border="0" width="100%" em="atom">
                                        <tbody>
                                        <tr>
                                            <td style="padding-right: 0px; padding-left: 0px;">
                                                <div style="font-family: Helvetica, Roboto, Arial, sans-serif; font-size: 20px; line-height: 28px; color: #333333;">
                                                    <strong>Ваш заказ<br></strong></div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <?php foreach ($products as $product) { ?>
                    <tr em="block" class="em-structure">
                        <td align="center" style="padding: 20px 40px;"
                            class="em-mob-padding_left-20 em-mob-padding_right-20">
                            <table border="0" cellspacing="0" cellpadding="0" class="em-mob-width-100perc">
                                <tbody>
                                <tr>
                                    <td width="130" valign="top"
                                        class="em-mob-wrap em-mob-wrap-cancel em-mob-width-auto">
                                        <table cellpadding="0" cellspacing="0" border="0" width="100%" em="atom">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <img src="<?= $product['$site_url'] . '/public/img/' . $product['image'] ?>"
                                                         width="130"
                                                         border="0" alt=""
                                                         style="display: block; width: 100%; max-width: 130px;"
                                                         class="em-mob-width-80px">
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td width="20" class="em-mob-wrap em-mob-height-20px em-mob-wrap-cancel"></td>
                                    <td width="230" valign="middle"
                                        class="em-mob-wrap em-mob-wrap-cancel em-mob-width-auto">
                                        <table cellpadding="0" cellspacing="0" border="0" width="100%" em="atom">
                                            <tbody>
                                            <tr>
                                                <td style="padding-right: 0px; padding-bottom: 5px; padding-left: 0px;">
                                                    <div style="font-family: Helvetica, Roboto, Arial, sans-serif; font-size: 18px; line-height: 25px; color: #333333;"
                                                         class="em-mob-font_size-14px em-mob-line_height-16px">
                                                        <strong><?= $product['title'] ?></strong></div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <table cellpadding="0" cellspacing="0" border="0" width="100%" em="atom">
                                            <tbody>
                                            <tr>
                                                <td style="padding-bottom: 10px;">
                                                    <div style="font-family: Helvetica, Roboto, Arial, sans-serif; font-size: 14px; line-height: 18px; color: #5a5a5a;">
                                                        Количество: <?= $product['count'] ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td width="20" class="em-mob-wrap em-mob-wrap-cancel"></td>
                                    <td width="180" class="em-mob-wrap em-mob-wrap-cancel em-mob-width-auto"
                                        valign="middle">
                                        <table cellpadding="0" cellspacing="0" border="0" width="100%" em="atom">
                                            <tbody>
                                            <tr>
                                                <td style="padding-right: 0px; padding-bottom: 10px; padding-left: 0px;">
                                                    <div style="font-family: Helvetica, Roboto, Arial, sans-serif; font-size: 18px; line-height: 25px; color: #cb5652;"
                                                         align="right" class="em-mob-font_size-16px">
                                                        <strong>
                                                            <?= number_format(
                                                                    num: $product['price'],
                                                                    decimals: 2,
                                                                    decimal_separator: ',',
                                                                    thousands_separator: ' '
                                                            ) ?> ₽
                                                        </strong>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                <?php } ?>
                <tr em="block" class="em-structure">
                    <td align="center" style="padding: 20px 40px;"
                        class="em-mob-padding_left-20 em-mob-padding_right-20">
                        <table align="center" border="0" cellspacing="0" cellpadding="0" class="em-mob-width-100perc">
                            <tbody>
                            <tr>
                                <td width="580" valign="top" class="em-mob-wrap em-mob-width-100perc">
                                    <table cellpadding="0" cellspacing="0" border="0" width="100%" em="atom">
                                        <tbody>
                                        <tr>
                                            <td height="0"
                                                style="padding: 20px 0px 0px; border-top: 1px solid #333333;">&nbsp;
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr em="block" class="em-structure">
                    <td align="center" style="padding: 20px 40px;"
                        class="em-mob-padding_left-20 em-mob-padding_right-20">
                        <table border="0" cellspacing="0" cellpadding="0" class="em-mob-width-100perc">
                            <tbody>
                            <tr>
                                <td width="580" valign="top" class="em-mob-wrap em-mob-width-100perc">
                                    <table cellpadding="0" cellspacing="0" border="0" width="100%" em="atom">
                                        <tbody>
                                        <tr>
                                            <td style="padding-right: 0px; padding-bottom: 20px; padding-left: 0px;"
                                                align="center">
                                                <table cellpadding="0" cellspacing="0" border="0" width="205"
                                                       style="width: 205px;">
                                                    <tbody>
                                                    <tr>
                                                        <td align="center" valign="middle" height="41"
                                                            style="background-color: #cb5652; border-radius: 5px; height: 41px;"
                                                            bgcolor="#CB5652">
                                                            <a style="display: block; height: 41px; font-family: -apple-system, 'Segoe UI', 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #ffffff; font-size: 16px; line-height: 41px; text-decoration: none; white-space: nowrap;"
                                                               href="<?= $site_url ?>"
                                                               target="_blank">Перейти на сайт<br></a>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr em="block" class="em-structure">
                    <td align="center" style="padding: 20px 40px 10px;"
                        class="em-mob-padding_left-20 em-mob-padding_right-20">
                        <table border="0" cellspacing="0" cellpadding="0" class="em-mob-width-100perc">
                            <tbody>
                            <tr>
                                <td width="580" class="em-mob-wrap em-mob-wrap-cancel em-mob-width-auto">
                                    <table cellpadding="0" cellspacing="0" border="0" width="100%" em="atom">
                                        <tbody>
                                        <tr>
                                            <td align="center">
                                                <!--                                                <img src="https://emcdn.ru/278815/240527_4213_f9xh8GD.png" width="158"-->
                                                <!--                                                     border="0" alt=""-->
                                                <!--                                                     style="display: block; width: 100%; max-width: 158px;">-->
                                                X_cable
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <table align="center" border="0" cellspacing="0" cellpadding="0" class="em-mob-width-100perc">
                            <tbody>
                            <tr>
                                <td width="580" valign="top" class="em-mob-wrap em-mob-width-100perc">
                                    <table cellpadding="0" cellspacing="0" border="0" width="100%" em="atom">
                                        <tbody>
                                        <tr>
                                            <td style="padding-bottom: 10px; padding-top: 21px;">
                                                <div style="font-family: Helvetica, Roboto, Arial, sans-serif; font-size: 16px; line-height: 21px; color: #595959;"
                                                     align="center">© 2026. Все права защищены.
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <table cellpadding="0" cellspacing="0" border="0" width="100%" em="atom">
                                        <tbody>
                                        <tr>
                                            <td style="padding-bottom: 10px;">
                                                <div style="font-family: Helvetica, Roboto, Arial, sans-serif; font-size: 16px; line-height: 21px; color: #595959;"
                                                     align="center">
                                                    <a href="<?= $site_url ?>" target="_blank"
                                                                       style="color: #595959; text-decoration: underline;">
                                                        <span style="text-decoration: underline;">Отписаться</span>
                                                    </a>&nbsp;
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>