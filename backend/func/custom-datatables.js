$.fn.dataTable.ext.type.order['file-size-pre'] = function(data) {
    var units = {
        'KB': 1024,
        'MB': 1024 * 1024,
        'GB': 1024 * 1024 * 1024,
        'TB': 1024 * 1024 * 1024 * 1024
    };

    var parts = data.split(' ');
    var size = parseFloat(parts[0]);
    var unit = parts[1];

    return size * units[unit];
};

$.fn.dataTable.ext.type.order['file-size-asc'] = function(a, b) {
    return a - b;
};

$.fn.dataTable.ext.type.order['file-size-desc'] = function(a, b) {
    return b - a;
};

// Custom sorting function for date in the format "9 October 2023 <br>9:50:16 AM"
$.fn.dataTable.ext.type.order['custom-date-pre'] = function(data) {

    var months = {
        'January': 0, 'February': 1, 'March': 2, 'April': 3, 'May': 4, 'June': 5,
        'July': 6, 'August': 7, 'September': 8, 'October': 9, 'November': 10, 'December': 11
    };

    if (data.includes('<br>')) {
        // Format "9 October 2023 <br>9:50:16 AM"
        var dateParts = data.split('<br>');
        var datePart = dateParts[0].trim();
        var timePart = dateParts[1].trim();

        // Parse date
        var dateElements = datePart.split(' ');
        var day = parseInt(dateElements[0]);
        var month = months[dateElements[1]];
        var year = parseInt(dateElements[2]);

        // Parse time
        var timeElements = timePart.split(/:| /);
        var hour = parseInt(timeElements[0]);
        var minute = parseInt(timeElements[1]);
        var second = parseInt(timeElements[2]);
        var period = timeElements[3];

        if (period === 'PM' && hour < 12) {
            hour += 12;
        } else if (period === 'AM' && hour === 12) {
            hour = 0;
        }

        var date = new Date(year, month, day, hour, minute, second);
        return date.getTime();
    } else {
        // Format "5 October 2023"
        var dateElements = data.split(' ');
        var day = parseInt(dateElements[0]);
        var month = months[dateElements[1]];
        var year = parseInt(dateElements[2]);

        var date = new Date(year, month, day);
        return date.getTime();
    }
};

$.fn.dataTable.ext.type.order['custom-date-asc'] = function(a, b) {
    return a - b;
};

$.fn.dataTable.ext.type.order['custom-date-desc'] = function(a, b) {
    return b - a;
};