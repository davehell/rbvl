function zvyrazneniTymu()
{
  $('table td').each(function() {
    if($(this).text() == $('select#druzstva').val()) {
      $(this).addClass('danger');
    }
    else {
      $(this).removeClass('danger');
    }
  });
}

