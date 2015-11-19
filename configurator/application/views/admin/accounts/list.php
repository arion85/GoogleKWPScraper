<div class="container top">

    <ul class="breadcrumb">
        <li>
            <a href="<?php echo site_url("admin"); ?>">
                <?php echo ucfirst($this->uri->segment(1)); ?>
            </a>
            <span class="divider">/</span>
        </li>
        <li class="active">
            <?php echo ucfirst($this->uri->segment(2)); ?>
        </li>
    </ul>

    <div class="page-header users-header">
        <h2>
            <?php echo ucfirst('Менеджер аккаунтов Гугл'); ?>
            <?php if (count($accounts) != 0) { ?>
                <a href="<?php echo site_url("admin") . '/' . $this->uri->segment(2); ?>/reload"
                   class="btn btn-warning">Обновить статусы</a>
            <?php } ?>
            <a href="<?php echo site_url("admin") . '/' . $this->uri->segment(2); ?>/add" class="btn btn-success">Добавить
                новый</a>
        </h2>
    </div>

    <div class="row">
        <div class="span12 columns">
            <div class="well">
                    <?php
                    $attributes = array('class' => '', 'id' => 'mycountries');

                    //save the columns names in a array that we will use as filter
                    $options_countries = array('' => "Выберите страну");
                    foreach ($countries as $row) {
                        $options_countries[$row['country_id']] = $row['full_name'];
                    }

                    echo form_open_multipart('admin/accounts', $attributes);
                    ?>
                <fieldset>
                    <legend>Пакетная загрузка Excel</legend>
                    <?php
                    //flash messages
                    if (isset($flash_message)) {
                        if ($flash_message['status'] == 'ok') {
                            echo '<div class="alert alert-success">';
                            echo '<a class="close" data-dismiss="alert">×</a>';
                            echo "{$flash_message['msg']}";
                            echo '</div>';
                        } elseif ($flash_message['status'] == 'error') {
                            echo '<div class="alert alert-error">';
                            echo '<a class="close" data-dismiss="alert">×</a>';
                            echo "{$flash_message['msg']}";
                            echo '</div>';
                        }
                    }
                    ?>
                    <div class="controls">
                        <input type="file" name="uplFile" class="input-xxlarge">
                    </div>
                    <label>Установки по умолчанию для пакета аккаунтов</label>

                    <div class="controls controls-row">
                        <?php echo form_dropdown('country_id', $options_countries, set_value('country_id'), 'class="span3"'); ?>
                        <input type="submit" name="btnUplFile" class="btn btn-primary"
                               onclick="this.setAttribute('value','Обработка...')">
                    </div>

                </fieldset>
                <?php

                echo form_close();
                ?>
            </div>
            </div>
        </div>


    <div class="row">
        <div class="span12 columns">
            <div class="well">

                <?php

                $attributes = array('class' => 'form-inline reset-margin', 'id' => 'myform');

                //save the columns names in a array that we will use as filter
                $options_accounts = array();
                foreach ($accounts as $array) {
                    foreach ($array as $key => $value) {
                        $options_accounts[$key] = $key;
                    }
                    break;
                }

                echo form_open('admin/accounts', $attributes);

                echo form_label('Search:', 'search_string');
                echo form_input('search_string', $search_string_selected, 'style="width: 170px;
height: 26px;"');

                echo form_label('Order by:', 'order');
                echo form_dropdown('order', $options_accounts, $order, 'class="span2"');

                $data_submit = array('name' => 'mysubmit', 'class' => 'btn btn-primary', 'value' => 'Go');

                $options_order_type = array('Asc' => 'Asc', 'Desc' => 'Desc');
                echo form_dropdown('order_type', $options_order_type, $order_type_selected, 'class="span1"');

                echo form_submit($data_submit);

                echo form_close();
                ?>

            </div>

            <table class="table table-striped table-bordered table-condensed">
                <thead>
                <tr>
                    <th class="header">ID</th>
                    <th class="yellow header headerSortDown">Логин</th>
                    <th class="green header">Пароль</th>
                    <th class="red header">Телефон</th>
                    <th class="red header">Запасной e-mail</th>
                    <th class="red header">% отказа</th>
                    <th class="red header">Статус</th>
                    <th class="header">Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $status_arr = array(0 => 'Работает', 1 => 'Отключен');
                foreach ($accounts as $row) {
                    $fails = ($row['cnt_work'] != 0) ? round((100 * $row['cnt_fail'] / $row['cnt_work']), 2) : '-';
                    echo '<tr>';
                    echo '<td>' . $row['id'] . '</td>';
                    echo '<td>' . $row['gm_login'] . '</td>';
                    echo '<td>' . $row['gm_pass'] . '</td>';
                    echo '<td>' . $row['gm_tel'] . '</td>';
                    echo '<td>' . $row['gm_recovery_email'] . '</td>';
                    echo '<td>' . $fails . '</td>';
                    echo '<td>' . $status_arr[$row['status']] . '</td>';
                    echo '<td class="crud-actions">
                  <a href="' . site_url("admin") . '/accounts/update/' . $row['id'] . '" class="btn btn-info">Редактировать</a>
                  <a href="' . site_url("admin") . '/accounts/delete/' . $row['id'] . '" class="btn btn-danger">Удалить</a>
                </td>';
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>

            <?php echo '<div class="pagination">' . $this->pagination->create_links() . '</div>'; ?>

        </div>
    </div>