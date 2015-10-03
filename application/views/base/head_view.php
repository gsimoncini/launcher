<div class="navbar navbar-default navbar-static-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only"><?php echo $this->lang->line('label_show') . '/' . $this->lang->line('label_hide_menu'); ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="navbar-brand">
                <a title="<?php echo $this->config->item('software_title') ?>" href="<?php echo base_url() ?>"><img src="<?php echo base_url($this->config->item('default_logo')) ?>" alt=""/></a>
            </div>
        </div>
        <div class="navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->session->userdata('name') ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo site_url('back/home/userdata') ?>"><i class="fa fa-pencil btn-icon-margin"></i>Editar datos</a></li>
                        <li><a href="<?php echo site_url('back/users/change_password') ?>"><i class="fa fa-key btn-icon-margin"></i><?php echo $this->lang->line('system_change_password'); ?></a></li>
                        <!--<li><a href="<?php echo site_url('custom/settings/table') ?>"><i class="fa fa-wrench btn-icon-margin"></i><?php // echo $this->lang->line('system_settings');  ?></a></li>-->
                        <li class="divider"></li>
                        <li><a href="<?php echo site_url('login/logout') ?>"><i class="fa fa-sign-out btn-icon-margin"></i>Salir</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
<!--- INFORMACION DE FILTRO GLOBAL -->
<div class="global-filter-selected hide">
    <div class="boxshadow"></div>
    <div class="panel">
        <div class="pane-body" >
            <div class="row">
                <div class="col-md-5">
                    <a href="#" class="thumbnail"><img src="" alt=""></a>
                </div>
                <div class="col-md-7" style="padding-left:0px; text-overflow:ellipsis; overflow: hidden;">
                    <h5 class="title"></h5>
                    <div class="subtitle"></div>
                    <div class="content-text"></div>
                </div>
            </div>
        </div>
    </div>
</div>