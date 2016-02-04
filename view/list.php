<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Larry DBVC</title>
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

<h1 class="am-animation-slide-left am-animation-delay-1">Larry DBVC 多人開發資料庫結構版控解決方案</h1>

<div data-am-sticky>
    <div class="am-btn-group am-animation-slide-top">
        <button type="button" class="am-btn am-btn-primary am-round btn-submit-init"
                data-am-popover="{theme: 'warning sm',content: '將資料庫版本還原到引入版本之前的結構', trigger: 'hover focus'}"
                data-am-modal="{target: '#modal-handling'}">初始化
        </button>
        <button type="button" class="am-btn am-btn-primary am-round btn-submit-auto_update"
                data-am-popover="{theme: 'warning sm',content: '自動更新至最新的開發版本', trigger: 'hover focus'}"
                data-am-modal="{target: '#modal-handling'}">自動更新
        </button>
        <button type="button" id="1454060274" class="am-btn am-btn-primary am-round btn-submit-up"
                data-am-popover="{theme: 'warning sm',content: '上升一個版本', trigger: 'hover focus'}"
                data-am-modal="{target: '#modal-handling'}"><span class="am-icon-arrow-up"></span></button>
        <button type="button" id="1454060274" class="am-btn am-btn-primary am-round btn-submit-down"
                data-am-popover="{theme: 'warning sm',content: '下降一個版本', trigger: 'hover focus'}"
                data-am-modal="{target: '#modal-handling'}"><span class="am-icon-arrow-down"></span></button>
    </div>
</div>
<hr>
<table id="versionTable" class="am-table am-table-bordered am-table-radius am-animation-slide-top">
    <thead>
    <tr>
        <th>版本號</th>
        <th>建立日期</th>
        <th>寫入日期</th>
        <th>開發者</th>
        <th>註解</th>
    </tr>
    </thead>
    <tbody></tbody>
</table>
<hr>
<table id="updateMessage" class="am-table am-table-bordered am-table-radius am-table-striped">
    <thead>
    <tr>
        <th>訊息</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td></td>
    </tr>
    </tbody>
</table>

<footer class="am-margin-top">
    <hr/>
    <p class="am-text-center">
        <small>by Larry Li.</small>
    </p>
</footer>
<div class="am-modal am-modal-alert" tabindex="-1" id="modal-handling">
    <div class="am-modal-dialog">
        <div id="modal-title" class="am-modal-hd">處理中
            <a href="javascript: void(0)" class="am-close am-close-spin" data-am-modal-close>&times;</a>
        </div>
        <div id="modal-body" class="am-modal-bd">
            <i class="am-icon-spinner am-icon-spin am-icon-lg"></i>
        </div>
    </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/amazeui.min.js"></script>

</body>
</html>

<script type="text/javascript">
    $(function () {

        //timestamp時間格式化
        var datetimefmt = function (timestamp) {
            if (timestamp == '') {
                return "<span style='color:red' >尚未寫入</span>";
            }
            var d = new Date(timestamp * 1000),
                dformat = [d.getFullYear(),
                        (d.getMonth() + 1),
                        d.getDate()].join('/') + ' ' +
                    [d.getHours(),
                        d.getMinutes(),
                        d.getSeconds()].join(':');
            return dformat;
        };

        //判斷當前版本高亮顯示
        var nowVersionHighLight = function (nowVersion, VersionNumber) {
            if (nowVersion == VersionNumber) {
                return 'am-active';
            }
        };

        //事件綁定
        var eventBind = function (method) {
            $(".btn-submit-" + method).on("click", function (event) {
                var $modal = $('#modal-handling');
                var $messageTable = $('#updateMessage');
                $.getJSON('', {c: "Admin", f: method}, function (json, textStatus) {
                    var message = "";
                    for (var i = 0; i < json.length; i++) {
                        if (json[i][0] == 1) {
                            message = "<tr class ='am-animation-slide-left'><td><span style='color:blue'>" + json[i][1] + "</span></td></tr>";
                        } else {
                            message = "<tr class ='am-animation-slide-left'><td><span style='color:red'>" + json[i][1] + "</span></td></tr>";
                        }
                        $messageTable.find('tbody').prepend(message);
                    }
                    $('#versionTable').trigger('reload');
                    $modal.modal('close');
                })
            })
        };

        //自訂重新載入事件
        $('#versionTable').on('reload', function (event) {
            progress.start();
            $.getJSON('', {c: "Admin", f: "dbvc_list"}, function (json, textStatus) {
                var tableinfo = "";
                $.each(json.vList, function (index, val) {
                    tableinfo += "<tr class='" + nowVersionHighLight(json.nowVersion, val['vc_file']) + "'><td>VC_" + val['vc_file'] + "</td><td>" + datetimefmt(val['vc_file']) + "</td><td>" + datetimefmt(val['create_date']) + "</td><td>" + val['create_author'] + "</td><td>" + val['v_comment'] + "</td></tr>";
                });
                $('#versionTable').find('tbody').html(tableinfo);
                progress.done();
            });
        });


        var progress = $.AMUI.progress;


        $('#versionTable').trigger('reload');


        eventBind('init');
        eventBind('auto_update');
        eventBind('up');
        eventBind('down');


    });
</script>