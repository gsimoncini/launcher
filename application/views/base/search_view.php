<script type="text/javascript">
    //Muestra el formulario de busqueda.
    function show_search_form() {
        document.getElementById('search_form').style.display = 'block';
        document.getElementById('search_button_inactive').style.display = 'block';
        document.getElementById('search_button_active').style.display = 'none';
    }
    function dispose_search_form() {
        document.getElementById('search_form').style.display = 'none';
        document.getElementById('search_button_inactive').style.display = 'none';
        document.getElementById('search_button_active').style.display = 'block';
    }
</script>
<div id="searchbox">
    <a id="search_button_active" class="" href="#" onclick="show_search_form()"><?php echo $this->lang->line('label_search'); ?></a>
    <a id="search_button_inactive" class="" href="#" onclick="dispose_search_form()"><?php echo $this->lang->line('label_hide'); ?></a>
    <div class="clear"></div>
    <div id="search_form">
        <a class="fright" href="#" onclick="dispose_search_form()"><?php echo $this->lang->line('label_close'); ?></a>
        <h3><?php echo $this->lang->line('label_search'); ?></h3>
        <br/>

        <div class="clear"></div>
    </div>

</div>

