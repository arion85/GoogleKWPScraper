<!DOCTYPE html>
<html lang="en-US">
<head>
    <title>Конфигуратор парсера</title>
    <meta charset="utf-8">
    <link href="<?php echo base_url(); ?>assets/css/admin/global.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="brand">Parser</a>
            <ul class="nav">
                <li <?php if ($this->uri->segment(2) == 'main') {
                    echo 'class="active"';
                } ?>>
                    <a href="<?php echo base_url(); ?>admin/main">Настройки</a>
                </li>
                <li <?php if ($this->uri->segment(2) == 'accounts') {
                    echo 'class="active"';
                } ?>>
                    <a href="<?php echo base_url(); ?>admin/accounts">Аккаунты</a>
                </li>
                <li <?php if ($this->uri->segment(2) == 'proxys') {
                    echo 'class="active"';
                } ?>>
                    <a href="<?php echo base_url(); ?>admin/proxys">Прокси-серверы</a>
                </li>
                <li <?php if ($this->uri->segment(2) == 'countries') {
                    echo 'class="active"';
                } ?>>
                    <a href="<?php echo base_url(); ?>admin/countries">Страны</a>
                </li>
                <li <?php if ($this->uri->segment(2) == 'keys') {
                    echo 'class="active"';
                } ?>>
                    <a href="<?php echo base_url(); ?>admin/keys">Ключи</a>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">System <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?php echo base_url(); ?>admin/logout">Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
