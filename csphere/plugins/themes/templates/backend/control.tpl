<div class="panel panel-default">
    <div class="panel-body">

        {* tpl default/com_header plugin=themes.themes action=default.control *}

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>{* lang themes.theme *}</th>
                    <th class="text-center">{* lang default.version *}</th>
                    <th class="text-center">{* lang default.published *}</th>
                </tr>
            </thead><!--END table thead-->

            <tbody>
                {* foreach themes *}
                <tr>
                    <td>
                        <a href="{* link themes/details/dir/$themes.short$ *}">
                            <i class="fa fa-fw {* var themes.icon *}"></i>
                            &nbsp; {* var themes.short *}
                        </a>
                    </td>
                    <td class="text-center">{* var themes.version *}</td>
                    <td class="text-center">{* var themes.pub *}</td>
                </tr>
                {* endforeach themes *}
            </tbody><!--END table tbody-->
        </table><!--END table-->

    </div><!--END panel-body-->
</div><!--END panel-->
