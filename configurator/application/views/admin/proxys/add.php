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
            <a href="#">Новый прокси-сервер</a>
        </li>
    </ul>

    <div class="page-header">
        <h2>
            Добавить <?php echo ucfirst('новый прокси-сервер'); ?>
        </h2>
    </div>

    <?php
    //flash messages
    if (isset($flash_message)) {
        if ($flash_message == TRUE) {
            echo '<div class="alert alert-success">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>OK!</strong> Новsq прокси-сервер успешно добавлен.';
            echo '</div>';
        } else {
            echo '<div class="alert alert-error">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>Ошибка!</strong> Исправьте ошибки и отправьте еще раз.';
            echo '</div>';
        }
    }
    ?>

    <?php
    //form data
    $attributes = array('class' => 'form-horizontal', 'id' => '');

    //form validation
    echo validation_errors();

    echo form_open('admin/proxys/add', $attributes);
    ?>
    <fieldset>
        <div class="control-group">
            <label for="inputError" class="control-label">IP адрес</label>

            <div class="controls">
                <input type="text" id="" name="pr_ip" value="<?php echo set_value('pr_ip'); ?>">
                <!--<span class="help-inline">Woohoo!</span>-->
            </div>
        </div>
        <div class="control-group">
            <label for="inputError" class="control-label">Порт</label>

            <div class="controls">
                <input type="text" id="" name="pr_port" value="<?php echo set_value('pr_port'); ?>">
                <!--<span class="help-inline">Woohoo!</span>-->
            </div>
        </div>
        <div class="control-group">
            <label for="inputError" class="control-label">Логин</label>

            <div class="controls">
                <input type="text" id="" name="pr_login" value="<?php echo set_value('pr_login'); ?>">
                <!--<span class="help-inline">Woohoo!</span>-->
            </div>
        </div>
        <div class="control-group">
            <label for="inputError" class="control-label">Пароль</label>

            <div class="controls">
                <input type="text" id="" name="pr_pass" value="<?php echo set_value('pr_pass'); ?>">
                <!--<span class="help-inline">Woohoo!</span>-->
            </div>
        </div>
        <?php
        echo '<div class="control-group">';
        echo '<label for="status" class="control-label">Статус</label>';
        echo '<div class="controls">';

        echo form_dropdown('status', array(0 => 'Работает', 1 => 'Не работает'), '', 'class="span2"');

        echo '</div>';
        echo '</div">';
        ?>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Сохранить</button>
            <button class="btn" type="reset">Отмена</button>
        </div>
    </fieldset>

    <?php echo form_close(); ?>

</div>
     