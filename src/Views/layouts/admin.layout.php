<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <meta name="description" content="<?= APP_DESCRIPTION ?>">
    <link rel="shortcut icon" href="<?= URL_PATH ?>/assets/images/icon/144.png">

    <?php require_once(__DIR__ . '/manifest.partial.php') ?>

    <link rel="stylesheet" href="<?= URL_PATH ?>/assets/build/css/admin.css">
    <link rel="stylesheet" href="<?= URL_PATH ?>/assets/build/css/nprogress.css">
    <link rel="stylesheet" href="<?= URL_PATH ?>/assets/libraries/css/fontawesome.css">
    <link rel="stylesheet" href="<?= URL_PATH ?>/assets/libraries/css/slimselect.css">
    <link rel="stylesheet" href="<?= URL_PATH ?>/assets/libraries/css/datepicker.min.css">

    <script>
        var URL_PATH = '<?= URL_PATH ?>';
    </script>
    <script src="<?= URL_PATH ?>/assets/libraries/js/sedna.js"></script>
    <script src="<?= URL_PATH ?>/assets/build/script/helpers/theme.js"></script>
    <script src="<?= URL_PATH ?>/assets/libraries/js/pristine.min.js"></script>
    <script src="<?= URL_PATH ?>/assets/libraries/js/nprogress.js"></script>
    <script src="<?= URL_PATH ?>/assets/libraries/js/slimselect.min.js"></script>
    <script src="<?= URL_PATH ?>/assets/build/script/helpers/conmon.js"></script>
    <script src="<?= URL_PATH ?>/assets/libraries/js/datepicker-full.min.js"></script>

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
</head>

<body>
    <?php
    if (isset($_SESSION[SESS_DATE_OF_DUE])) {
        $dateOfDue = $_SESSION[SESS_DATE_OF_DUE];
        $dateOfDueMin = date("Y-m-d", strtotime($dateOfDue . "- " . $_SESSION[SESS_DATE_OF_DUE_DAY] . " days"));
        $currentDate = new DateTime(date("Y-m-d"));
        $dateContract = new DateTime($dateOfDueMin);
        if ($currentDate > $dateContract) {
            echo '<div style="padding:10px !important; color: var(--snWarningInverse); background-color: var(--snWarning)">Tiene un recibo pendiente por pagar, hasta el ' . $dateOfDue . '</div>';
        }
    }
    ?>
    <div class="AdminLayout" id="AdminLayout">
        <div class="AdminLayout-header">
            <header class="Header">
                <div class="Header-left">
                    <div id="AsideMenu-toggle"><i class="fas fa-bars"></i></div>
                </div>
                <div class="Header-right">
                    <ul class="UserMenu">
                        <li>
                            <span style="color: var(--snSuccess); padding-right: 8px; padding-left: 8px;">Producción</span>
                        </li>
                        <li>
                            <a href="#">
                                <i class="far fa-bell"></i>
                            </a>
                            <ul>
                                <li>Notificaciones</li>
                                <li class="divider"></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">
                                <div class="SnAvatar">
                                    <?php if ($_SESSION[SESS_USER]['avatar'] !== '') : ?>
                                        <img class="SnAvatar-img" src="<?= URL_PATH ?><?= $_SESSION[SESS_USER]['avatar'] ?>" alt="avatar">
                                    <?php else : ?>
                                        <div class="SnAvatar-text"><?= substr($_SESSION[SESS_USER]['user_name'], 0, 2); ?></div>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <ul>
                                <li class="UserMenu-profile SnMt-2 SnMb-2">
                                    <a href="<?= URL_PATH ?>/user/update">
                                        <div class="SnAvatar">
                                            <?php if ($_SESSION[SESS_USER]['avatar'] !== '') : ?>
                                                <img class="SnAvatar-img" src="<?= URL_PATH ?><?= $_SESSION[SESS_USER]['avatar'] ?>" alt="avatar">
                                            <?php else : ?>
                                                <div class="SnAvatar-text"><?= substr($_SESSION[SESS_USER]['user_name'], 0, 2); ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="UserMenu-title"><strong id="userTitleInfo"><?= $_SESSION[SESS_USER]['email'] ?></strong></div>
                                            <div class="UserMenu-description" id="userDescriptionInfo"><?= $_SESSION[SESS_USER]['user_name'] ?></div>
                                        </div>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li class="SnMt-2"><a href="<?= URL_PATH ?>/user/update"><i class="fas fa-user SnMr-2"></i>Perfil</a></li>
                                <li class="SnMb-2"><a href="<?= URL_PATH ?>/user/logout"><i class="fas fa-sign-out-alt SnMr-2"></i>Cerrar sesión</a></li>
                                <?php if ($_SESSION[SESS_USER]['user_role_id'] == 1) : ?>
                                    <li class="divider"></li>
                                    <li class="SnMt-2 SnMb-2"><a href="<?= URL_PATH ?>/admin/config/app"><i class="fas fa-cog SnMr-2"></i>Configurar</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    </ul>
                </div>
            </header>
        </div>
        <div class="AdminLayout-aside">
            <div id="AsideMenu-wrapper" class="AsideMenu-wrapper">
                <div class="AsideMenu-container">
                    <div class="AsideHeader">
                        <div class="Branding">
                            <a href="<?= URL_PATH ?>/admin" class="Branding-link">
                                <img src="<?= URL_PATH ?>/assets/images/icon/144.png" alt="Logo" class="Branding-img">
                                <span class="Branding-name"><?= APP_NAME ?></span>
                            </a>
                        </div>
                    </div>
                    <ul class="AsideMenu" id="AsideMenu">
                        <?php if (menuIsAuthorized('home')) : ?>
                            <li>
                                <a href="<?= URL_PATH ?>/admin"><i class="fas fa-tachometer-alt AsideMenu-icon"></i><span>Inicio</span> </a>
                            </li>
                        <?php endif; ?>
                        <?php if (menuIsAuthorized('order')) : ?>
                            <li>
                                <a href="<?= URL_PATH ?>/admin/order"><i class="fas fa-directions AsideMenu-icon"></i><span>Pedido</span> </a>
                            </li>
                        <?php endif; ?>
                        <?php if (menuIsAuthorized('delivery')) : ?>
                            <li>
                                <a href="<?= URL_PATH ?>/admin/delivery"><i class="fas fa-shopping-basket AsideMenu-icon"></i><span>Entrega</span> </a>
                            </li>
                        <?php endif; ?>
                        <?php if (menuIsAuthorized('exhibitor')) : ?>
                            <li>
                                <a href="<?= URL_PATH ?>/admin/exhibitor"><i class="fas fa-charging-station AsideMenu-icon"></i><span>Hexibidoras</span> </a>
                            </li>
                        <?php endif; ?>
                        <?php if (menuIsAuthorized(['customer', 'product'])) : ?>
                            <li>
                                <a href="#"><i class="fas fa-toolbox AsideMenu-icon"></i><span>Mantenimiento</span></a>
                                <ul>
                                    <?php if (menuIsAuthorized('customer')) : ?>
                                        <li>
                                            <a href="<?= URL_PATH ?>/admin/customer"><i class="far fa-address-book AsideMenu-icon"></i><span>Clientes</span> </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if (menuIsAuthorized('product')) : ?>
                                        <li>
                                            <a href="<?= URL_PATH ?>/admin/product"><i class="fas fa-boxes AsideMenu-icon"></i><span>Productos</span> </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if (menuIsAuthorized(['company', 'user', 'rol'])) : ?>
                            <li>
                                <a href="#"><i class="fas fa-cog AsideMenu-icon"></i><span>Configuración</span></a>
                                <ul>
                                    <?php if (menuIsAuthorized('company')) : ?>
                                        <li>
                                            <a href="<?= URL_PATH ?>/admin/company"><i class="far fa-building AsideMenu-icon"></i><span>Empresa</span></a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if (menuIsAuthorized('user')) : ?>
                                        <li>
                                            <a href="<?= URL_PATH ?>/admin/user"><i class="fas fa-user AsideMenu-icon"></i><span>Usuarios</span></a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if (menuIsAuthorized('rol')) : ?>
                                        <li>
                                            <a href="<?= URL_PATH ?>/admin/appAuthorization"><i class="fas fa-user-tag AsideMenu-icon"></i><span>Roles</span></a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if (menuIsAuthorized(['report'])) : ?>
                            <li>
                                <a href="#"><i class="fas fa-chart-pie AsideMenu-icon"></i><span>Reportes</span></a>
                                <ul>
                                    <?php if (menuIsAuthorized('report')) : ?>
                                        <li>
                                            <a href="<?= URL_PATH ?>/admin/exhibitor/monitoring"><i class="fas fa-desktop AsideMenu-icon"></i><span>Monitoreo</span></a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if (menuIsAuthorized('report')) : ?>
                                        <li>
                                            <a href="<?= URL_PATH ?>/admin/income"><i class="fas fa-hand-holding-usd AsideMenu-icon"></i><span>Reporte ingreso</span></a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if (menuIsAuthorized('report')) : ?>
                                        <li>
                                            <a href="<?= URL_PATH ?>/admin/report/orderReport"><i class="fas fa-directions AsideMenu-icon"></i><span>Reporte orden</span></a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if (menuIsAuthorized('report')) : ?>
                                        <li>
                                            <a href="<?= URL_PATH ?>/admin/report/deliveryReport"><i class="fas fa-shopping-basket AsideMenu-icon"></i><span>Reporte Entrega</span></a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <li>
                            <a href="<?= URL_PATH ?>/admin/home/help"><i class="fas fa-hands-helping AsideMenu-icon"></i><span>Ayuda</span> </a>
                        </li>
                    </ul>
                    <div class="AsideFooter">
                        <input class="SnSwitch" type="checkbox" id="themeMode" title="Cambiar tema">
                    </div>
                </div>
            </div>
        </div>
        <div class="AdminLayout-main">
            <?php echo $content ?>
        </div>
    </div>
    <script src="<?= URL_PATH ?>/assets/build/script/admin/adminLayout.js"></script>
</body>

</html>