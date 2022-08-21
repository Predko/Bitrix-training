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

$temp_data_file = $_SERVER['DOCUMENT_ROOT'] . "/upload/tmp/predko_customer_import_csv_formData.tmp";

use Bitrix\Main\Application;

global $APPLICATION;

$importCSV = new ImportCSV($temp_data_file);

// Обрабатываем AJAX запрос и импортируем файл данных.
$importCSV->RequestHandler();

// Отправляем ответ.
echo $importCSV->GetResponse();




// Класс для обработки запросов и импорта файла CSV
// для добавления данных в базу данных.
class ImportCSV
{
    private $hasError = false;
    private $tmpDataFileName;
    private $tmpHeaderFileName;
    private $request;
    private $response;

    public function __construct(String $temp_file)
    {
        $this->tmpDataFileName = $temp_file;

        $this->tmpHeaderFileName = preg_replace("#(\.tmp)$#", "_hdr.tmp", $temp_file);

        $this->request = Application::getInstance()->getContext()->getRequest();

        $this->response = "";
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
    public function RequestHandler(): bool
    {
        if (!$this->CheckSessid())
            return $this->IsError();
        elseif ($this->CheckIsInitialFormData())
            return $this->IsError();
        elseif ($this->CheckIsDataFile())
            return $this->IsError();

        return false;
    }

    /**
     *
     * @return bool;
     * 
     **/
    public function IsError(): bool
    {
        return $this->hasError;
    }


    public function GetResponse()
    {
        return json_encode($this->response);
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
        {
            $this->hasError = true;
            $this->response = [
                'result' => 'error',
                'message' => Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_TMP_FILE_HEADER_SAVE_ERROR')
            ];
            return false;
        }

        return true;
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

            $this->hasError = true;

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

        $this->isDataReady = false;

        $fields = [];
        foreach ($this->request['ENTITY_NAME_TABLE'] as $index => $field)
        {
            $fields[$field] = $this->request['ENTITY_NAME_CSV'][$index];
        }

        // обрезаем файл данных.
        file_put_contents($this->tmpDataFileName, "");

        // Записываем заголовок во временный файл.
        if (!$this->SaveHeader([
            'FILE_SIZE' => $this->request['file-size'],
            'FIELDS' => $fields,
            'CURRENT_FILE_SIZE' => 0,
            'PART_SIZE' => []
        ]))
        {   // Ошибка записи.
            return true;
        }

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

        if (!$file_info = $this->LoadHeader())
        {   // Ошибка чтения.
            return true;
        }

        $data = $this->request['file-data'];
        $beginOffset = $this->request['overlapBefore'];
        $endOffset = $this->request['overlapAfter'];
        $search_arr = json_decode($this->request['search_arr'], true);

        $blob_size = $this->request['blob-size'];
        $lengthData = strlen($data);

        $startIndex = $this->findIndex(substr($data, 0, count($search_arr) + $beginOffset + 5), $search_arr);

        // Записываем данные в файл.
        if (!$length = $this->SaveData(
            $data,
            $startIndex,
            $blob_size - $beginOffset - $endOffset
        ))
        {   // Ошибка записи.
            return true;
        }


        $partSize = intval($this->request['blob-size'])
            - $this->request['overlapBefore']
            - $this->request['overlapAfter'];

        // Рассчитываем размер полученных данных.
        $file_info['CURRENT_FILE_SIZE'] = intval($file_info['CURRENT_FILE_SIZE'])
            + $partSize;

        // размер записанной части файла.(вместе со служебными данными)
        $file_info["PART_SIZE"]["'" . $this->request['file-index-part'] . "'"] = $length;
        $file_info["beginOffset"] = $beginOffset;
        $file_info["endOffset"] = $endOffset;

        // Записываем заголовок во временный файл.
        if (!$this->SaveHeader($file_info))
        {   // Ошибка записи.
            return true;
        }

        if (intval($file_info['CURRENT_FILE_SIZE']) == intval($file_info['FILE_SIZE']))
        {
            // Файл получен полностью и готов к обработке.
            $this->isDataReady = false;

            $this->response = [
                'result' => 'end',
                'message' => Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_DATA_TRANSFERRED_SUCCESSFULLY')
            ];
        }
        else
        {
            $this->response = [
                'result' => 'ok',
                'message' => Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_DATA_RECEIVED')
            ];
        }

        return true;
    }
}
