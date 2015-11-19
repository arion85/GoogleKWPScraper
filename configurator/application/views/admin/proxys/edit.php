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
            <a href="#">Изменить</a>
        </li>
    </ul>

    <div class="page-header">
        <h2>
            Изменить <?php echo ucfirst('прокси-сервер'); ?>
        </h2>
    </div>


    <?php
    //flash messages
    if ($this->session->flashdata('flash_message')) {
        if ($this->session->flashdata('flash_message') == 'updated') {
            echo '<div class="alert alert-success">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>OK!</strong> прокси-сервер успешно обновлен.';
            echo '</div>';
        } else {
            echo '<div class="alert alert-error">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>Ошибка!</strong> исправьте ошибки и попробуйте снова.';
            echo '</div>';
        }
    }
    ?>

    <?php
    //form data
    $attributes = array('class' => 'form-horizontal', 'id' => '');

    //form validation
    echo validation_errors();

    echo form_open('admin/proxys/update/' . $this->uri->segment(4) . '', $attributes);
    ?>
    <fieldset>
        <div class="control-group">
            <label for="inputError" class="control-label">IP адрес</label>
            <div class="controls">
                <input type="text" id="" name="pr_ip" value="<?php echo $proxy[0]['pr_ip']; ?>">
                <!--<span class="help-inline">Woohoo!</span>-->
            </div>
        </div>
        <div class="control-group">
            <label for="inputError" class="control-label">Порт</label>

            <div class="controls">
                <input type="text" id="" name="pr_port" value="<?php echo $proxy[0]['pr_port']; ?>">
                <!--<span class="help-inline">Woohoo!</span>-->
            </div>
            </div>
        <div class="control-group">
            <label for="inputError" class="control-label">Логин</label>

            <div class="controls">
                <input type="text" id="" name="pr_login" value="<?php echo $proxy[0]['pr_login']; ?>">
                <!--<span class="help-inline">Woohoo!</span>-->
            </div>
            </div>
        <div class="control-group">
            <label for="inputError" class="control-label">Пароль</label>

            <div class="controls">
                <input type="text" id="" name="pr_pass" value="<?php echo $proxy[0]['pr_pass']; ?>">
                <!--<span class="help-inline">Woohoo!</span>-->
            </div>
            </div>
        <?php
        echo '<div class="control-group">';
        echo '<label for="status" class="control-label">Статус</label>';
        echo '<div class="controls">';

        echo form_dropdown('status', array(0 => 'Работает', 1 => 'Не работает'), $proxy[0]['status'], 'class="span2"');

        echo '</div>';
        echo '</div">';
        ?>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Применить</button>
            <button class="btn" type="reset">Отменить</button>
        </div>
    </fieldset>

    <?php echo form_close(); ?>

</div>
     