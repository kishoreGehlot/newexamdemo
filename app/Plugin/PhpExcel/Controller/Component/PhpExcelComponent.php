<?php
App::uses('Component', 'Controller');
class PhpExcelComponent extends Component {
    /**
     * Instance of PHPExcel class
     *
     * @var PHPExcel
     */
    protected $_xls;

    public function createWorksheet() {
        // load vendor classes
        App::import('Vendor', 'PhpExcel.PHPExcel');

        $this->_xls = new PHPExcel();
        $this->_xls->setActiveSheetIndex(0);
        return $this;
    }

    /**
     * Create new worksheet from existing file
     *
     * @param string $file path to excel file to load
     * @return $this for method chaining
     */
    public function loadWorksheet() {
        // load vendor classes
        App::import('Vendor', 'PhpExcel.PHPExcel');

        $this->_xls = new PHPExcel();
        return $this;
    }

    /**
     * Write array of data to current row
     *
     * @param array $data
     * @return $this for method chaining
     */
    public function addTableRow($data) {
        $this->_xls->getActiveSheet()->fromArray($data, null, 'A1');
        return $this;
    }

   
    /**
     * Get writer
     *
     * @param $writer
     * @return PHPExcel_Writer_Iwriter
     */
    public function getWriter($writer) {
        return PHPExcel_Writer_Excel5($this->_xls, $writer);
    }

    /**
     * Save to a file
     *
     * @param string $file path to file
     * @param string $writer
     * @return bool
     */
    public function save($file, $writer = 'Excel2007')
    {
        $objWriter = new PHPExcel_Writer_Excel5($this->_xls);
	return $objWriter->save($file);
    }

    /**
     * Output file to browser
     *
     * @param string $file path to file
     * @param string $writer
     * @return exit on this call
     */
    public function output($file,$author,$filename = 'export.xlsx', $writer = 'Excel2007')
    {
        $this->_xls->getProperties()->setCreator($author);
        $this->_xls->getProperties()->setLastModifiedBy($author);
        $this->_xls->getProperties()->setTitle("Office 2007 XLSX $file");
        $this->_xls->getProperties()->setSubject("Office 2007 XLSX $file");
        $this->_xls->getProperties()->setDescription("$file for Office 2007 XLSX, generated using PHP classes.");
        $this->_xls->getProperties()->setKeywords("office 2007 openxml php");
        $this->_xls->getProperties()->setCategory("$file file");
        // remove all output
        ob_end_clean();
        

        // headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // writer
        $this->save('php://output');
        $this->freeMemory();
        exit;
    }
    
    /**
     *
     *@ import Excel
     */
    public function importData($inputFileType,$inputFileName)
    {
        try
        {
            /**  Create a new Reader of the type defined in $inputFileType  **/
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            /**  Advise the Reader that we only want to load cell data  **/
            $objReader->setReadDataOnly(true);
            /**  Load $inputFileName to a PHPExcel Object  **/
            $this->_xls = $objReader->load($inputFileName);
            //  Get worksheet dimensions
            $sheet = $this->_xls->getSheet(0); 
            $highestRow = $sheet->getHighestRow(); 
            $highestColumn = $sheet->getHighestColumn();
            
            //  Loop through each row of the worksheet in turn
            for($row = 1; $row <= $highestRow; $row++)
            {
                //  Read a row of data into an array
                if($row>1)
                $rowData[] = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,NULL,TRUE,FALSE);
            }
            return $rowData;
        }
        catch(Exception $e)
        {
            die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }
    }

    /**
     * Free memory
     *
     * @return void
     */
    public function freeMemory() {
        $this->_xls->disconnectWorksheets();
        unset($this->_xls);
    }
}