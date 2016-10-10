<script>
    function confirmDelete() {
        return confirm("ВНИМАНИЕ! Будут удалены ВСЕ таблицы и ключи для данной страны. Вы подтверждаете удаление?");
    }
</script>
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
            <?php echo ucfirst('Менеджер стран'); ?>
            <a href="<?php echo site_url("admin") . '/' . $this->uri->segment(2); ?>/add" class="btn btn-success">Добавить
                новый</a>
        </h2>
    </div>

    <div class="row">
        <div class="span12 columns">
            <div class="well">

                <?php

                $attributes = array('class' => 'form-inline reset-margin', 'id' => 'myform');

                //save the columns names in a array that we will use as filter
                $options_countries = array();
                foreach ($countries as $array) {
                    foreach ($array as $key => $value) {
                        $options_countries[$key] = $key;
                    }
                    break;
                }

                echo form_open('admin/countries', $attributes);

                echo form_label('Search:', 'search_string');
                echo form_input('search_string', $search_string_selected);

                echo form_label('Order by:', 'order');
                echo form_dropdown('order', $options_countries, $order, 'class="span2"');

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
                    <th class="yellow header headerSortDown">Название</th>
                    <th class="header">Внутренний идентификатор</th>
                    <th class="header">ID для автоактиватора аккаунта</th>
                    <th class="header">Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($countries as $row) {
                    echo '<tr>';
                    echo '<td>' . $row['id'] . '</td>';
                    echo '<td>' . $row['full_name'] . '</td>';
                    echo '<td>' . $row['short_name'] . '</td>';
                    echo '<td>' . $row['country_id'] . '</td>';
                    echo '<td class="crud-actions">
                  <a href="' . site_url("admin") . '/countries/update/' . $row['id'] . '" class="btn btn-info">view & edit</a>
                  <a href="' . site_url("admin") . '/countries/delete/' . $row['id'] . '" class="btn btn-danger" onclick="return confirmDelete();">delete</a>
                </td>';
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>

            <?php echo '<div class="pagination">' . $this->pagination->create_links() . '</div>'; ?>

        </div>
    </div>