/**
 *  file scripts.js
 * Created by Visual Studio Code
 * User: Victor Predko
 * predko.victor@gmail.com
 * 15-08-2022
 */

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
		.then((response) => SendFile(JSON.parse(response)))
		.catch((error) => alert(error));
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
		var HttpRequest = new XMLHttpRequest(); //Создадим объект для отправки AJAX запроса
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

/**********************************************************************
 * Передаёт на сервер указанный фрагмент файла
 * @param blob передаваемые данные
 * @param index {number} номер фрагмента
 * @param size {number} размер фрагмента в байтах
 */
function SendPartFile(blob, index, size) {
	var strarr = blob;

	console.log(index, size);

	const formData = new FormData(form);

	formData.append('file-data', strarr);
	formData.append('blob-size', size);
	formData.append('type-form-data', 'get_file');
	formData.append('file-index-part', index);

	// Отправляем данные формы.
	sendData(formData)
		.then((response) => {
			if (JSON.parse(response).result == 'end')
				alert('Данные переданы успешно');
		})
		.catch((error) => alert(error));
}

/**********************************************************************
 * Читает текст из blob
 * Возвращает Promise объект.
 *
 * @param blob {Blob} фрагмент файла
 */
function ReadFromFile(blob) {
	return new Promise((resolve, reject) => {
		var reader = new FileReader();

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
 *   Разбивает файл на фрагменты и отправляет их на сервер.
 *   Передача ассинхронна, фрагменты могут прийти в произвольном порядке.
 *
 */
function SendFile(response) {
	if (response.result == 'error') {
		alert(response.message);
		return;
	}

	let file = document.getElementById('input-file-csv').files[0];

	if (file == null || file.type != 'text/csv') {
		alert('Need a text/csv file!');

		return;
	}

	const MAX_SIZE = 524288; // 512 kb

	var currentBlob = {
		size: MAX_SIZE,
		rest_blob: file.size,
		begin: 0,
		index: 0,
	};

	// Разбиваем данные на фрагменты размером MAX_SIZE и передаём серверу.
	do {
		currentBlob.size =
			currentBlob.rest_blob < MAX_SIZE ? currentBlob.rest_blob : MAX_SIZE;
		console.log(currentBlob);
		var blob = file.slice(
			currentBlob.begin,
			currentBlob.begin + currentBlob.size
		);
		console.log(blob.size);

		let i = currentBlob.index;
		let size = blob.size;

		// Читаем фрагмент из файла.
		ReadFromFile(blob)
			.then((result) => SendPartFile(result, i, size)) // передаём.
			.catch((error) => alert(error));

		currentBlob.index++;
		currentBlob.rest_blob -= currentBlob.size;
		currentBlob.begin += currentBlob.size;
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
		alert('Need a text/csv file!');

		return;
	}

	var blob = file.slice(0, 500);

	ReadFromFile(blob)
		.then((result) => createFieldName(result, items))
		.catch((error) => alert(error));

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
			alert('Не удалось создать список соответствий полей');
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
