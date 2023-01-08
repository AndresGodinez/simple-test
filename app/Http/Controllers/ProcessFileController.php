<?php

namespace App\Http\Controllers;

use League\Csv\EscapeFormula;
use League\Csv\Reader;
use League\Csv\Statement;

use Illuminate\Http\Request;
use League\Csv\Writer;

class ProcessFileController extends Controller
{
    protected array $dataCSV;
    protected $csv;
    protected $arrResult = [];

    public function process(Request $request)

    {
        $path = $request->file('csv_file')->getRealPath();
        $originalName = $request->file('csv_file')->getClientOriginalName();
        $data = array_map('str_getcsv', file($path));

        $this->setCsv($path);
        $this->setDataCSV($data);
        $this->processContent();

        $resource = fopen($path, 'r+');

        $formatter = new EscapeFormula("`");

        foreach ($this->arrResult as $record) {
            fputcsv($resource, $formatter->escapeRecord($record));
        }

        return response()->download($path, $originalName);

    }

    public function setDataCSV($data): void
    {
        $this->dataCSV = $data;
    }

    public function getDataCSV(): array
    {
        return $this->dataCSV;
    }

    protected function setCsv($path)
    {
        $this->csv = Reader::createFromPath($path, 'r');
        $this->csv->setHeaderOffset(0);
    }

    protected function processContent()
    {
        $records = $this->csv->getRecords();
        $counter =0;
        foreach ($records as $record) {
            $counter++;
            $this->processRow($record, $counter);
        }
    }

    protected function processRow($row, $counter)
    {
        $cloneRow = [];
        $cloneRow['number'] = memory_get_usage();
        $cloneRow['medium_id'] = $row['medium_id'];
        $cloneRow['member_id'] = $row['member_id'];
        $cloneRow['medium_code'] = $row['medium_code'];

        if (isset($row['medium_type']) && $row['medium_type'] === 0) {
            $cloneRow['medium_type'] = 'physical';
        } else {
            $cloneRow['medium_type'] = 'digital';
        }
        if (isset($row['status']) && $row['status'] === 0 && isset($row['valid']) && $row['valid'] == 'true') {
            $cloneRow['active'] = "true";
            $cloneRow['redeem'] = "true";
            $cloneRow['status'] = 'assigned';
        } else {
            $cloneRow['active'] = "false";
            $cloneRow['redeem'] = "false";
            $cloneRow['status'] = 'blocked';
        }

        $this->arrResult[] = $cloneRow;
    }
}
