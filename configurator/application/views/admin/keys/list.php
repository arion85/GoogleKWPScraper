<script>
    function confirmDelete() {
        return confirm("ВНИМАНИЕ! Ключ для данной страны будет удален безвозвратно. Вы подтверждаете удаление?");
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
            <?php echo ucfirst('Менеджер ключевых слов'); ?>
        </h2>
    </div>

    <div class="row">
        <div class="span12 columns">
            <div class="well">
                <div class="span9 columns">
                    <?php

                    $attributes = array('class' => 'form-inline reset-margin', 'id' => 'myform');

                    //save the columns names in a array that we will use as filter
                    $options_countries = array(0 => '-Выберите страну-');
                    foreach ($countries as $array) {
                        $options_countries[$array['ID']] = $array['full_name'];
                    }

                    echo form_open('admin/keys', $attributes);

                    echo form_label('Поиск ключа:', 'search_string');
                    echo form_input('search_string', $search_string_selected);

                    echo form_label('Страна:', 'country');
                    echo form_dropdown('country', $options_countries, $country_selected, 'class="span2"');

                    $data_submit = array('name' => 'mysubmit', 'class' => 'btn btn-primary', 'value' => 'Go');

                    $options_order_type = array('Asc' => 'Asc', 'Desc' => 'Desc');
                    echo form_dropdown('order_type', $options_order_type, $order_type_selected, 'class="span1"');

                    echo form_submit($data_submit);

                    echo form_close();
                    ?>
                </div>
                <div class="span2 columns">
                    <div class="row"><b>Всего:</b>&nbsp;<?php echo $count_keys; ?></div>
                    <div class="row"><b>Обработано:</b>&nbsp;<?php echo $count_coml_keys; ?></div>
                </div>
            </div>
            <?php if (!$keys) { ?>
                <strong style="color:red;">Необходимо выбрать страну!</strong>
            <?php } else { ?>
                <table class="table table-striped table-bordered table-condensed">
                    <thead>
                    <tr>
                        <th class="header">ID</th>
                        <th class="yellow header headerSortDown">Keyword</th>
                        <th class="header">Currency</th>
                        <th class="header">Avg. Monthly Searches (exact match only)</th>
                        <th class="header">Competition</th>
                        <th class="header">Suggested bid</th>
                        <th class="header">Статус</th>
                        <th class="header">Keywords</th>
                        <th class="header">Время</th>
                        <th class="header">Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $status_arr = array(0 => 'Обработан', 1 => 'В ожидании', 2 => 'В процессе', 4 => 'Нет Keywords');
                    foreach ($keys as $row) {
                        echo '<tr>';
                        echo '<td>' . $row['id'] . '</td>';
                        echo '<td>' . $row['key'] . '</td>';
                        echo '<td>' . $row['currency'] . '</td>';
                        echo '<td>' . $row['AMS'] . '</td>';
                        echo '<td>' . $row['competition'] . '</td>';
                        echo '<td>' . $row['suggested_bid'] . '</td>';
                        echo '<td>' . $status_arr[$row['status']] . '</td>';
                        echo '<td>' . $row['total_count'] . '</td>';
                        echo '<td>' . sprintf('%.4F сек.', $row['time']) . '</td>';
                        echo '<td class="crud-actions">
                  <a href="' . site_url("admin") . '/keys/keywords/' . $row['id'] . '" class="btn btn-info">keywords</a>
                  <a href="' . site_url("admin") . '/keys/delete/' . $row['id'] . '" class="btn btn-danger" onclick="return confirmDelete();">delete</a>
                </td>';
                        echo '</tr>';
                    }
                    ?>
                    </tbody>
                </table>
            <?php } ?>
            <?php echo '<div class="pagination">' . $this->pagination->create_links() . '</div>'; ?>

        </div>
    </div>