<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Larry TTO</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp"/>

    <link rel="icon" type="image/png" href="assets/i/favicon.png">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="icon" sizes="192x192" href="assets/i/app-icon72x72@2x.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Larry TTO"/>
    <link rel="apple-touch-icon-precomposed" href="assets/i/app-icon72x72@2x.png">
    <meta name="msapplication-TileImage" content="assets/i/app-icon72x72@2x.png">
    <meta name="msapplication-TileColor" content="#0e90d2">
    <link rel="stylesheet" href="assets/css/amazeui.min.css">
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

<h1 class="am-animation-slide-left am-animation-delay-1">Larry TTO 資料庫表格轉換物件工具</h1>

<table class="am-table am-table-bordered am-table-radius">
    <thead>
    <tr>
        <th>表格名稱</th>
        <th>注釋</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody></tbody>
</table>

<footer class="am-margin-top">
    <hr/>
    <p class="am-text-center">
        <small>by Larry Li.</small>
    </p>
</footer>
<div class="am-modal am-modal-no-btn" tabindex="-1" id="doc-modal-success">
    <div class="am-modal-dialog">
        <div class="am-modal-hd">建立完成
            <a href="javascript: void(0)" class="am-close am-close-spin" data-am-modal-close>&times;</a>
        </div>
        <div class="am-modal-bd">
            檔案建立完成，請至<h2 style="color:red">\Out</h2>資料夾取得您的檔案！
        </div>
    </div>
</div>

<div class="am-modal am-modal-no-btn" tabindex="-1" id="doc-modal-fail">
    <div class="am-modal-dialog">
        <div class="am-modal-hd">建立失敗
            <a href="javascript: void(0)" class="am-close am-close-spin" data-am-modal-close>&times;</a>
        </div>
        <div class="am-modal-bd">
            <h2 style="color:red">發生了未預期的錯誤！</h2>
        </div>
    </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/amazeui.min.js"></script>

</body>
</html>

<script type="text/javascript">
    $(function () {
        var progress = $.AMUI.progress;
        progress.start();
        $.getJSON('', {handle: "GetDbInfo"}, function (json, textStatus) {
            var tableinfo = "";
            $.each(json, function (index, val) {
                var select = "<select multiple data-am-selected id='"+val['TABLE_NAME']+"_select"+"'><option value='gethtml'>產生HTML</option><option value='getlanguage'>產生語言檔</option><option value='getcontrol'>產生控制器</option></select>";
                var button = "<button type='button' id = '"+val['TABLE_NAME']+"' class='am-btn am-btn-primary btn-loading-example' data-am-loading=\"{loadingText: '檔案建立...'}\">執行</button>";
                tableinfo += "<tr><td>" + val['TABLE_NAME'] + "</td><td>" + val['TABLE_COMMENT'] + "</td><td>" + select + button + "</td></tr>";
            });
            $('tbody').append(tableinfo);

            //選擇器
            $('select').selected({
                btnWidth: '300px',
                placeholder: '請選擇想要的操作…'
            });

            //執行按鈕
            $('.btn-loading-example').click(function () {
                var tableName = $(this).attr("id");
                var $checkLi = $("#"+tableName+"_select").next().find("li.am-checked");
                var builderArr = new Array();

                $.each($checkLi,function(index,element){
                    var builderMethod = {};
                    var $e = $(element);
                    builderMethod.name = $e.attr("data-value");
                    builderArr.push(builderMethod);
                });

                var $btn = $('.btn-loading-example');
                $btn.button('loading');
                $.getJSON("", {handle: 'RunBuilder', table_name: tableName, runMethod:JSON.stringify(builderArr)}, function (json, textStatus) {
                    if(json.result){
                        $('#doc-modal-success').modal();
                    }else{
                        $('#doc-modal-fail').modal();
                    }
                    $btn.button('reset');
                })

            });
            progress.done();
        });

    });
</script>