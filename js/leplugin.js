/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 * @copyright Les Coders
 */
var LePlugin = LePlugin || {};
LePlugin.dialogConfirm = function (opts) {
    var options = {
        title: 'Confirm Dialog',
        message: '',
        dialogClass: 'leplugin-dialog-no-close',
        modal: true,
        buttons: {
            Continue: function () {
                options.callbackResult(true);
                jQuery(this).dialog('close');
            },
            Cancel: function () {
                options.callbackResult(false);
                jQuery(this).dialog('close');
            }
        },
        callbackResult: function () {
            alert("No callback defined in LePlugin.dialogConfirm()!");
        }
    };
    jQuery.extend(true, options, opts);
    var newDiv = jQuery(document.createElement('div'));
    newDiv.html(options.message);
    newDiv.dialog(options);
};
LePlugin.utcDateToLocalDate = function (isoDate) {
    //01/15/2000
    var utcDate = new Date(isoDate);
    utcDate.setMinutes(utcDate.getMinutes() - utcDate.getTimezoneOffset());
    return utcDate;
};
LePlugin.utcTimeToLocalDate = function (utc_sec) {
    //01/15/2000
    var utcDate = new Date(utc_sec*1000);
    utcDate.setMinutes(utcDate.getMinutes() - utcDate.getTimezoneOffset());
    return utcDate;
};
LePlugin.Google = LePlugin.Google || {};
LePlugin.Google.Maps = LePlugin.Google.Maps || {};
LePlugin.Google.Maps.getFirstLocation = function (addressString, resultCallback) {
    var url = 'http://maps.googleapis.com/maps/api/geocode/json';
    var data = {
        address: addressString,
        sensor: false
    };
    jQuery.getJSON(url, data, function (response) {
        if (response && response.hasOwnProperty('results')
                && response.results.length > 0
                && response.results[0].hasOwnProperty('geometry')
                && response.results[0].geometry.hasOwnProperty('location')) {
            resultCallback(response.results[0].geometry.location);
        } else {
            resultCallback(false);
        }
    });
};
LePlugin.Google.Maps.getTimezoneFromLocation = function (lat, lng, resultCallback) {
    var url = 'https://maps.googleapis.com/maps/api/timezone/json'
    var data = {
        location: lat + ',' + lng,
        timestamp: ((new Date()).getTime()) / 1000
    };
    jQuery.getJSON(url, data, function (response) {
        if (response && response.hasOwnProperty('status')
                && response.status === 'OK') {
            resultCallback(response);
        } else {
            resultCallback(false);
        }
    });
};

