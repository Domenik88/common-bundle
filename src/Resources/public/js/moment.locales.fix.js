if(typeof moment === 'function')
{
	var monthsShortCaseReplace = function(m, format) {
			var monthsShort = {
					'nominative': 'янв._февр._март_апр._май_июнь_июль_авг._сент._окт._нояб._дек.'.split('_'),
					'accusative': 'янв._февр._марта_апр._мая_июня_июля_авг._сент._окт._нояб._дек.'.split('_')
				},
				nounCase = (/D[oD]?(\[[^\[\]]*\]|\s+)+MMMM?/).test(format) ?
					'accusative' :
					'nominative'
			;
			return monthsShort[nounCase][m.month()];
		},
		oldLocale = moment.locale()
	;

	moment.defineLocale('ru', {
		monthsShort : monthsShortCaseReplace,
		meridiemParse : /[ap]\.?m?\.?/i,
		meridiem : function (hours, minutes, isLower) {
			if (hours > 11) {
				return isLower ? 'pm' : 'PM';
			} else {
				return isLower ? 'am' : 'AM';
			}
		},
		isPM : function (input) {
			return ((input + '').toLowerCase().charAt(0) === 'p');
		},
	});
	moment.locale(oldLocale);
}
