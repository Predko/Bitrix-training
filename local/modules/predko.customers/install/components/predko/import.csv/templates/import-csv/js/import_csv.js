/**
 *  file scripts.js
 * Created by Visual Studio Code
 * User: Victor Predko
 * predko.victor@gmail.com
 * 15-08-2022
 */

// !!! Эта константа должна быть равна такой же константе из файла
// import.csv\ajax-file.php
const OVERLAP = 6; // Вырезаем фрагмент с таким отступом до начала фрагмента и после.

document.addEventListener('DOMContentLoaded', ready);

let form;

/**********************************************************************
 * Подключает событие submit для формы с id = 'file-csv-form'
 */

function ready() {
	// Access the form element...
	form = document.getElementById('file-csv-form');

	// ...and take over its submit event.
	form.addEventListener('submit', function (event) {
		formSubmit(event);
	});
}

/**********************************************************************
 * Отправляет POST запрос с данными формы на сервер
 *
 * @param event {FormDataEvent}
 */
function formSubmit(event) {
	event.preventDefault();

	let fileSize = document.getElementById('input-file-csv').files[0].size;

	const formData = new FormData(form);

	formData.append('type-form-data', 'initial');
	formData.append('file-size', fileSize);

	// Отправляем данные формы.
	sendData(formData)
		.then((response) => JSON.parse(response))
		// Отправляем файл.
		.then((response) => SendFile(response))
		.catch((error) => Message(error));
}

/**********************************************************************
 * Отправляет данные из formData:FormData в formData.action.
 * Возвращает Promise объект.
 *
 * @param formData {FormData} данные формы
 *
 */
function sendData(formData) {
	return new Promise((resolve, reject) => {
		//ajax
		let HttpRequest = new XMLHttpRequest(); //Создадим объект для отправки AJAX запроса
		HttpRequest.onload = function (e) {
			if (this.status == 200) {
				//Проверка что результат отчета успешный (может быть 404 или другие)
				return resolve(HttpRequest.response); // Успешно.
			} else {
				return reject(new Error(HttpRequest.statusText)); // Ошибка.
			}
		}; //Функция в которую возвращается ответ от сеовера

		HttpRequest.open('POST', form.action, true);
		HttpRequest.send(formData); //Отправка запроса на сервер
	});
}


// Вывод информационных сообщений.
function Message(text) {

	alert(text);
	
}


/**********************************************************************
 * Передаёт на сервер указанный фрагмент файла.
 * @param str передаваемые данные.
 * @param index {number} номер фрагмента.
 * @param size {number} размер фрагмента в байтах.
 * @param overlapBefore {number} перекрытие до основных данных.
 * @param overlapAfter {number} перекрытие после основных данных.
 * @param searchArr {array} массив-образец для поиска начала основных данных.
 */
async function SendPartFile(
	str,
	index,
	size,
	overlapBefore,
	overlapAfter,
	searchArr
) {
	const formData = new FormData(form);

	formData.append('file-data', str);
	formData.append('file-index-part', index);
	formData.append('blob-size', size);
	formData.append('overlapBefore', overlapBefore);
	formData.append('overlapAfter', overlapAfter);
	formData.append('type-form-data', 'get_file');

	formData.append('search_arr', JSON.stringify(searchArr));

	// Отправляем данные формы.
	return sendData(formData)
		.then((response) => JSON.parse(response))
		.catch((error) => Message(error));
}

/**********************************************************************
 * Читает текст из blob
 * Возвращает Promise объект.
 *
 * @param blob {Blob} фрагмент файла
 */
function ReadFromFile(blob) {
	return new Promise((resolve, reject) => {
		let reader = new FileReader();

		reader.readAsText(blob);

		reader.onload = function () {
			return resolve(reader.result);
		};

		reader.onerror = function () {
			return reject(reader.error);
		};
	});
}

// Вырезает из blob массив данных начиная с overlapBefore, 10 байт.
async function getSearchArr(blob, overlapBefore) {
	let blob_sa = blob.slice(overlapBefore, overlapBefore + 10);

	return await new Response(blob_sa).arrayBuffer();
}

/**********************************************************************
 *
 *   Пофрагментная передача файла.
 *
 *   1. Отправляются данные формы о полях.
 *
 *   Разбивает файл на фрагменты размером max_size и отправляет их на сервер.
 * 	 Схема фрагмента:
 *   {[overlapBefore] [[массив-образец 1] остальная часть фрагмента] [overlapAfter]}
 *
 *   {[ob] [[м-о 0] очф] [oa]}
 * 				   {[ob] [[м-о 1] очф] [oa]}
 * 							     {[ob] [[м-о 2] очф] [oa]}
 *
 */
async function SendFile(response) {
	if (response.result == 'error') {
		Message(response.message);
		return;
	}

	let file = document.getElementById('input-file-csv').files[0];

	if (file == null || file.type != 'text/csv') {
		Message('Need a text/csv file!');

		return;
	}

	var max_size = 524288; // 512 kb

	let currentBlob = {
		size: 0,
		rest_blob: file.size,
		begin: 0,
		index: 0,
	};

	var overlapBefore = 0;
	var overlapAfter = OVERLAP;

	// Разбиваем данные на фрагменты размером max_size
	do {
		// Определяем размер части файла для вырезания.
		if (currentBlob.rest_blob <= max_size - overlapBefore) {
			// Остаток файла не требует перекрытия в конце.
			currentBlob.size = currentBlob.rest_blob + overlapBefore;
			overlapAfter = 0;
		} else {
			currentBlob.size = max_size;
		}

		// Вырезаем фрагмент для отправки.
		let blob = file.slice(
			currentBlob.begin,
			currentBlob.begin + currentBlob.size
		);

		// Индекс и размер фрагмента
		let partIndex = currentBlob.index;
		let size = blob.size;

		// Читаем фрагмент из файла.
		resultStr = await ReadFromFile(blob)
			.then((result) => result) // передаём.
			.catch((error) => Message(error));

		// Вырезаем массив-образец, для поиска начала данных.
		// {[overlapBefore] [[массив-образец] остальная часть фрагмента] [overlapAfter]}
		let searchArr = new Uint8Array(await getSearchArr(blob, overlapBefore));

		// Передаём данные.
		var res = await SendPartFile(
			resultStr,
			partIndex,
			size,
			overlapBefore,
			overlapAfter,
			searchArr
		);

		if (res.result == 'end') 
			Message('Данные переданы успешно');

		currentBlob.index++;

		currentBlob.rest_blob -=
			currentBlob.size - overlapBefore - overlapAfter;
		overlapBefore = OVERLAP;
		currentBlob.begin += currentBlob.size - overlapBefore - overlapAfter;
	} while (currentBlob.rest_blob > 0);
}

/**********************************************************************
 *   Читает начало указанного файла CSV(input.files[0])
 *   и создаёт набор полей <select> для выбора соответствия полей
 *   БД и из файла CSV.
 *   Добавляет набор в форму.
 */
function _getFieldNamesFromFile(input, items, not_used_message) {
	let file = input.files[0];

	if (file.type != 'text/csv') {
		Message('Need a text/csv file!');

		return;
	}

	var blob = file.slice(0, 500);

	ReadFromFile(blob)
		.then((result) => createFieldName(result, items))
		.catch((error) => Message(error));

	function createFieldName(str, items) {
		// Выделяем первую строку
		var strArr = str.split('\r', 1);
		// Разбиваем на поля
		var fieldNames = strArr[0].split(';');
		// Получаем список полей выбранной сущности.
		var efn = document.getElementById('entity-name-fields-id').value;
		var entityFieldNames = items[efn]['FIELD_NAMES'];

		createListfieldMatches(entityFieldNames, fieldNames);
	}

	// Создаёт список соответствия полей базы данных и файла CSV
	// формирует раззметку из <select> <==> <select>
	function createListfieldMatches(entityFieldNames, fieldNames) {
		// Число полей в списке.
		var length = fieldNames.length;
		if (entityFieldNames.length > length) {
			length = entityFieldNames.length;
		}

		var ul = clearAndGetElement();
		if (ul == null) {
			Message('Не удалось создать список соответствий полей');
			return;
		}

		// Показываем заголовок
		document.getElementById('field_names_header').style.display = 'block';

		for (let i = 0; i < length; i++) {
			// Колонка таблицы БД.
			var used = false;
			var selected = '""';

			let liLast = document.createElement('li');
			var selectStr =
				'<select name="ENTITY_NAME_TABLE[]' +
				i +
				'" required="required">';
			entityFieldNames.forEach((element, indx) => {
				if (typeof indx !== 'undefined' && i == indx) {
					used = true;
					selected = ' selected="selected"';
				} else selected = '';

				selectStr +=
					'<option value="' +
					element +
					'"' +
					selected +
					'>' +
					element +
					'</option>';
			});

			selected = !used ? ' selected="selected"' : '';

			selectStr +=
				'<option value="not used"' +
				selected +
				'>' +
				not_used_message +
				'</option><br />';
			selectStr +=
				'</select>&nbsp;&nbsp;&nbsp;<==>&nbsp;&nbsp;&nbsp;\
            <select name="ENTITY_NAME_CSV[]' +
				i +
				'" required="required">';

			// Колонка из CSV файла.
			used = false;
			fieldNames.forEach((element, indx) => {
				if (typeof indx !== 'undefined' && i == indx) {
					used = true;
					selected = ' selected="selected"';
				} else selected = '';

				selectStr +=
					'<option value="' +
					element +
					'"' +
					selected +
					'>' +
					element +
					'</option><br />';
			});

			selected = !used ? ' selected="selected"' : '';

			selectStr +=
				'<option value="not used"' +
				selected +
				'>' +
				not_used_message +
				'</option><br />';
			selectStr += '</select><br />';

			liLast.innerHTML = selectStr;

			// вставить liLast в конец <ul>
			ul.append(liLast);
		}

		// Делаем кнопку импорта активной
		document.getElementById('import_btn').removeAttribute('disabled');
	}
}

/**********************************************************************
 *   Скрывает заголовок с id='field_names_header'.
 *   Очищает содержимое элемента.
 *   Возвращает элемент с id = 'place_to_select_field_names'.
 */
function clearAndGetElement() {
	// Скрываем заголовок.
	document.getElementById('field_names_header').style.display = 'none';

	// Делаем кнопку импорта неактивной активной
	document.getElementById('import_btn').setAttribute('disabled', true);

	// Очищаем элемент.
	var element = document.getElementById('place_to_select_field_names');
	element.innerHTML = '';
	return element;
}
