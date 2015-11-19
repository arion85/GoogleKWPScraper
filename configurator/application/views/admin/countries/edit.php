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
            <a href="#">Update</a>
        </li>
    </ul>

    <div class="page-header">
        <h2>
            Updating <?php echo ucfirst($this->uri->segment(2)); ?>
        </h2>
    </div>


    <?php
    //flash messages
    if ($this->session->flashdata('flash_message')) {
        if ($this->session->flashdata('flash_message') == 'updated') {
            echo '<div class="alert alert-success">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>Ok!</strong> Данные страны обновлены.';
            echo '</div>';
        } else {
            echo '<div class="alert alert-error">';
            echo '<a class="close" data-dismiss="alert">×</a>';
            echo '<strong>Ошибка!</strong> исправьте ошибку и попробуйте снова.';
            echo '</div>';
        }
    }
    ?>

    <?php
    //form data
    $attributes = array('class' => 'form-horizontal', 'id' => '');

    //form validation
    echo validation_errors();

    echo form_open('admin/countries/update/' . $this->uri->segment(4) . '', $attributes);
    ?>
    <fieldset>
        <div class="control-group">
            <label for="inputError" class="control-label">Название</label>

            <div class="controls">
                <input type="text" id="" name="full_name" value="<?php echo $country[0]['full_name']; ?>">
                <!--<span class="help-inline">Woohoo!</span>-->
            </div>
        </div>
        <div class="control-group">
            <label for="inputError" class="control-label">Короткое название</label>

            <div class="controls">
                <input type="text" id="" name="short_name" value="<?php echo $country[0]['short_name']; ?>"
                       disabled="disabled">
                <!--<span class="help-inline">Woohoo!</span>-->
            </div>
        </div>
        <div class="control-group">
            <label for="inputError" class="control-label">Добавить ключевые слова</label>

            <div class="controls">
                <textarea id="" rows="10" name="start_words"><?php echo set_value('start_words'); ?></textarea>
                <!--<span class="help-inline">Woohoo!</span>-->
            </div>
        </div>
        <div class="control-group">
            <label for="inputError" class="control-label">Стоп-слова</label>

            <div class="controls">
                <textarea id="" rows="10" name="stop_words"><?php echo $country[0]['stop_words']; ?></textarea>
                <!--<span class="help-inline">Woohoo!</span>-->
            </div>
        </div>
        <div class="control-group">
            <label for="inputError" class="control-label">ID страны для автоактиватора</label>

            <div class="controls">
                <input type="text" id="" name="country_id" value="<?php echo $country[0]['country_id']; ?>">
                <!--<span class="help-inline">Woohoo!</span>-->
            </div>
        </div>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Save changes</button>
            <button class="btn" type="reset">Cancel</button>
        </div>
    </fieldset>

    <?php echo form_close(); ?>

</div>
     