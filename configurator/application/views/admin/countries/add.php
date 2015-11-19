<div class="container top">

    <ul class="breadcrumb">
        <li>
            <a href="<?php echo site_url("admin"); ?>">
                <?php echo ucfirst($this->uri->segment(1)); ?>
            </a>
            <span class="divider">/</span>
        </li>
        <li>
            <a href="<?php echo site_url("admin") . '/' . $this->uri->segment(2); ?>">
                <?php echo ucfirst($this->uri->segment(2)); ?>
            </a>
            <span class="divider">/</span>
        </li>
        <li class="active">
            <a href="#">Новая страна</a>
        </li>
    </ul>

    <div class="page-header">
        <h2>
            Добавить <?php echo ucfirst('новую страну'); ?>
        </h2>
    </div>

    <?php
    //flash messages
    if (isset($flash_message)) {
        if ($flash_message == TRUE) {
            echo '<div class="alert alert-success">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>OK!</strong> Новая страна успешно добавлена.';
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

    //form validation
    echo validation_errors();

    echo form_open('admin/countries/add', $attributes);
    ?>
    <fieldset>
        <div class="control-group">
            <label for="inputError" class="control-label">Полное название</label>
            <div class="controls">
                <input type="text" id="" name="full_name" value="<?php echo set_value('full_name'); ?>">
                <!--<span class="help-inline">Woohoo!</span>-->
            </div>
        </div>
        <div class="control-group">
            <label for="inputError" class="control-label">Внутренний идентификатор</label>

            <div class="controls">
                <input type="text" id="" name="short_name" value="<?php echo set_value('short_name'); ?>">
                <!--<span class="help-inline">Woohoo!</span>-->
            </div>
            </div>
        <div class="control-group">
            <label for="inputError" class="control-label">Начальные слова</label>

            <div class="controls">
                <textarea id="" rows="10" name="start_words"><?php echo set_value('start_words'); ?></textarea>
                <!--<span class="help-inline">Woohoo!</span>-->
            </div>
            </div>
        <div class="control-group">
            <label for="inputError" class="control-label">Стоп-слова</label>

            <div class="controls">
                <textarea id="" rows="10" name="stop_words"><?php echo set_value('stop_words'); ?></textarea>
                <!--<span class="help-inline">Woohoo!</span>-->
            </div>
            </div>
        <div class="control-group">
            <label for="inputError" class="control-label">ID страны для автоактиватора</label>

            <div class="controls">
                <input type="text" id="" name="country_id" value="<?php echo set_value('country_id'); ?>">
                <!--<span class="help-inline">Woohoo!</span>-->
            </div>
            </div>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Сохранить</button>
            <button class="btn" type="reset">Отмена</button>
        </div>
    </fieldset>

    <?php echo form_close(); ?>

</div>
     