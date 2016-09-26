function zvyrazneniTymu()
{
  $('table td').each(function() {
    if($(this).text() == $('select#druzstva').val()) {
      $(this).addClass('warning');
    }
    else {
      $(this).removeClass('warning');
    }
  });
}

