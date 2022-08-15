"use strict";
/**
* file submit.ts
* Created by Visual Studio Code
* User: Victor Predko
* predko.victor@gmail.com
* 11-08-2022
* Отправка запроса формы и обработка ответа сервера
*/
let form;
function ready() {
    // Access the form element...
    form = document.getElementById('the-add-form');
    // form.addEventListener("blur", (event) => LostFocus(event), true);
    // ...and take over its submit event.
    form.addEventListener("submit", function (event) {
        submitForm()
            .then(data => ChangeContent(data))
            .catch(error => console.log(error));
        event.preventDefault();
    });
}
document.addEventListener("DOMContentLoaded", ready);
function submitForm() {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        // Define what happens on successful data submission
        xhr.onload = () => {
            if (xhr.readyState === xhr.DONE) {
                if (xhr.status < 400) {
                    return resolve(xhr.responseText);
                }
                else {
                    return reject(new Error(xhr.statusText));
                }
            }
        };
        // Define what happens in case of error
        xhr.onerror = () => {
            return reject(new Error('Oops! Something went wrong.'));
        };
        xhr.open('POST', form.action);
        // Bind the FormData object and the form element
        let FD = new FormData(form);
        FD.append("JSrequest", "yes");
        // Set up our request
        // The data sent is what the user provided in the form
        xhr.send(FD);
    });
}
function ChangeContent(text) {
    if (text.trim() == "NoChanged") {
        // Изменения не требуются.
        return;
    }
    let tableContainer = document.getElementById("table-container");
    if (tableContainer != null) {
        tableContainer.innerHTML = text;
    }
    else {
        console.log("Нет контейнера для таблицы.");
    }
}
function LostFocus(event) {
    const field = event.target;
    if (field.tagName.toUpperCase() === 'INPUT') {
        const input = field;
        // Ограничиваем длину введённой строки 10-ю числами.
        const value = input.value.trim().substr(0, 10);
        if (input.name === 'min' || input.name === 'max' || input.name === 'amount') {
            let min = parseFloat(input.min);
            let max = parseFloat(input.max);
            console.log("\nmin = " + min +
                "\nmax = " + max +
                "\nvalue = " + value +
                "\nname = " + input.name);
            if (!value || value === null || value.trim().length == 0) {
                switch (input.name) {
                    case 'min':
                        input.value = input.min;
                        break;
                    case 'max':
                        input.value = input.max;
                        break;
                    case 'amount':
                        input.value = "20";
                        break;
                }
            }
            else if (parseFloat(value) < min) {
                input.value = input.min;
            }
            else if (parseFloat(value) > max) {
                input.value = input.max;
            }
        }
    }
}
