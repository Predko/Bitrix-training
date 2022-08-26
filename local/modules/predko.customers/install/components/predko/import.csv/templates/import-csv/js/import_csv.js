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
 * @param {FormDataEvent} event
 */
function formSubmit(event) {
	event.preventDefault();

	let file = document.getElementById('input-file-csv').files[0];

	const formData = new FormData(form);

	formData.append('type-form-data', 'initial');
	formData.append('file-size', file.size);
	formData.append('file-name', file.name);

	// Отправляем данные формы.
	sendData(formData)
		.then((response) => JSON.parse(response))
		// Отправляем файл.
		.then((response) => SendFile(response))
		.catch((error) => Message(error));
}

// Реализует переключение вкладок.
function showThisTab(event, idTab) {
	var tabs = document.getElementsByClassName('i-csv-tab-content');

	// Убираем старую вкладку и высвечиваем новую.
	for (let tab of tabs) {
		if (tab.id == idTab) {
			tab.style.display = 'block';
		} else {
			tab.style.display = 'none';
		}
	}

	// Меняем активную кнопку.
	for (let btn of document.getElementsByClassName('i-csv-tab-link')) {
		btn.classList.remove('active');
	}

	// Делаем кнопку активной.
	event.target.classList.add('active');
}

// Реализует переключение вкладок.
function enableTab(event, idTab) {
	createEntityTab(event.target.id);
	showThisTab(event, idTab);
}

const ELEMENT_NODE = 1; // nodeType для элемента.

/**
 * Вызывает функцию для данного элемента
 * и, рекурсивно для всех дочерних элементов.
 * @param {HTMLElement} current елемент.
 * @param {CallableFunction} change функция.
 */
function changeElementId(current, change) {
	change(current);

	for (let node of current.childNodes) {
		if (node.nodeType != ELEMENT_NODE) continue;
		arguments.callee(node, change);
	}
}
/**
 * Создаёт вкладку указанной сущности
 * и делает её активной.
 * @param {String} entityName имя сущности.
 * @param {false|Array} fields массив сопоставления полей.
 */
function createEntityTab(entityName, fields = false) {
	let putDataToDbEntitydiv = document.getElementById(
		'put-data-csv-to-db-' + entityName
	);

	// Если такой элемент есть - выходим.
	if (putDataToDbEntitydiv) return true;

	if (!fields) {
		let importCSV = JSON.parse(localStorage.getItem('importCSV'));
		localStorage.removeItem('importCsv');

		if (
			!importCSV ||
			!(entityName in importCSV) ||
			importCSV[entityName].received == false
		) {
			let res = getDataFromServer('{ type: get, get: fields }' + entityName);
			if (res == null || res == '') return false;

			entityFields = JSON.parse(res);
		} else entityFields = importCSV[entityName].fields;
	} else {
		entityFields = fields;
	}

	let putDataToDbdiv = document.getElementById('put-data-csv-to-db');
	putDataToDbEntitydiv = putDataToDbdiv.cloneNode(true);

	// Меняем id элемента и всех дочерних элементов.
	changeElementId(putDataToDbEntitydiv, (element) => {
		let id = element.getAttribute('id');
		if (id != null && id != '') {
			element.setAttribute('id', id + '-' + entityName);
		}
	});

	putDataToDbdiv.parentNode.appendChild(putDataToDbEntitydiv);

	// Приводим в соответствие содержимое блока.
	document.getElementById('field_names_mapping-' + entityName).innerHTML +=
		' ' + entityName;
	let ul = document.getElementById('i_csv_fields_mapping-' + entityName);
	ul.innerHTML = '';

	for (let key in entityFields) {
		if (key == 'not used') continue;

		let li = document.createElement('li');
		li.innerHTML = key + '  <==>  ' + entityFields[key];

		ul.append(li);
	}

	let btnTab = createAndAppendButton(entityName, 'i-csv-tabs-nav');

	if (btnTab == null) return false;

	// Скрываем предыдущую и показываем эту вкладку.
	showThisTab(btnTab, putDataToDbEntitydiv.id);

	return true;
}

/**
 *
 * @param {Number} id кнопки.
 * @param {Number} parentId id элемента, к которому надо присоединить кнопку.
 * @returns {HTMLElement} созданная или найденная кнопка.
 */
function createAndAppendButton(id, parentId) {
	let btn = document.getElementById(id);
	if (btn) return btn;

	btn = document.createElement('button');
	btn.setAttribute('id', id);
	btn.setAttribute('class', 'i-csv-tab-link');
	btn.onclick = enableTab(event, 'put-data-csv-to-db-' + id);
	btn.innerText = id;

	document.getElementById(parentId).appendChild(btn);
}

/**
 * Прогресс бар
 */

let ProgressInfo = {
	showProgressBar: showProgressBar,

	hideProgressBar: hideProgressBar,

	maxProgressValue: 100,

	currentStartFragment: 0,

	message: '',

	isStart: false,
};

function showProgressBar(currentProgressValue) {
	if (!this.isStart) {
		i_csv_progress.hidden = false;
		i_csv_progress_info.textContent = this.message;
		i_csv_progress.max = this.maxProgressValue;
		this.isStart = true;
	}

	i_csv_progress_info.textContent =
		this.message +
		' ' +
		Math.round(
			((this.currentStartFragment + currentProgressValue) * 100.0) /
				this.maxProgressValue
		) +
		'%';

	i_csv_progress.value = this.currentStartFragment + currentProgressValue;

	if (currentProgressValue == this.maxProgressValue) {
		i_csv_progress_info.textContent = 'Sending comleted';
		this.isStart = false;
	}
}

function hideProgressBar() {
	i_csv_progress.hidden = true;
	i_csv_progress_info.textContent = '';
}

/**********************************************************************
 * Отправляет данные из formData:FormData в formData.action.
 * Возвращает Promise объект.
 *
 * @param {FormData} formData данные формы
 *
 */
function sendData(formData) {
	return new Promise((resolve, reject) => {
		//ajax
		let HttpRequest = new XMLHttpRequest();
		HttpRequest.onload = function (e) {
			//Проверка что результат отчета успешный (может быть 404 или другие)
			if (this.status == 200) {
				// Устанавливаем прогрессбар на 100%.
				ProgressInfo.showProgressBar(ProgressInfo.maxProgressValue);

				return resolve(HttpRequest.response); // Успешно.
			} else {
				return reject(new Error(HttpRequest.statusText)); // Ошибка.
			}
		};

		HttpRequest.onprogress = function (event) {
			ProgressInfo.showProgressBar(event.loaded);
		};

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
 * @param {String} str передаваемые данные.
 * @param {Number} index номер фрагмента.
 * @param {Number} size размер фрагмента в байтах.
 * @param {Number} overlapBefore перекрытие до основных данных.
 * @param {Number} overlapAfter перекрытие после основных данных.
 * @param {Array} searchArr массив-образец для поиска начала основных данных.
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

/**
 *
 * @param {String} typeRequest тип запроса
 * @returns {String} строка ответа.
 */
async function getDataFromServer(typeRequest) {
	const formData = new FormData(form);

	formData.append('type-form-data', typeRequest);

	// Отправляем данные формы.
	return await sendData(formData)
		.then((response) => response)
		.catch((error) => Message(error));
}

/**********************************************************************
 * Читает текст из blob
 * Возвращает Promise объект.
 *
 * @param {Blob} blob фрагмент файла
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
 *   Разбивает файл на фрагменты размером maxFragmentSize и отправляет их на сервер.
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
		// Ошибка инициализации.
		// Удаляем из  localStorage информацию о соответствии полей данных.
		localStorage.removeItem('importCSV');
		return;
	}

	if (response.result == 'ok') {
		// Инициализация прошла успешно.
		// Сохраняем в localStorage информацию о соответствии полей данных.
		localStorage.setItem('importCSV', JSON.stringify(response.importCSV));
	}

	let file = document.getElementById('input-file-csv').files[0];

	if (file == null || file.type != 'text/csv') {
		Message('Need a text/csv file!');

		return;
	}

	var maxFragmentSize = 524288; // 512 kb

	let currentBlob = {
		size: 0,
		rest_blob: file.size,
		begin: 0,
		index: 0,
	};

	var overlapBefore = 0;
	var overlapAfter = OVERLAP;

	ProgressInfo.maxProgressValue = file.size;
	ProgressInfo.message = 'Sending file ' + file.name;

	ProgressInfo.showProgressBar(0);

	// Разбиваем данные на фрагменты размером maxFragmentSize
	do {
		// Определяем размер части файла для вырезания.
		if (currentBlob.rest_blob <= maxFragmentSize - overlapBefore) {
			// Остаток файла не требует перекрытия в конце.
			currentBlob.size = currentBlob.rest_blob + overlapBefore;
			overlapAfter = 0;
		} else {
			currentBlob.size = maxFragmentSize;
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

		// Начало текущего фрагмента в файле для прогресс бара.
		ProgressInfo.currentStartFragment = currentBlob.begin;

		// Передаём данные.
		var res = await SendPartFile(
			resultStr,
			partIndex,
			size,
			overlapBefore,
			overlapAfter,
			searchArr
		);

		if (res.result == 'end') {
			Message('Данные переданы успешно');

			// Сохраняем в localStorage информацию о успешно переданном файле.
			let importCSV = JSON.parse(localStorage.getItem('importCSV'));

			importCSV[response.entityName].received = true;

			localStorage.setItem('importCSV', JSON.stringify(importCSV));

			// Создаём вкладку для данной сущности для переноса данных в БД.
			createEntityTab(
				response.entityName,
				importCSV[response.entityName].fields
			);

			break;
		}

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
	ProgressInfo.hideProgressBar();

	let file = input.files[0];

	if (file.type != 'text/csv') {
		Message('Need a text/csv file!');

		return;
	}

	var blob = file.slice(0, 500);

	ReadFromFile(blob)
		.then((result) => createFieldName(result, items))
		.then((fieldsInfo) => createListfieldMapping(fieldsInfo))
		.catch((error) => Message(error));

	function createFieldName(str, items) {
		// Выделяем первую строку
		var strArr = str.split('\r', 1);
		// Разбиваем на поля
		var CsvFieldNames = strArr[0].split(';');
		// Получаем список полей выбранной сущности.
		var efn = document.getElementById('entity-name-fields-id').value;
		var entityFieldNames = items[efn]['FIELD_NAMES'];

		return {
			entityFieldNames: entityFieldNames,
			CsvFieldNames: CsvFieldNames,
		};
	}

	// Создаёт список соответствия полей базы данных и файла CSV
	// формирует раззметку из <select> <==> <select>
	function createListfieldMapping(fieldsInfo) {
		// Минимальное количество полей из двух списков.
		let minFieldsCount = 0;

		// Число полей в списке.
		var length = fieldsInfo.CsvFieldNames.length;
		var lengthEntity = fieldsInfo.entityFieldNames.length;
		if (lengthEntity > length) {
			minFieldsCount = length;
			length = lengthEntity;
		} else {
			minFieldsCount = lengthEntity;
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
			fieldsInfo.entityFieldNames.forEach((element, indx) => {
				if (
					typeof indx !== 'undefined' &&
					i == indx &&
					i < minFieldsCount
				) {
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
			fieldsInfo.CsvFieldNames.forEach((element, indx) => {
				if (
					typeof indx !== 'undefined' &&
					i == indx &&
					i < minFieldsCount
				) {
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
	ProgressInfo.hideProgressBar();

	// Скрываем заголовок.
	document.getElementById('field_names_header').style.display = 'none';

	// Делаем кнопку импорта неактивной активной
	document.getElementById('import_btn').setAttribute('disabled', true);

	// Очищаем элемент.
	var element = document.getElementById('place_to_select_field_names');
	element.innerHTML = '';
	return element;
}
