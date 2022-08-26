/**
 * Прогресс бар
 */

class ProgressBar {
	progressElement;

	maxProgressValue;

	startFragment;

	lengthFragment;

	message;

	isStart;

	constructor(element, max, message) {
		this.init(element, max, message);
	}

	/**
	 * 
	 * @param {HTMLProgressElement} element 
	 * @param {Number} max 
	 * @param {CallableFunction} message 
	 */
	init(element, max, message) {
		this.progressElement = element;
		this.progressElement.value = 0;

		this.maxProgressValue = max;

		this.startFragment = 0;
		this.lengthFragment = 0;

		this.message = message;
		this.message(false);

		this.isStart = false;
	}

	/**
	 *
	 * @param {Number} value число переданных байт, если typeProgress = true,
	 *  и часть переданного фрагмента (от 0 до 1), если typeProgress = false.
	 * @param {Boolean} typeProgress тип передаваемого параметра value
	 */
	showProgressBar(value, typeProgress = true) {
		if (!this.isStart) {
			this.message(0);
			this.progressElement.hidden = false;
			this.progressElement.value = 0;
			this.progressElement.max = this.maxProgressValue;
			this.isStart = true;
		}

		let currentProgressValue = typeProgress
			? value
			: value * this.lengthFragment;

		 this.message(
			Math.round(
				((this.startFragment + currentProgressValue) * 100.0) /
					this.maxProgressValue
			)
		);

		this.progressElement.value = this.startFragment + currentProgressValue;

		if (currentProgressValue == this.maxProgressValue) {
			this.isStart = false;
		}
	}

	hideProgressBar() {
		this.progressElement.hidden = true;
		this.message(false);
	}
}

// export { ProgressBar };
