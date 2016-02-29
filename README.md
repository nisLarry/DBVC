# Larry DBVC(Database Version Control System)

##簡介
Larry DBVC 是一個基於PHP+MYSQL的資料庫結構版控系統，提供了網頁操作介面及命令列操作介面。你可以非常方便的進行資料庫結構的管理，包含版本的前進、後退。

##我想解決的問題
在多人開發的情況下，我們常常會發現每個開發人員的本地和測試機資料庫的結構都不一致。為了解決這樣的問題，我希望能提供一個統一的管理介面，讓所有的開發機、測試機都能夠保持資料庫結構的版本一致。

##引入DBVC之後的工作流程
![工作流程][1]



##使用教學

###1.資料庫配置
####/LiB/Db/db_config.json
```json
{
	"host":"localhost",//資料庫伺服器位置
	"user":"root",//使用者名稱
	"pwd":"",//密碼
	"dbName":"dbvc"//資料庫名稱
}
```

###2.建立版控檔案
在命令介面下執行：

    D:\projects\DBVC>php CLI.php create
    VCFiles\VC_1454664931.php  be created.

接著可以在\VCFiles\中找到提示中的版控檔案，可以參考其它的範例檔來做編輯。
```php
<?php

namespace VCFiles;

use Lib\VersionControl\BaseFunction;

class VC_1454664931 extends BaseFunction
{
    public function up()
    {
        //TODO//這裡需要回傳一段資料庫結構更新需要的sql腳本
    }

    public function down()
    {
        //TODO//這裡需要回傳一段資料庫結構下降需要的sql腳本
    }

    public function comment()
    {
        //TODO//這裡需要回傳一段此次版本更新的說明
    }

    public function author()
    {
        //TODO//這裡需要回傳開發者的名稱
    }
}
```
###3.命令介面操作
####/CLI.php
#####命令列表
| 命令        | 功能   |
| --------   | -----  |
| help     | 顯示所有命令 |
| install        |   安裝DBVC.   |
| getlist        |   顯示所有版本檔案   |
| create        |    建立一個新的版本檔    |
| up          |    更新一個版本，也可以輸入一個版本號作為參數，更新到指定版本    |
| down        |    下降一個版本，也可以輸入一個版本號作為參數，下降到指定版本    |
| init        |    還原到未引入DBVC前的資料庫結構    |
| auto_update        |    更新到開發最新版本    |

#####示範
```
D:\projects\DBVC>php CLI.php help
help  : show all command.
getlist  : show all version files.
create  : create a new version file.
up [v_no]  : up database structure to assign version. you can enter a version number. default up one version.
down [v_no]  : down database structure to assign version. you can enter a version number. default down one version.
init  : database structure initialization.
auto_update  : up database structure to the latest version.
```

###4.網頁介面操作
####/index.php

#####資料庫版控安裝檢測
![資料庫版控檢測][2]
按下（是）之後，就會在資料庫中新建一個db_vc資料表，來做為版控之用。

#####功能提示訊息
![此处输入图片的描述][3]

#####操作結果訊息
![此处输入图片的描述][4]


###最後
感謝大家的使用，有任何的問題，歡迎使用[issues][5]來對我提出反饋。

我是[@nislarry][6]


  [1]: /Design/UML/workflow/workflow.png
  [2]: /Design/example/image1.png
  [3]: /Design/example/image3.png
  [4]: /Design/example/image2.png
  [5]: http://192.168.18.19/larrynis/DBVC/issues
  [6]: http://192.168.18.19/larrynis