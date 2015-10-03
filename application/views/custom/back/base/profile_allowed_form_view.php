<?php
//Clave primaria  
if (isset($id))
    echo $id->generate();
?>
<div class="row">
    <?php echo form_group($name, 6); ?>
</div>
<div class="panel panel-default"><?php echo $table; ?></div>