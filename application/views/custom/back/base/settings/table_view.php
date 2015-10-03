<style>
    input[readonly] {
        border-color: transparent;
    }

    #settings_table > tbody > tr#new {
        display: none;
    }
</style>

<div style="height: initial;" class="sticky-wrapper" id="undefined-sticky-wrapper">
    <div style="width: 1059px;" class="sticker">
        <div id="action-buttons">
            <div class="dropdown btn-group">
                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
                    <?= $group ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                    <?php
                    foreach ($groups as $_group) {
                        if ($_group != $group) {
                            ?>
                            <li><a href="<?= site_url() ?>/custom/settings/table/<?= $_group ?>" target="_self"><?= $_group ?></a></li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
            <a id="btn_new" class="btn btn-sm btn-default" href="javascript:void();">
                <i class="fa fa-plus btn-icon-margin"></i>Nueva clave
            </a>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <form method="POST" action="<?= site_url() ?>/custom/settings/table">
        <input type="hidden" name="group" value="<?= $group ?>"/>
        <table class="table" id="settings_table" border="0" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>PARAMETRO</th>
                    <th>VALOR</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <tr id="new">
                    <td class="text_column key">
                        <input type="text" name="new[0][key]" value="" placeholder="--------------------------------" readonly/>
                    </td>
                    <td class="text_column value">
                        <input type="text" name="new[0][val]" value="" placeholder="--------------------------------" readonly/>
                    </td>
                    <td class="text_column delete">
                        <a href="javascript:void();" data-delete="remove">Eliminar</a>
                    </td>
                </tr>
                <?php foreach ($settings as $setting) { ?>
                    <tr>
                        <td class="text_column key">
                            <input type="text" name="H<?= $strToHex($setting->setting) ?>[key]" value="<?= $setting->setting ?>" readonly/>
                        </td>
                        <td class="text_column value">
                            <input type="text" name="H<?= $strToHex($setting->setting) ?>[val]" value="<?= $setting->value ?>" readonly/>
                        </td>
                        <td class="text_column delete">
                            <a href="javascript:void();" data-delete="clear">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </form>
</div>

<script>
    $("#settings_table").on("click", "input[type='text'][readonly]", function () {
        $(this).attr("readonly", false);
    });
    $("#settings_table").on("blur", "input[type='text'][readonly!='readonly']", function () {
        $(this).attr("readonly", true);
    });
    $("#action-buttons").on("click", "#btn_new", function () {
        var size = $("#settings_table > tbody > tr.new").size();
        var $new = $("#settings_table > tbody > tr#new").clone().removeAttr("id").addClass("new");
        var $inputs = $new.find(" > td.text_column > input");
        $.each($inputs, function () {
            if ($(this).attr("name") == "new[][key]") {
                $(this).attr("name", "new[" + size + "][key]");
            } else if ($(this).attr("name") == "new[][val]") {
                $(this).attr("name", "new[" + size + "][val]");
            }
        });
        $("#settings_table > tbody").append($new);
        return false;
    });
    $("#settings_table > tbody").on("click", " > tr > td.text_column.delete > a", function (event) {
        var $this = $(this);
        baseController.confirmFormSubmit(event, function () {
            var dataDelete = $this.attr("data-delete");
            if (dataDelete == "clear") {
                var $tr = $this.parents("tr");
                $.each($tr.find(" > td > input"), function () {
                    $(this).removeAttr("value");
                });
                $tr.hide();
            }
            if (dataDelete == "remove") {
                var $tr = $this.parents("tr");
                $tr.remove();
            }
            return false;
        });
    });
</script>