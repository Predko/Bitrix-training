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

$temp_data_file = $_SERVER['DOCUMENT_ROOT'] . "/upload/tmp/predko_customer_import_csv_formData.tmp";

use Bitrix\Main\{
    Application,
    // Context, 
    // Request, 
    // Server
};
use Sale\Handlers\Delivery\Rest\RequestHandler;

global $APPLICATION;

$importCSV = new ImportCSV($temp_data_file);

$importCSV->RequestHandler();

echo $importCSV->GetResponse();


class ImportCSV
{
    private $hasError = false;
    private $tmpDataFileName;
    private $tmpHeaderFileName;
    private $request;
    private $response;

    private $isDataReady = false;

    private const PART_DELIMITER = '|';

    public function __construct(String $temp_file)
    {
        $this->tmpDataFileName = $temp_file;

        $this->tmpHeaderFileName = $temp_file . "_hdr";

        $this->request = Application::getInstance()->getContext()->getRequest();

        $this->response = "";
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
     *
     * @param  $data данные
     * @return int|false 
     * false - ошибка записи заголовка.
     * length(bytes) - число записанных байт. 
     **/
    private function SaveData($data): int|bool
    {
        $file = fopen($this->tmpDataFileName,"ab");
        $length = fwrite($file, $data);
        fwrite($file, "\n\n\n");
        fclose($file);

        if (!$length)
        {
            $this->hasError = true;
            $this->response = [
                'result' => 'error',
                'message' => Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_TMP_FILE_DATA_SAVE_ERROR')
            ];
            return false;
        }

        return $length;
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
        file_put_contents("d:/error.php", "\n" . print_r($header, true), FILE_APPEND);

        return $header;
    }


    /**
     *
     *
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

        file_put_contents("d:/error.php", "\n" . print_r($this->request['sting-array-size,true']), FILE_APPEND);


        $this->hasError = false; // Ошибки не было.

        $this->response = [
            'result' => 'ok',
            'message' => Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_DATA_RECEIVED')
        ];

        return true; // обработано.
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
        
        file_put_contents("d:/error".$this->request['file-index-part'].".tm1", print_r($this->request['file-data'], true), FILE_APPEND);
        
        $file = fopen("d:/part_".$this->request['file-index-part'].".tm2","w");
        $length = fwrite($file, $this->request['file-data']);
        fclose($file);

        if (!$file_info = $this->LoadHeader())
        {   // Ошибка чтения.
            return true;
        }

        $blobSize = intval($this->request['blob-size']);

        // Рассчитываем размер полученных данных.
        $file_info['CURRENT_FILE_SIZE'] = intval($file_info['CURRENT_FILE_SIZE'])
            + $blobSize;


        //$str = substr($this->request['file-data'], 0, 4);
        $byteArr = unpack("H*", $this->request['file-data']);
        
        file_put_contents("d:/error.php", "\n\n\nbyteArr = " . print_r($byteArr, true), FILE_APPEND);

        // for ($i = 0; $i < 4; $i++)
        // {
        //     if (intval($str[$i]) & 0xC000)
        // }




        // Записываем данные во временный файл.
        if (!$length = $this->SaveData($this->request['file-data']))
        {   // Ошибка записи.
            return true;
        }

        // размер записанной части файла.(вместе со служебными данными)
        $file_info["PART_SIZE"]["'" . $this->request['file-index-part'] . "'"] = $length;

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

        // Проверяем, были ли сохранены поля БД и файла CSV
        //     if (!$session->has('FIELDS_DB_CSV'))
        //     {
        //         echo json_encode(
        //             [
        //                 'result' => 'error',
        //                 'message' => Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_NO_FIELDS_ERROR')
        //             ]
        //         );
        //         return false;
        //     }

        // $fields = $session['FIELDS_DB_CSV'];

        // foreach ($fields as $db => $csv)
        // {
        // }

        return false;
    }
}
