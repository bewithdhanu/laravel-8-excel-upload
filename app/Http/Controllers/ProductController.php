<?php

namespace App\Http\Controllers;

use App\Models\Product;
use http\Env\Request;
use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $inputFileType = "Csv";// Change this to what ever Xlsx,Xls,Csv
        $inputFileName = 'Sample1.csv';

        /**  Create a new Reader of the type defined in $inputFileType  **/
        $reader = IOFactory::createReader($inputFileType);
        /**  Advise the Reader that we only want to load cell data  **/
        $reader->setReadDataOnly(true);

        $worksheetData = $reader->listWorksheetInfo($inputFileName);

        $sheetName = $worksheetData[0]['worksheetName'];
        $reader->setLoadSheetsOnly($sheetName);
        $spreadsheet = $reader->load($inputFileName);

        $worksheet = $spreadsheet->getActiveSheet();
        $res = $worksheet->toArray();
        $data = $this->processData($res);
        Product::insert($data);
        print_r($data);
        return null;
    }

    private function processData(array $res)
    {
        $keys = ["name",
            "position",
            "uoc",
            "st_rate",
            "ot_rate",
            "tt_rate",
            "sub_rate",
            "distance_rate",
            "dt_rate"];
        $rows = [];
        foreach ($res as $row_index => $row) {
            $data = [];
            if ($row_index != 0) {
                foreach ($row as $index => $ele) {
                    $data[$keys[$index]] = $ele;
                }
                $rows[] = $data;
            }
        }
        return $rows;
    }
}
