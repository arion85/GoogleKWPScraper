<?php
/**
 * Created by PhpStorm.
 * Author: Arioshkin Evgeniy
 * Date: 09.10.16
 * Time: 15:51
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Crt_first_tbl extends CI_Migration
{
    public function up()
    {
        // Структура таблицы `ci_cookies`
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `ci_cookies` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `cookie_id` varchar(255) DEFAULT NULL,
              `netid` varchar(255) DEFAULT NULL,
              `ip_address` varchar(255) DEFAULT NULL,
              `user_agent` varchar(255) DEFAULT NULL,
              `orig_page_requested` varchar(120) DEFAULT NULL,
              `php_session_id` varchar(40) DEFAULT NULL,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
        ");

        // Структура таблицы `membership`
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `membership` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `first_name` varchar(255) DEFAULT NULL,
              `last_name` varchar(255) DEFAULT NULL,
              `email_addres` varchar(255) DEFAULT NULL,
              `user_name` varchar(255) DEFAULT NULL,
              `pass_word` varchar(32) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

    }

    public function down()
    {
        // Для быстрого удаления
        // Сначала очищаем таблицу
        $this->db->query("TRUNCATE TABLE `ci_cookies`");
        // Потом удаляем
        $this->db->query("DROP TABLE IF EXISTS `ci_cookies`");

        // Для быстрого удаления
        // Сначала очищаем таблицу
        $this->db->query("TRUNCATE TABLE `ci_sessions`");
        // Потом удаляем
        $this->db->query("DROP TABLE IF EXISTS `ci_sessions`");

        // Для быстрого удаления
        // Сначала очищаем таблицу
        $this->db->query("TRUNCATE TABLE `membership`");
        // Потом удаляем
        $this->db->query("DROP TABLE IF EXISTS `membership`");
    }
}