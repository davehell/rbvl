$( document ).ready(function() {
    $("#frm-vysledkyForm-sety_domaci").focus();

    $("#frm-vysledkyForm input.form-control").keypress(function (e) {
      if (e.which == 13) {
        $("input[name='saveAndNext']").click();
        return false;
      }
    });
});