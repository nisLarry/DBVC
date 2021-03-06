<?php

namespace Lib\Builder;

use Lib\Db\TableInfo;

/**
 * 基本檔案生成控制器
 * @package Lib\Builder
 */
class BaseFileBuilder
{
    private $out_folder;
    public function __construct($out_folder)
    {
        $this->out_folder = $out_folder;
        $this->check_dir($this->out_folder);
    }

    /**
     * 建立語言檔
     * @return bool 建立結果
     */
    public function builder()
    {
        $content = $this->make_content();
        $resulte = $this->write_file($content);

        return $resulte;
    }

    /**
     * 檢查資料夾是否存在，如果不存在就建立一個新的資料夾。
     * @param $dirname 資料夾名稱
     */
    public function check_dir($dirname)
    {
        if (!is_dir($dirname)) {
            mkdir($dirname);
        }
    }

    /**
     * 取得範本內容
     * @param $template_path 範本路徑
     * @return string 範本內容
     */
    public function get_template($template_path)
    {
        $template_content = file_get_contents($template_path);
        return $template_content;
    }

    /**
     * 回傳資料夾路徑
     * @return String 資料夾路徑
     */
    public function getOutFolder()
    {
        return $this->out_folder;
    }

    /**
     * 製作填充內容(需自己實作)
     * @return string Controller檔內容
     */
    public function make_content()
    {
        return "";
    }


    /**
     * 寫入檔案 (需自己實作)
     * @param mixed $content 要寫入的內容，此內容會取代掉範本檔裡的{t:....}相關標簽(注意，這邊的內容應該由make_content()產生)
     * @return bool 寫入結果
     */
    public function write_file($content)
    {
        $template_content = $this->get_template(dirname(__FILE__).DIRECTORY_SEPARATOR."template.php");
        $file_name = "VC_".time();
        $file_path = $this->out_folder.DIRECTORY_SEPARATOR.$file_name.'.php';
        $fp = fopen($file_path, 'w');
        $template_content = str_replace("{t:vc_file_name}",$file_name,$template_content);

        mb_convert_encoding($template_content, 'UTF-8');
        fwrite($fp, $template_content);
        fclose($fp);

        return $file_path;
    }

}