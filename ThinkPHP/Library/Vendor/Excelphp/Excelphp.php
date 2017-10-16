<?php
// +----------------------------------------------------------------------
// | php操作 excel 导入导出模块 引用调用PHPExcel
// +----------------------------------------------------------------------
// | Author: vition <369709991@qq.com>
// +----------------------------------------------------------------------
require_once("Classes/PHPExcel.php");
// require_once("Classes/PHPExcel.php");
Class Excelphp{

    function __construct(){
        
        // require_once("Classes/PHPExcel.php");
    }


    function readExcel($excelPath){
        $objPHPExcel=PHPExcel_IOFactory::load($excelPath);

        $currentSheet= $objPHPExcel->getSheet(0);
        
        $allColumn = $currentSheet->getHighestColumn();//列数 返回字母
        $allRow = $currentSheet->getHighestRow();//行数 返回数字
        $resultData=array();//定义返回数据        
        for ($currentRow=2; $currentRow <=$allRow ; $currentRow++) { 
            // $cell = $currentSheet->getCellByColumnAndRow(1, $currentRow);
            for ($currentCol=0; $currentCol <=(ord($allColumn)-ord("A")); $currentCol++) { 
                $cell = $currentSheet->getCellByColumnAndRow($currentCol, $currentRow);
                $cellData=$cell->getFormattedValue();
                if($currentCol==0){
                    echo date("Y-m-d",strtotime($cellData));
                }else{
                    echo $cellData;
                }
                echo "|";
            }
            echo "</br>";
        }
    }

    function writeExcel($dataArray,$name){
        if(empty($dataArray)){
            echo "没有数据";
            return false;
        }
        $Excelphp=new PHPExcel();
        $workSheet=$Excelphp->getActiveSheet(); 
        // $allCol=count($dataArray[0]);
        foreach($dataArray as $row=>$data){
            $col=0;
            foreach($data as $value){
                $workSheet->setCellValueByColumnAndRow($col, $row+1, $value);
                $col++;
            }
        }
        header('Content-Type: application/vnd.ms-excel');  
        header('Content-Disposition: attachment;filename="'.$name.'.xlsx"');  
        // header('Cache-Control: max-age=0'); 
        $objWriter = PHPExcel_IOFactory::createWriter($Excelphp, 'Excel2007');  
        $objWriter->save( 'php://output'); 
    }
}
