<?php

/**
 * file ajax-file.php
 * Created by Visual Studio Code
 * User: Victor Predko
 * predko.victor@gmail.com
 * 14-08-2022
 */

//Подключаем ядро 1С Битрикс
require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

// !!! Эта константа должна быть равна такой же константе из файла
// import.csv\templates\import-csv\js\import_csv.js
const OVERLAP = 6; // Вырезаем фрагмент с таким отступом до начала фрагмента и после.

$tmp_data_file = $_SERVER['DOCUMENT_ROOT'] . "/upload/tmp/predko_customer_import_csv_filename.tmp";

const PART_FILE_GETTING_ERROR = 1;
const SESSID_ERROR = 2;
const DATA_RECEIVED = 3;

use Bitrix\Main\Application;

global $APPLICATION;


$session = \Bitrix\Main\Application::getInstance()->getSession();

//$session->remove('IMPORT_CSV_OBJECT'); return;

if ($session->has("IMPORT_CSV_OBJECT"))
{
    $importCSV = unserialize($session["IMPORT_CSV_OBJECT"]);
}
else
{
    $importCSV = new ImportCSV($tmp_data_file);
}


// Обрабатываем AJAX запрос и импортируем файл данных.
$result = $importCSV->RequestHandler();

// Отправляем ответ.
echo $importCSV->GetResponse();

$session = \Bitrix\Main\Application::getInstance()->getSession();

if (
    $result == PART_FILE_GETTING_ERROR
    || $result == SESSID_ERROR
    || $importCSV->GetResult() == "end"
)
{
    $session->remove('IMPORT_CSV_OBJECT');
}
else
{
    $session["IMPORT_CSV_OBJECT"] = serialize($importCSV);
}



// Класс для обработки запросов и импорта файла CSV
// для добавления данных в базу данных.
class ImportCSV
{
    private $hasError = false;
    private $tmpDataFileName = "";
    private $tmpHeaderFileName = "";
    private $tmpFileName;
    private $request;
    private $response = "";
    private $isDataFileReady = false;
    private $fields = [];
    private $fileInfo = [];

    public function __construct(string $tmpFileName)
    {
        $this->tmpFileName = $tmpFileName;

        $this->request = Application::getInstance()->getContext()->getRequest();
    }

    public function __serialize()
    {
        return [
            "hasError" => $this->hasError,
            "tmpFileName" => $this->tmpFileName,
            "tmpDataFileName" => $this->tmpDataFileName,
            "tmpHeaderFileName" => $this->tmpHeaderFileName,
            "isDataFileReady" => $this->isDataFileReady,
            "fields" => $this->fields,
            "fileInfo" => $this->fileInfo,
        ];
    }

    public function __unserialize($saved_data)
    {
        $this->hasError = $saved_data["hasError"];
        $this->tmpFileName = $saved_data["tmpFileName"];
        $this->tmpDataFileName = $saved_data["tmpDataFileName"];
        $this->tmpHeaderFileName = $saved_data["tmpHeaderFileName"];
        $this->isDataFileReady = $saved_data["isDataFileReady"];
        $this->fields = $saved_data["fields"];
        $this->fileInfo = $saved_data["fileInfo"];

        $this->request = Application::getInstance()->getContext()->getRequest();
    }

    /**
     *
     * @return bool 
     * true - ok, 
     * false - error,
     * if there was an error - (isError() == true) 
     * and $this->response['result'] == 'error'
     * $this->response['message'] - message; 
     *  
     **/
    public function RequestHandler(): bool|int
    {
        $result = false;

        if (!$this->CheckSessid())
            $result =  $this->IsError();
        elseif ($this->CheckIsInitialFormData())
            $result =  $this->IsError();
        elseif ($this->CheckIsDataFile())
            $result =  $this->IsError();
        else
        {
            $this->response = [
                'result' => 'error', // not_processed
                'message' => Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_NOT_PROCESSED')
            ];
        }

        return $result;
    }

    /**
     *
     * @return bool;
     * 
     **/
    public function IsError(): bool|int
    {
        return $this->hasError;
    }


    public function GetResponse()
    {
        return json_encode($this->response);
    }

    public function GetResult()
    {
        return $this->response["result"];
    }

    /**
     *
     * @param array  массив данных
     * @return array|false 
     * false - ошибка записи заголовка.
     * true - данные записаны. 
     **/
    private function SaveHeader(array $header): bool
    {
        // Записываем заголовок во временный файл.([длина заголовка][json заголовок])
        if (!file_put_contents(
            $this->tmpHeaderFileName,
            json_encode($header),
            true
        ))
            return false;

        return true;
    }

    private function SetFileNames(string $fileName)
    {
        $lowerFileName = str_replace(".csv", "", strtolower($fileName));

        $this->tmpDataFileName = str_replace(
            "filename",
            $lowerFileName,
            $this->tmpFileName
        );

        $this->tmpHeaderFileName = str_replace(
            "filename",
            $lowerFileName,
            preg_replace("#(\.tmp)$#", "_hdr.tmp", $this->tmpFileName)
        );

        file_put_contents("d:/error.php", $this->tmpFileName . " " . $this->tmpDataFileName . " " . $this->tmpHeaderFileName, FILE_APPEND);
    }

    /**
     * {[beginOffset] [[массив-образец 1] остальная часть фрагмента] [overlapAfter]}
     * 
     * @param  $data {string} фрагмент данных со служебными вставками до и после.
     * @param  $beginOffset {int} смещение данных от начала $data.
     * @param  $length {int} длина фрагмента данных.
     * @return int|false 
     * false - ошибка записи заголовка.
     * length(bytes) - число записанных байт. 
     **/
    private function SaveData($data, $beginOffset, $length): int|bool
    {
        $buffer = substr($data, $beginOffset, $length);

        $file = fopen($this->tmpDataFileName, "ab");

        $resultLength = fwrite($file, $buffer);

        fclose($file);

        if (!$resultLength)
        {
            $this->hasError = true;
            $this->response = [
                'result' => 'error',
                'message' => Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_TMP_FILE_DATA_SAVE_ERROR')
            ];
            return false;
        }

        return $resultLength;
    }

    /**
     *
     * @return array|false 
     * false - ошибка чтения заголовка.
     * Иначе - данные заголовка(массив) 
     **/
    private function LoadHeader(): array|false
    {
        $result = file_get_contents($this->tmpHeaderFileName);
        if (!$result)
        {
            $this->hasError = true;

            $this->response = [
                'result' => 'error',
                'message' => Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_TMP_FILE_HEADER_LOAD_ERROR')
            ];

            return false;
        }

        $header = json_decode($result, null, 512, JSON_OBJECT_AS_ARRAY);

        return $header;
    }


    /**
     * @return bool 
     * true - sessid - ok, 
     * false - sessid - wrong($this->response[] - error message)
     *  
     **/
    public function CheckSessid(): bool
    {
        if (!check_bitrix_sessid())       // проверка идентификатора сессии
        {
            $this->response = [
                'result' => 'error',
                'message' => Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_SESS_ID_ERROR')
            ];

            $this->hasError = SESSID_ERROR;

            return false;
        }

        return true;
    }

    /**
     *
     * @return bool 
     * true - processed, 
     * false - no,
     * if there was an error - (isError() == true) 
     * and $this->response['result'] == 'error'
     * $this->response['message'] - message; 
     *  
     **/
    public function CheckIsInitialFormData(): bool
    {
        if ($this->request['type-form-data'] != 'initial')
            return false;

        // Это исходные данные для импорта.
        $this->isDataFileReady = false;

        $fields = [];
        foreach ($this->request['ENTITY_NAME_TABLE'] as $index => $field)
        {
            $fields[$field] = $this->request['ENTITY_NAME_CSV'][$index];
        }

        $this->SetFileNames($this->request['file-name']);

        // Обрезаем временный файл данных.
        file_put_contents($this->tmpDataFileName, "");

        $this->SaveHeader([$this->request['ENTITY_NAME'] => $fields]);

        // Информация о файле и полях.
        $this->fields = $fields;
        $this->fileInfo = [
            'FILE_NAME' => $this->request['file-name'],
            'FILE_SIZE' => $this->request['file-size'],
            'CURRENT_FILE_SIZE' => 0,
            'PART_SIZE' => []
        ];

        $this->hasError = false; // Ошибки не было.

        $this->response = [
            'result' => 'ok',
            'message' => Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_DATA_RECEIVED')
        ];

        return true; // обработано.
    }

    /**
     * Ищет подмассив в массиве, полученном из строки.
     * @param $str {string} строка для перобразования в массив байт, в котором ищем.
     * @param $searchArr {array} искомый массив.
     * @return int индекс начала найденного подмассива,
     *          или -1, если не найден.
     */

    private function findIndex(string $str, array $searchArr): int
    {
        $arr = array_map("ord", str_split($str));

        $end = count($arr);
        $endSearchArr = count($searchArr);
        for ($i = 0; $i < count($arr); $i++)
        {
            if ($arr[$i] != $searchArr[0])
                continue;

            $j = 0;
            $startIndex = $i;
            for ($k = $i; $j < $endSearchArr && $k < $end; $k++, $j++)
            {
                if ($arr[$k] != $searchArr[$j])
                    break;
            }

            if ($j == $endSearchArr)
            {
                return $startIndex;
            }
        }

        return -1;
    }

    /**
     *
     * @return bool 
     * true - processed, 
     * false - no,
     * if there was an error - (isError() == true) 
     * and $this->response['result'] == 'error'
     * $this->response['message'] - message; 
     *  
     **/

    public function CheckIsDataFile(): bool
    {
        if ($this->request['type-form-data'] != 'get_file')
            return false;

        $data = $this->request['file-data'];
        $beginOffset = $this->request['overlapBefore'];
        $endOffset = $this->request['overlapAfter'];
        $search_arr = json_decode($this->request['search_arr'], true);

        $blob_size = $this->request['blob-size'];

        $startIndex = $this->findIndex(substr($data, 0, count($search_arr) + $beginOffset + 5), $search_arr);

        // Записываем данные в файл.
        if (!$length = $this->SaveData(
            $data,
            $startIndex,
            $blob_size - $beginOffset - $endOffset
        ))
        {   // Ошибка записи.
            // Файл получен  не полностью.
            $this->isDataFileReady = false;

            $this->hasError = PART_FILE_GETTING_ERROR;

            $this->response = [
                'result' => 'error',
                'message' => Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_DATA_TRANSFERRED_SUCCESSFULLY')
            ];

            return true;
        }

        $partSize = $this->request['blob-size']
            - $this->request['overlapBefore']
            - $this->request['overlapAfter'];

        // Рассчитываем размер полученных данных.
        $this->fileInfo['CURRENT_FILE_SIZE'] = $this->fileInfo['CURRENT_FILE_SIZE']
            + $partSize;

        // размер записанной части файла.(вместе со служебными данными)
        $this->fileInfo["PART_SIZE"]["'" . $this->request['file-index-part'] . "'"] = $length;
        $this->fileInfo["BEGIN_OFFSET"] = $beginOffset;
        $this->fileInfo["END_OFFSET"] = $endOffset;

        if ($this->fileInfo['CURRENT_FILE_SIZE'] == $this->fileInfo['FILE_SIZE'])
        {
            // Файл получен полностью и готов к обработке.
            $this->isDataFileReady = false;

            $this->hasError = false;

            $this->response = [
                'result' => 'end',
                'message' => Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_DATA_TRANSFERRED_SUCCESSFULLY')
            ];
        }
        else
        {
            $this->hasError = false;

            $this->response = [
                'result' => 'ok',
                'message' => Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_DATA_RECEIVED')
            ];
        }

        return true;
    }
}
