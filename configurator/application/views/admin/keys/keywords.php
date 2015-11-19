<script>
    function confirmDelete() {
        return confirm("ВНИМАНИЕ! Данные будут удалены безвозвратно. Вы подтверждаете удаление?");
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
        <li>
            <a href="<?php echo site_url("admin" . '/' . $this->uri->segment(2)); ?>">
                <?php echo ucfirst($this->uri->segment(2)); ?>
            </a>
            <span class="divider">/</span>
        </li>
        <li class="active">
            <?php echo ucfirst($this->uri->segment(3)); ?>
        </li>
    </ul>

    <div class="page-header users-header">
        <h2>
            <?php echo ucfirst('Ключевые слова для ключа: ' . $key); ?>
        </h2>
    </div>
    <?php if ($total_rows > 0){ ?>
        <div class="row">
            <div class="span12 columns">
                <div class="well">

                    <?php

                    $attributes = array('class' => 'form-inline reset-margin', 'id' => 'myform');

                    //save the columns names in a array that we will use as filter
                    $options_keywords = array('keywords' => 'Keywords', 'AMS' => 'Avg. Monthly Searches',
                        'competition' => 'Competition', 'suggested_bid' => 'Suggested bid');

                    echo form_open('admin/keys/keywords/' . $key_id, $attributes);

                    //echo form_label('Search:', 'search_string');
                    //echo form_input('search_string', $search_string_selected);

                    echo form_label('Order by:', 'order');
                    echo form_dropdown('order', $options_keywords, $order, 'class="span2"');

                    $data_submit = array('name' => 'mysubmit', 'class' => 'btn btn-primary', 'value' => 'Go');

                    $options_order_type = array('Asc' => 'Asc', 'Desc' => 'Desc');
                    echo form_dropdown('order_type_keywords', $options_order_type, $order_type_selected, 'class="span1"');

                    echo form_submit($data_submit);

                    echo form_close();
                    ?>

                </div>

                <table class="table table-striped table-bordered table-condensed">
                    <thead>
                    <tr>
                        <th class="header">ID</th>
                        <th class="yellow header headerSortDown">Keyword</th>
                        <th class="header">Currency</th>
                        <th class="header">Avg. Monthly Searches (exact match only)</th>
                        <th class="header">Competition</th>
                        <th class="header">Suggested bid</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($keywords as $row) {
                        echo '<tr>';
                        echo '<td>' . $row['id'] . '</td>';
                        echo '<td>' . $row['keywords'] . '</td>';
                        echo '<td>' . $row['currency'] . '</td>';
                        echo '<td>' . $row['AMS'] . '</td>';
                        echo '<td>' . $row['competition'] . '</td>';
                        echo '<td>' . $row['suggested_bid'] . '</td>';
                        echo '</tr>';
                    }
                    ?>
                    </tbody>
                </table>

                <?php echo '<div class="pagination">' . $this->pagination->create_links() . '</div>'; ?>

        </div>
        </div>
    <?php }else{ ?>
    <div class="row">
        <div class="span12 columns">
            Записей не найдено!
        </div>
    </div>
<?php } ?>