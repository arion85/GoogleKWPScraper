<div class="container top" xmlns="http://www.w3.org/1999/html">

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
            <?php echo ucfirst('Настройки'); ?>
        </h2>
    </div>

    <div class="row">
        <div class="span12 columns">

            <?php
            //flash messages
            if (isset($flash_message)) {
                if ($flash_message == TRUE) {
                    echo '<div class="alert alert-success">';
                    echo '<a class="close" data-dismiss="alert">×</a>';
                    echo '<strong>OK!</strong> Настройки успешно сохранены.';
                    echo '</div>';
                } else {
                    echo '<div class="alert alert-error">';
                    echo '<a class="close" data-dismiss="alert">×</a>';
                    echo '<strong>Ошибка!</strong> Исправьте ошибку и отправьте повторно.';
                    echo '</div>';
            }
            }
            ?>

            <?php
            //form data
            $attributes = array('class' => 'form-horizontal', 'id' => '');

            $options_countries = array('' => "Выберите страну");
            foreach ($countries as $row) {
                $options_countries[$row['ID']] = $row['full_name'];
            }
            //form validation
            echo validation_errors();

            echo form_open('admin/main/add', $attributes);
            ?>
            <fieldset>
                <div class="control-group">
                    <label for="status" class="control-label input-group">Состояние</label>

                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default">
                            <input type="radio" id="status" value="1"
                                   name="status" <?php echo ($params->status == 't') ? 'checked="checked"' : '' ?>>Включен
                        </label>
                        <label class="btn btn-default">
                            <input type="radio" id="status" value="0"
                                   name="status" <?php echo ($params->status == 'f') ? 'checked="checked"' : '' ?>>Отключен
                        </label>
                </div>
                </div>
                <div class="control-group">
                    <label for="inputError" class="control-label">Количество потоков</label>

                    <div class="controls">
                        <input type="text" id="thr_cnt" name="thr_cnt" value="<?php echo $params->thr_cnt; ?>"
                               class="input-mini">
                        <!--<span class="help-inline">Woohoo!</span>-->
                </div>
                </div>
                <div class="control-group">
                    <label for="inputError" class="control-label">Страна</label>

                    <div class="controls">
                        <?php echo form_dropdown('country_id', $options_countries, $params->country_id, 'class="span3"'); ?>
                        <!--<span class="help-inline">Woohoo!</span>-->
                    </div>
                </div>
                <div class="form-actions">
                    <button class="btn btn-primary" type="submit">Сохранить</button>
                </div>
            </fieldset>

            <?php echo form_close(); ?>
    </div>
    </div>