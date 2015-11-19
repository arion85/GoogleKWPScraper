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
            <a href="#">Новый</a>
        </li>
    </ul>

    <div class="page-header">
        <h2>
            Добавить <?php echo ucfirst('аккаунт гугл'); ?>
        </h2>
    </div>

    <?php
    //flash messages
    if (isset($flash_message)) {
        if ($flash_message == TRUE) {
            echo '<div class="alert alert-success">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>OK!</strong> новый аккаунт добавлен успешно.';
            echo '</div>';
        } else {
            echo '<div class="alert alert-error">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>Ошибка!</strong> исправьте ошибки и повторите отправку.';
            echo '</div>';
        }
    }
    ?>

    <?php
    //form data
    $attributes = array('class' => 'form-horizontal', 'id' => '');

    //form validation
    echo validation_errors();

    echo form_open('admin/accounts/add', $attributes);
    ?>
    <fieldset>
        <div class="control-group">
            <label for="inputError" class="control-label">Логин</label>

            <div class="controls">
                <input type="text" id="" name="gm_login" value="<?php echo set_value('gm_login'); ?>">
                <!--<span class="help-inline">Woohoo!</span>-->
            </div>
            </div>
        <div class="control-group">
            <label for="inputError" class="control-label">Пароль</label>

            <div class="controls">
                <input type="text" id="" name="gm_pass" value="<?php echo set_value('gm_pass'); ?>">
                <!--<span class="help-inline">Cost Price</span>-->
            </div>
            </div>
        <div class="control-group">
            <label for="inputError" class="control-label">Телефон</label>

            <div class="controls">
                <input type="text" id="" name="gm_tel" value="<?php echo set_value('gm_tel'); ?>">
                <!--<span class="help-inline">Cost Price</span>-->
            </div>
            </div>
        <div class="control-group">
            <label for="inputError" class="control-label">Запасной e-mail</label>

            <div class="controls">
                <input type="text" name="gm_recovery_email" value="<?php echo set_value('gm_recovery_email'); ?>">
                <!--<span class="help-inline">OOps</span>-->
            </div>
            </div>
        <?php
        $options_proxys = array(0 => '-Выберите прокси-');
        foreach ($proxys as $array) {
            $options_proxys[$array['id']] = $array['pr_ip'];
        }
        ?>
        <div class="control-group">
            <label for="inputError" class="control-label">Прокси IP</label>

            <div class="controls">
                <?php echo form_dropdown('proxy_ip', $options_proxys, 0, 'class="span2"'); ?>
                <!--<span class="help-inline">OOps</span>-->
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
            <button class="btn btn-primary" type="submit">Применить</button>
            <button class="btn" type="reset">Отмена</button>
        </div>
    </fieldset>

    <?php echo form_close(); ?>

</div>
     