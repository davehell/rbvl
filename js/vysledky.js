function zvyrazneniTymu()
{
  $('table td').each(function() {
    if($(this).text() == $('select#druzstva').val()) {
      $(this).addClass('highlight');
    }
    else {
      $(this).removeClass('highlight');
    }
  });
}

