<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <meta name="description" content="<?= APP_DESCRIPTION ?>">
    <link rel="shortcut icon" href="<?= URL_PATH ?>/assets/images/icon/144.png">

    <?php require_once(__DIR__ . '/manifest.partial.php') ?>

    <link rel="stylesheet" href="<?= URL_PATH ?>/assets/build/css/site.css">
    <link rel="stylesheet" href="<?= URL_PATH ?>/assets/build/css/nprogress.css">
    <link rel="stylesheet" href="<?= URL_PATH ?>/assets/libraries/css/fontawesome.css">
    <link rel="stylesheet" href="<?= URL_PATH ?>/assets/libraries/css/datepicker.min.css">

    <script>
        var URL_PATH = '<?= URL_PATH ?>';
    </script>
    <script src="<?= URL_PATH ?>/assets/libraries/js/sedna.js"></script>
    <script src="<?= URL_PATH ?>/assets/build/script/helpers/theme.js"></script>
    <script src="<?= URL_PATH ?>/assets/libraries/js/nprogress.js"></script>
    <script src="<?= URL_PATH ?>/assets/build/script/helpers/conmon.js"></script>
    <script src="<?= URL_PATH ?>/assets/libraries/js/datepicker-full.min.js"></script>

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
</head>

<body itemscope itemtype="http://schema.org/WebPage">
    <div class="SiteLayout" id="SiteLayout">
        <div class="SiteLayout-header ">
            <header class="SiteHeader MainContainer" itemscope itemtype="http://schema.org/WPHeader">
                <div class="SiteHeader-left">
                    <div class="Branding" itemscope itemtype="http://schema.org/Organization">
                        <a class="Branding-link" href="<?= URL_PATH ?>" itemprop="url">
                            <img class="Branding-logo" alt="Logotipo de Sedna" itemprop="logo" src="<?= URL_PATH ?>/assets/images/logo_white.svg">
                            <!-- <span class="Branding-name"><?= APP_NAME ?></span> -->
                        </a>
                    </div>
                </div>
                <div class="SiteHeader-right">
                    <div class="SiteHeader-nav">
                        <div class="icon-menu" id="SiteMenu-toggle"><i class="fas fa-bars"></i></div>
                        <nav class="SiteMenu-wrapper" itemscope itemtype="http://schema.org/SiteNavigationElement" role="navigation" id="SiteMenu-wrapper">
                            <div class="SiteMenu-content">
                                <ul class="SiteMenu SnMenu" id="SiteMenu">
                                    <li itemprop="url"><a href="<?= URL_PATH ?>" target="" itemprop="name" title="Inicio">Inicio</a></li>
                                    <li itemprop="url"><a href="<?= URL_PATH ?>/admin" target="" itemprop="name" title="Company">Corporativo</a></li>
                                    <li itemprop="url"><a href="<?= URL_PATH ?>/page/ayuda" target="" itemprop="name" title="Help">Ayuda</a></li>
                                </ul>
                                <!-- <div class="SnSwitch SnMl-2"><input class="SnSwitch-control" id="themeMode" type="checkbox"><label class="SnSwitch-label" for="themeMode"></label></div> -->
                            </div>
                        </nav>
                    </div>

                    <ul class="UserMenu">
                        <li>
                            <a href="#">
                                <i class="far fa-bell"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <div class="SnAvatar">
                                    <?php if (isset($_SESSION[SESS_USER])) : ?>
                                        <?php if ($_SESSION[SESS_USER]['avatar'] !== '') : ?>
                                            <img class="SnAvatar-img" src="<?= URL_PATH ?><?= $_SESSION[SESS_USER]['avatar'] ?>" alt="avatar">
                                        <?php else : ?>
                                            <div class="SnAvatar-text"><?= substr($_SESSION[SESS_USER]['user_name'], 0, 2); ?></div>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <div class="SnAvatar-text"><i class="fas fa-user"></i></div>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <ul>
                                <?php if (isset($_SESSION[SESS_USER])) : ?>
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
                                    <li><a href="<?= URL_PATH ?>/user/update"><i class="fas fa-user SnMr-2"></i>Perfil</a></li>
                                    <?php if (($_SESSION[SESS_USER]['company_id'] ?? 0) > 0) : ?>
                                        <li><a href="<?= URL_PATH ?>/admin"><i class="fas fa-user-cog SnMr-2"></i>Corporativo</a></li>
                                    <?php endif; ?>
                                    <li class="SnMb-2"><a href="<?= URL_PATH ?>/user/logout"><i class="fas fa-sign-out-alt SnMr-2"></i>Cerrar sesión</a></li>
                                <?php else : ?>
                                    <li class="SnMt-2"><a href="<?= URL_PATH ?>/user/login"><i class="fas fa-user SnMr-2"></i>Iniciar sesión</a></li>
                                    <li class="SnMb-2"><a href="<?= URL_PATH ?>/user/register"><i class="fas fa-pen SnMr-2"></i>Registrarse</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    </ul>
                </div>
            </header>
        </div>
        <div class="SiteLayout-main">
            <?php echo $content ?>
        </div>
        <div class="SiteLayout-footer">
            <div class="MainContainer">
                <a href="<?= APP_AUTHOR_WEB ?>" class="copyright" target="_blank">Copyright © <?= date('Y') ?> <?= APP_AUTHOR ?></a>
            </div>
        </div>
    </div>
    <script src="<?= URL_PATH ?>/assets/build/script/site/siteLayout.js"></script>
</body>

</html>