<?php
/**
 * Data handler class
 * Author: Habib Hadi <hadicse@gmail.com>
 * @version  0.0.1 beta
 */

class LocalDatabase {

    public $directory;

    public function __construct($directory = null) {
        if (substr($directory, -1) != '/')
            $directory .= '/';

        $this->directory = (string) $directory;
    }

    public function save($filename, $content) {
        $file = $this->directory . $filename;

        if(!$this->put($file, $content)) {
            trigger_error(get_class($this) . " error: Couldn't write to $file", E_USER_WARNING);
            return false;
        }

        return true;
    }

    public function load($filename) {
        return $this->get($this->directory . $filename);
    }

    public function delete($filename) {
        return unlink($this->directory . $filename);
    }

    public function saveArray($filename, $array = array()){
        if(!is_array($array)) {
            trigger_error("Array isn't supplied! Please check 2nd parameter!", E_USER_WARNING);
            return false;
        }

        $content = serialize($array);
        $this->save($filename, $content);
    }

    public function insertArray($filename, $array = array()){
        if(!is_array($array) || count($array) == 0) return false;
        $data = $this->loadArray($filename);
        array_push($data, $array);
        $this->saveArray($filename, $data);
    }

    // $db->updateArray($table, ['taskname' => 'updated task name', 'date' => '15/15/2016'], '5698cb4c32670');
    public function updateArray($filename, $array = array(), $id) {
        if(!is_array($array) || count($array) == 0) return false;
        $data = $this->loadArray($filename);

        $new_array = [];
        foreach($data as $row) {
            if($row['id'] == $id) {
                $row = array_merge($row, $array);
            }
            $new_array[] = $row;
        }

        $this->saveArray($filename, $new_array);
    }

    public function deleteArray($filename, $id) {
        $data = $this->loadArray($filename);

        $new_array = [];
        foreach($data as $row) {
            if($row['id'] != $id) {
                $new_array[] = $row;
            }
        }

        $this->saveArray($filename, $new_array);
    }

    public function loadSingleArray($filename, $id) {
        $data = $this->loadArray($filename);
        foreach ($data as $row) {
            if($row['id'] == $id) {
                return $row;
            }
        }
    }

    public function loadArray($filename){
        $serialized_content = $this->get($this->directory . $filename);
        return unserialize($serialized_content);
    }

    public function isFile($filename){
        return file_exists($this->directory . $filename);
    }

    protected function put($file, $data, $mode = false) {
        if(file_exists($file) && file_get_contents($file) === $data) {
            touch($file);
            return true;
        }

        if(!$fp = @fopen($file, 'wb')) {
            return false;
        }

        fwrite($fp, $data);
        fclose($fp);

        $this->chmod($file, $mode);
        return true;

    }

    protected function chmod($file, $mode = false){
        if(!$mode)
            $mode = 0644;
        return @chmod($file, $mode);
    }

    protected function get($filename) {
        if(!$this->check($filename))
            return null;

        return file_get_contents($filename);
    }

    protected function check($filename){
        return file_exists($filename);
    }
}
