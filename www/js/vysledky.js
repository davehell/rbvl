function setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function zvyrazneniTymu(druzstvo)
{
    if(!druzstvo) {
        druzstvo = $('select#druzstva').val();
    }
    setCookie('druzstvo', druzstvo, 14);

    $('table td').each(function() {
        if($(this).text() == druzstvo) {
            $(this).addClass('warning');
        }
        else {
            $(this).removeClass('warning');
        }
    });
}

window.onload = function () {
    var druzstvo = getCookie('druzstvo');
    if(druzstvo) {
        $('select').val(druzstvo);
        zvyrazneniTymu(druzstvo);
    }
}
