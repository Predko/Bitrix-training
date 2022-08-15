/**
*  file scripts.js
* Created by Visual Studio Code
* User: Victor Predko
* predko.victor@gmail.com
* 15-08-2022
*/


function _getFieldNamesFromFile(input, items, not_used_message) 
{
    let file = input.files[0];

    if (file.type != "text/csv")
    {
        alert("Need a text/csv file!");
        
        return;
    };
    
    var blob = file.slice(0, 500);
    
    let reader = new FileReader();

    reader.readAsText(blob);

    reader.onload = function() {
        createFieldName(reader.result, items);
    };

    reader.onerror = function() {
        console.log(reader.error);
    };

    function createFieldName(str, items) {
        var str = reader.result;
        // Выделяем первую строку
        var strArr = str.split("\r",1);
        // Разбиваем на поля
        var fieldNames = strArr[0].split(";");
        // Получаем список полей выбранной сущности.
        var efn = document.getElementById("entity-name-fields-id").value;
        var entityFieldNames = items[efn]["FIELD_NAMES"];
        
        createListfieldMatches(entityFieldNames, fieldNames);
    }

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
            var used = false;
            var selected = '""';

            let liLast = document.createElement('li');
            var selectStr = '<select name="ENTITY_NAME_TABLE" required="required">';
            entityFieldNames.forEach((element,indx) => {
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
            <select name="ENTITY_NAME_CSV" required="required">';
            used = false;
            fieldNames.forEach((element,indx) => {
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

            ul.append(liLast); // вставить liLast в конец <ul>
        }
    }
}

// Скрывает заголовок.
// Очищает содержимое элемента.
// Возвращает найденный элемент.
function clearAndGetElement()
{
    // Скрываем заголовок.
    document.getElementById('field_names_header').style.display = "none";
    
    // Очищаем элемент.
    var element = document.getElementById('place_to_select_field_names')
    element.innerHTML = "";
    return element;
}


