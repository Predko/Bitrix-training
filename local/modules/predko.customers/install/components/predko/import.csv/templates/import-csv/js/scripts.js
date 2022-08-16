/**
 *  file scripts.js
 * Created by Visual Studio Code
 * User: Victor Predko
 * predko.victor@gmail.com
 * 15-08-2022
 */

document.addEventListener("DOMContentLoaded", ready);

let form;

/**********************************************************************
 * Подключает событие submit для формы с id = 'file-csv-form'
 */

function ready() {
    // Access the form element...
    form = document.getElementById('file-csv-form');
    
    // ...and take over its submit event.
    form.addEventListener("submit", function (event) {
        formSubmit(event);
    });
}

/**********************************************************************
 * Отправляет POST запрос с данными формы на сервер
*/
function formSubmit(event) {
    
    event.preventDefault();

    // Отправляем данные формы.
    sendData(new FormData(form))
    .then(response => SendFile(response))
    .catch(error => alert(error));

    alert("Success!");
}

/**********************************************************************
 * Отправляет данные из formData:FormData в action.
 * Возвращает Promise объект.
 */
function sendData(formData) {

    return new Promise((resolve, reject) => {
        //ajax
        var HttpRequest = new XMLHttpRequest(); //Создадим объект для отправки AJAX запроса
        HttpRequest.onload = function (e) {
            if (this.status == 200) { //Проверка что результат отчета успешный (может быть 404 или другие)
                return resolve(HttpRequest.responseText);   // Успешно.
            }
            else {
                return reject(new Error(HttpRequest.statusText));    // Ошибка.
            }
        }; //Функция в которую возвращается ответ от сеовера

        // Define what happens in case of error
        HttpRequest.onerror = () =>
        {
            return reject(new Error('Oops! Something went wrong.'));
        }
        
        HttpRequest.open("POST", form.action, true); //Настройка запроса для отправки (второй параметр путь к PHP скрипту)
        HttpRequest.send(formData); //Отправка запроса на сервер
    });
}

/**********************************************************************
 *  Формирует из blob массив строк.
 * В начало первой строки добавляется rest_str.
 * Если последняя строка неполная(не завершается переносом строки)
 * она удаляется из массива, помещается в rest_str и возращается
 * из функции.
 */
function SendPartFile(rest_str, blob) {
    
    
    
    
    return rest_str;
}

/**********************************************************************
 * Отправляет данные из formData:FormData в action.
 * Возвращает Promise объект.
 */
 function ReadFromFile(reader, blob) {

    return new Promise((resolve, reject) => {
    
            reader.readAsText(blob);
            
            reader.onload = function () {
                return resolve(reader.result);
            };
            
            reader.onerror = function () {
                return reject(reader.error);
            };

    });
}


/**********************************************************************
 *
 *   Передача данных для обработки.
 *
 *   1. Отправляются данные формы о полях.
 *   
 *   2. Из файла выбирается фрагмент.
 *   2.1 Фрагмент разбивается на строки.
 *   2.2 К первой строке в начало добавляется фрагмент последней строки из предыдущей выборки.
 *   2.3 если последняя строка не заканчивается на символ конца строки
 *       она запоминается в качестве фрагмента первой строки.
 *   3. Массив строк передаётся в FormData и отправляется обработчику на сервер.
 *
 *   Сервер добавляет их в базу данных, меняя при необходимости связанные id.
 *   (при первоначальном заполнении если данные из другой базы)
 *
 *   После обработки, сервер запрашивает следующую порцию данных.
 *   4. Если файл ещё обработан не полностью, переходим к пункту 2.
 *
 *   5. Выводим сообщение о завершении передачи данных.
 *
 */
function SendFile(response) {

    let file = document.getElementById('input-file-csv').files[0];

    if (file == null || file.type != "text/csv") {
        alert("Need a text/csv file!");

        return;
    };

    const MAX_SIZE = 524288;    // 512 kb
    
    let reader = new FileReader();

    var currentBlob = {size:MAX_SIZE,
        rest_blob:file.size,
        begin:0, rest_str:""};

    do {
        currentBlob.size = (currentBlob.rest_blob < MAX_SIZE) ? currentBlob.rest_blob : MAX_SIZE;

        var blob = file.slice(currentBlob.begin, currentBlob.size);
        
        ReadFromFile(reader, blob)
        .then(result => currentBlob.rest_str = SendPartFile(currentBlob.rest_str, result))
        .catch(error => alert(error));
        
        if (reader.error != null){
            return;
        }

        if (currentBlob.rest_str == null){    // Ничего не отправлено.
            return;
        }

        currentBlob.rest_blob -= currentBlob.size;
        currentBlob.begin += currentBlob.size;
    }
    while (currentBlob.rest_blob != 0);
    
    
    
    
    alert("Данные переданы успешно");
}
/**********************************************************************
 *   Читает начало указанного файла CSV(input.files[0])
 *   и создаёт набор полей <select> для выбора соответствия полей
 *   БД и из файла CSV.
 *   Добавляет набор в форму.
 */
function _getFieldNamesFromFile(input, items, not_used_message) {
    let file = input.files[0];

    if (file.type != "text/csv") {
        alert("Need a text/csv file!");

        return;
    };

    var blob = file.slice(0, 500);

    let reader = new FileReader();

    ReadFromFile(reader, blob)
    .then(result => createFieldName(result, items))
    .catch(error => alert(error));

    function createFieldName(str, items) 
    {
        var str = reader.result;
        // Выделяем первую строку
        var strArr = str.split("\r", 1);
        // Разбиваем на поля
        var fieldNames = strArr[0].split(";");
        // Получаем список полей выбранной сущности.
        var efn = document.getElementById("entity-name-fields-id").value;
        var entityFieldNames = items[efn]["FIELD_NAMES"];

        createListfieldMatches(entityFieldNames, fieldNames);
    }

    // Создаёт список соответствия полей базы данных и файла CSV
    // формирует раззметку из <select> <==> <select>
    function createListfieldMatches(entityFieldNames, fieldNames) 
    {
        // Число полей в списке.
        var length = fieldNames.length;
        if (entityFieldNames.length > length) 
        {
            length = entityFieldNames.length;
        }

        var ul = clearAndGetElement();
        if (ul == null) 
        {
            alert("Не удалось создать список соответствий полей");
            return;
        }

        // Показываем заголовок
        document.getElementById('field_names_header').style.display = "block";

        for (let i = 0; i < length; i++) 
        {
            // Колонка таблицы БД.
            var used = false;
            var selected = '""';

            let liLast = document.createElement('li');
            var selectStr = '<select name="ENTITY_NAME_TABLE[]' + i + '" required="required">';
            entityFieldNames.forEach((element, indx) => {
                if (typeof indx !== 'undefined' && i == indx) 
                {
                    used = true;
                    selected = ' selected="selected"';
                }
                else
                    selected = '';

                selectStr += '<option value="' + element + '"' + selected + '>' + element + '</option>';
            });

            selected = (!used) ? ' selected="selected"' : '';

            selectStr += '<option value="not used"' + selected + '>' + not_used_message + '</option><br />';
            selectStr += '</select>&nbsp;&nbsp;&nbsp;<==>&nbsp;&nbsp;&nbsp;\
            <select name="ENTITY_NAME_CSV[]' + i + '" required="required">';

            // Колонка из CSV файла.
            used = false;
            fieldNames.forEach((element, indx) => {
                if (typeof indx !== 'undefined' && i == indx) 
                {
                    used = true;
                    selected = ' selected="selected"';
                }
                else
                    selected = '';

                selectStr += '<option value="' + element + '"' + selected + '>' + element + '</option><br />';
            });

            selected = (!used) ? ' selected="selected"' : '';

            selectStr += '<option value="not used"' + selected + '>' + not_used_message + '</option><br />';
            selectStr += '</select><br />';

            liLast.innerHTML = selectStr;

            // вставить liLast в конец <ul>
            ul.append(liLast);
        }

        // Делаем кнопку импорта активной
        document.getElementById("import_btn").removeAttribute("disabled");
    }
}

/**********************************************************************
 *   Скрывает заголовок с id='field_names_header'.
 *   Очищает содержимое элемента.
 *   Возвращает элемент с id = 'place_to_select_field_names'.
 */
function clearAndGetElement() 
{
    // Скрываем заголовок.
    document.getElementById('field_names_header').style.display = "none";

    // Делаем кнопку импорта неактивной активной
    document.getElementById("import_btn").setAttribute("disabled", true);
    
    // Очищаем элемент.
    var element = document.getElementById('place_to_select_field_names')
    element.innerHTML = "";
    return element;
}


