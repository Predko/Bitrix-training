<!DOCTYPE html>
<html lang="ru">

<?php
use Bitrix\Main\Page\Asset;
?>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?=LANG_CHARSET?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title><?$APPLICATION->ShowTitle();?></title>

  <!-- Font Awesome Icons -->
  <!-- <link href="<?=SITE_TEMPLATE_PATH?>/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css"> -->
  <?Asset::getInstance()->AddCss(SITE_TEMPLATE_PATH.'/vendor/fontawesome-free/css/all.min.css');?>
  
  <!-- Google Fonts -->
  <!-- <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans:400,700&selection.subset=cyrillic" rel="stylesheet">
  <link href='https://fonts.googleapis.com/css?family=Merriweather:300,300i,400,400i,700,700i&display=swap&subset=cyrillic' rel='stylesheet' type='text/css'> -->
  <?Asset::getInstance()->AddCss(SITE_TEMPLATE_PATH."/https://fonts.googleapis.com/css?family=Merriweather+Sans:400,700&selection.subset=cyrillic");?>
  <?Asset::getInstance()->AddCss(SITE_TEMPLATE_PATH."/https://fonts.googleapis.com/css?family=Merriweather:300,300i,400,400i,700,700i&display=swap&subset=cyrillic");?>

  <!-- Plugin CSS -->
  <!-- <link href="<?=SITE_TEMPLATE_PATH?>/vendor/magnific-popup/magnific-popup.css" rel="stylesheet"> -->
  <?Asset::getInstance()->AddCss(SITE_TEMPLATE_PATH."/vendor/magnific-popup/magnific-popup.css");?>

  <!-- Theme CSS - Includes Bootstrap -->
  <!-- <link href="<?=SITE_TEMPLATE_PATH?>/css/creative.min.css" rel="stylesheet"> -->
  <?Asset::getInstance()->AddCss(SITE_TEMPLATE_PATH."/css/creative.min.css");?>

  <?$APPLICATION->ShowHead();?>

</head>

<body id="page-top">

<div id="panel">
    <?$APPLICATION->ShowPanel(); ?>
</div>

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
    <div class="container">
      <a class="navbar-brand js-scroll-trigger" href="<?=SITE_DIR?>/#page-top">РЫБЫ.НЕТ</a>
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto my-2 my-lg-0">
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="<?=SITE_DIR?>/#home">Главная</a>
          </li>
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="<?=SITE_DIR?>/#news">Новости</a>
          </li>
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="<?=SITE_DIR?>/#portfolio">Галерея</a>
          </li>
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="<?=SITE_DIR?>/#contact">Контакты</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Masthead -->
  <section class="bg-dark">
			<div style="padding-top:5em"></div>
  </section>