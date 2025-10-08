document.querySelectorAll('.inputdatemask').forEach(function (input) {
  input.addEventListener('input', function (e) {
    var value = this.value.replace(/\D/g, '').substr(0, 8); // Keep only digits, max length 8
    var day = '';
    var month = '';
    var year = '';

    // Extract and validate day part
    if (value.length > 0) {
      day = value.substr(0, 2);
      if (parseInt(day) > 31) day = '31';
    }

    // Extract and validate month part
    if (value.length > 2) {
      month = value.substr(2, 2);
      if (parseInt(month) > 12) month = '12';
    }

    // Extract year part, but only validate when complete
    if (value.length > 4) {
      year = value.substr(4);
      var currentYear = new Date().getFullYear();
      var minYear = currentYear - 100;
      if (year.length === 4) { // Only validate when year is fully entered
        if (parseInt(year) > currentYear) year = currentYear.toString();
        if (parseInt(year) < minYear) year = minYear.toString();
      }
    }

    // Build formatted value
    var formattedValue = day;
    if (month) formattedValue += '/' + month;
    if (year) formattedValue += '/' + year;

    // Set formatted value
    this.value = formattedValue;
  });
});
