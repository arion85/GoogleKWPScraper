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

            $options_countries = array('' => "Выберите страну");
            foreach ($countries as $row) {
                $options_countries[$row['id']] = $row['full_name'];
            }
            //form validation
            echo validation_errors();

            echo form_open('admin/main/add', array('class' => 'form-horizontal', 'id' => ''));
            echo form_fieldset();
            ?>
                <div class="control-group">

                    <?php echo form_label('Состояние', 'status', array('class'=>'control-label input-group')); ?>

                    <div class="btn-group" data-toggle="buttons" id="status">
                        <?php echo form_label(trim(form_radio(array('name'=>'status'),'1',$params->status==1?true:false)).'&nbsp;'.'Включен', '' , array('class'=>'btn btn-default')); ?>
                        <?php echo form_label(trim(form_radio(array('name'=>'status'),'0',$params->status==0?true:false)).'&nbsp;'.'Отключен', '' , array('class'=>'btn btn-default')); ?>
                    </div>
                </div>

                <div class="control-group">

                    <?php echo form_label('Количество потоков', 'thr_cnt', array('class'=>'control-label')); ?>

                    <div class="controls">
                        <?php echo form_input(array('name'=>'thr_cnt','id'=>'thr_cnt'),$params->thr_cnt,array('class'=>'input-mini'));?>
                        <!--<span class="help-inline">Woohoo!</span>-->
                    </div>
                </div>

                <div class="control-group">

                    <?php echo form_label('Страна', 'country_id', array('class'=>'control-label')); ?>

                    <div class="controls">
                        <?php echo form_dropdown(array('name'=>'country_id','id'=>'country_id'), $options_countries, $params->country_id, 'class="span3"'); ?>
                        <!--<span class="help-inline">Woohoo!</span>-->
                    </div>
                </div>

                <div class="form-actions">
                    <?php echo form_submit('submitParams', 'Сохранить', array('class'=>'btn btn-primary'));?>
                </div>
            <?php
                echo form_fieldset_close();
                echo form_close();
            ?>
    </div>
    </div>