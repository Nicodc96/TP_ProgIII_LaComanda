<?php
require_once 'Pedido.php';

/* 
    Esta clase fue diseñada por Facu Falcone: https://github.com/caidevOficial. Créditos y agradecimientos. 
*/
class UploadManager{

    private $_DIR_TO_SAVE;
    private $_fileExtension;
    private $_newFileName;
    private $_pathToSaveImage;

    public function __construct($dirToSave, $pedido_id, $array){
        self::createDirIfNotExists($dirToSave);
        $this->setDirectoryToSave($dirToSave);
        $this->saveFileIntoDir($pedido_id, $array);
    }
    
    public function setDirectoryToSave($dirToSave){
        $this->_DIR_TO_SAVE = $dirToSave;
    }

    public function setFileExtension($fileExtension = 'png'){
        $this->_fileExtension = $fileExtension;
    }

    public function setNewFileName($newFileName){
        $this->_newFileName = $newFileName;
    }

    public function setPathToSaveImage(){
        $this->_pathToSaveImage = $this->getDirectoryToSave().'Pedido_'.$this->getNewFileName().'.'.$this->getFileExtension();
    }
    
    public function getFileExtension(){
        return $this->_fileExtension;
    }

    public function getNewFileName(){
        return $this->_newFileName;
    }

    public function getPathToSaveImage(){
        return $this->_pathToSaveImage;
    }

    public function getDirectoryToSave(){
        return $this->_DIR_TO_SAVE;
    }

    public static function getOrderImageNameExt($fileManager, $id){
        $fullpath = $fileManager->getPathToSaveImage();
        return $fullpath;
    }

    private static function createDirIfNotExists($dirToSave){
        if (!file_exists($dirToSave)) {
            mkdir($dirToSave, 0777, true);
        }
    }

    public function saveFileIntoDir($order_id, $array){
        $success = false;
        
        try {
            $this->setNewFileName($order_id);
            $this->setFileExtension();
            $this->setPathToSaveImage();
            if ($this->moveUploadedFile($array['foto_pedido']['tmp_name'])) {
                $success = true;
            }
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }finally{
            return $success;
        }
    }

    public function moveUploadedFile($tmpFileName){
        return move_uploaded_file($tmpFileName, $this->getPathToSaveImage());
    }

    public static function moveImageFromTo($oldDir, $newDir, $fileName){
        self::createDirIfNotExists($newDir);
        return rename($oldDir.$fileName, $newDir.$fileName);
    }
}
?>