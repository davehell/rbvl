$( document ).ready(function() {
    $("#frmvysledkyForm-sety_domaci").focus();

    $("#frm-vysledkyForm input.form-control").keypress(function (e) {
      if (e.which == 13) {
        $("#frmvysledkyForm-saveAndNext").click();
        return false;
      }
    });
});